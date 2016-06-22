<?php

namespace DreamCommerce\BugTrackerBundle\Jira;

use DreamCommerce\BugTrackerBundle\Exception\RuntimeException;
use DreamCommerce\BugTrackerBundle\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client
{
    /**
     * @var ClientInterface
     */
    private $_httpClient;

    /**
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->_httpClient = $httpClient;
    }

    /**
     * @return ClientInterface
     */
    public function getHttpClient()
    {
        return $this->_httpClient;
    }

    /**
     * @param ClientInterface $httpClient
     * @return $this
     */
    public function setHttpClient($httpClient)
    {
        $this->_httpClient = $httpClient;
        return $this;
    }

    /**
     * @param Configuration $configuration
     * @param Issue $issue
     * @return OperationResult
     */
    public function persistIssue(Configuration $configuration, Issue $issue)
    {
        $data = array(
            'project' => array(
                'key' => $configuration->getProject(),
            ),
            'summary' => $issue->getSummary(),
            'description' => $issue->getDescription(),
            'assignee' => array(
                'name' => $configuration->getAssignee(),
            ),
            'labels' => $configuration->getLabels()
        );

        if ($configuration->isUseCounter()) {
            $data['customfield_' . $configuration->getCounterFieldId()] = 1;
        }
        $hash = $issue->getHash();
        if ($configuration->isUseHash() && $hash !== null) {
            $data['customfield_' . $configuration->getHashFieldId()] = $hash;
        }

        $priority = null;
        $priorities = $configuration->getPriorities();
        if (isset($priorities[$level])) {
            $priority = $priorities[$level];
        } else {
            $priority = $this->getDefaultPriority();
        }
        if ($priority !== null) {
            $data['priority'] = array(
                'id' => (string)$priority,
            );
        }

        $type = null;
        $types = $this->getTypes();
        if (isset($types[$level])) {
            $type = $types[$level];
        } else {
            $type = $this->getDefaultType();
        }
        if ($type !== null) {
            $data['issuetype'] = array(
                'id' => $type,
            );
        }

        $data = array_merge($data, $configuration->getFields());
        $client = $this->getHttpClient();
        $uri = $configuration->getEntryPoint() . '/rest/api/2/issue';

        $request = $client->createRequest('POST', $uri, array('Content-type' => 'application/json'), json_encode(array('fields' => $data)));
        $response = $client->send($request, $this->_getAuthParams());

        return $this->_handleResponse($request, $response);
    }

    /**
     * @param Configuration $configuration
     * @param string $fieldName
     * @param mixed $fieldValue
     * @return Issue|null
     */
    public function getIssueByField(Configuration $configuration, $fieldName, $fieldValue)
    {
        $client = $this->getHttpClient();
        $uri = $configuration->getEntryPoint() . '/rest/api/2/search?jql=' . $fieldName . ' ~ ' . $fieldValue;
        $request = $client->createRequest('GET', $uri, array('Content-type' => 'application/json'));
        $response = $client->send($request, $this->_getAuthParams());

        $result = $this->_handleResponse($request, $response);
        if ($result['total'] == 0) {
            return;
        }

        return $result['issues'][0];
    }

    /**
     * @param int $issueId
     * @param int $counter
     *
     * @return Ticket
     */
    private function updateCounterForIssue($issueId, $counter)
    {
        $data = array();
        $data['customfield_' . $this->getCounterFieldId()] = $counter;

        $client = $this->getHttpClient();
        $uri = $this->getEntryPoint() . '/rest/api/2/issue/' . $issueId;

        $request = $client->createRequest('PUT', $uri, array('Content-type' => 'application/json'), json_encode(array('fields' => $data)));
        $response = $client->send($request, $this->_getAuthParams());

        return $this->_apiHandleResponse($request, $response);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     *
     * @return Response
     */
    private function _handleResponse(RequestInterface $request, ResponseInterface $response)
    {
        $result = json_decode($response->getBody(), true);
        if ($result === false) {
            throw new RuntimeException('Unable decode response from URL ' . $request->getUri() . '; method: ' . $request->getMethod());
        }

        if (isset($result['errorMessages']) || isset($result['errors'])) {
            $errorMessagesArr = array();
            if (isset($result['errorMessages'])) {
                foreach ($result['errorMessages'] as $field => $error) {
                    if (is_array($error)) {
                        $error = implode('; ', $error);
                    }

                    $errorMessagesArr[] = $field . ' - ' . $error;
                }
            }

            $errorArr = array();
            if (isset($result['errors'])) {
                foreach ($result['errors'] as $field => $error) {
                    if (is_array($error)) {
                        $error = implode('; ', $error);
                    }

                    $errorArr[] = $field . ' - ' . $error;
                }
            }

            throw new RuntimeException('The error occurred while processing data; error messages: "' . implode('; ', $errorMessagesArr) . '"; errors: "' . implode('; ', $errorArr));
        }

        return $result;
    }
}