<?php

namespace DreamCommerce\Component\BugTracker\Connector;

use DreamCommerce\Component\BugTracker\Exception\RuntimeException;
use DreamCommerce\Component\BugTracker\Http\Client\ClientInterface;
use DreamCommerce\Component\BugTracker\Model\Jira\Credentials;
use DreamCommerce\Component\BugTracker\Model\Jira\Issue;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Webmozart\Assert\Assert;

final class JiraConnector implements JiraConnectorInterface
{
    /**
     * @var ClientInterface
     */
    private $_httpClient;

    public function __construct(ClientInterface $httpClient = null)
    {
        $this->_httpClient = $httpClient;
    }

    /**
     * @return ClientInterface
     */
    public function getHttpClient()
    {
        if ($this->_httpClient === null) {
            throw new RuntimeException('HTTP client has been not defined');
        }

        return $this->_httpClient;
    }

    /**
     * @param ClientInterface $httpClient
     *
     * @return $this
     */
    public function setHttpClient(ClientInterface $httpClient)
    {
        $this->_httpClient = $httpClient;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function createIssue(Credentials $credentials, Issue $issue)
    {
        $summary = $this->_repairString($issue->getSummary());
        $description = $this->_repairString($issue->getDescription());

        $data = array(
            'project' => array(
                'key' => $issue->getProject(),
            ),
            'summary' => substr($summary, 0, 255),
            'description' => substr($description, 0, 4000),
            'assignee' => array(
                'name' => $issue->getAssignee(),
            ),
        );

        $labels = $issue->getLabels();
        if (!empty($labels)) {
            $data['labels'] = $labels;
        }

        $fields = $issue->getFields();
        foreach ($fields as $k => $v) {
            $data['customfield_'.$k] = $v;
        }

        $priority = $issue->getPriority();
        if ($priority !== null) {
            $data['priority'] = array(
                'id' => (string) $priority,
            );
        }

        $type = $issue->getType();
        if ($type !== null) {
            $data['issuetype'] = array(
                'id' => $type,
            );
        }

        $uri = $credentials->getEntryPoint().'/rest/api/2/issue';

        $client = $this->getHttpClient();
        $request = $client->createRequest('POST', $uri, array('Content-type' => 'application/json'), json_encode(array('fields' => $data)));
        $response = $client->send($request, $this->_getAuthParams($credentials));

        return $this->_apiHandleResponse($request, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function findIssuesByField(Credentials $credentials, $fieldName, $fieldValue)
    {
        Assert::notEmpty($fieldName);
        Assert::notEmpty($fieldValue);

        $client = $this->getHttpClient();
        $uri = $credentials->getEntryPoint().'/rest/api/2/search?jql='.$fieldName.' ~ '.$fieldValue;
        $request = $client->createRequest('GET', $uri, array('Content-type' => 'application/json'));
        $response = $client->send($request, $this->_getAuthParams($credentials));

        $result = $this->_apiHandleResponse($request, $response);

        return $result['issues'];
    }

    /**
     * {@inheritdoc}
     */
    public function updateIssueFields(Credentials $credentials, $issueId, array $fields = array())
    {
        Assert::integer($issueId);

        $data = array(
            'fields' => $fields,
        );

        $client = $this->getHttpClient();
        $uri = $credentials->getEntryPoint().'/rest/api/2/issue/'.$issueId;

        $request = $client->createRequest('PUT', $uri, array('Content-type' => 'application/json'), json_encode($data));
        $response = $client->send($request, $this->_getAuthParams($credentials));

        return $this->_apiHandleResponse($request, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function getIssueTransitions(Credentials $credentials, $issueId)
    {
        Assert::integer($issueId);

        $client = $this->getHttpClient();
        $uri = $credentials->getEntryPoint().'/rest/api/2/issue/'.$issueId.'/transitions';

        $request = $client->createRequest('GET', $uri, array('Content-type' => 'application/json'));
        $response = $client->send($request, $this->_getAuthParams($credentials));

        return $this->_apiHandleResponse($request, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function updateIssueTransition(Credentials $credentials, $issueId, $transition)
    {
        Assert::integer($issueId);
        Assert::integer($transition);

        $data = array(
            'transition' => array(
                'id' => $transition,
            ),
        );

        $client = $this->getHttpClient();
        $uri = $credentials->getEntryPoint().'/rest/api/2/issue/'.$issueId.'/transitions';

        $request = $client->createRequest('POST', $uri, array('Content-type' => 'application/json'), json_encode($data));
        $response = $client->send($request, $this->_getAuthParams($credentials));

        return $this->_apiHandleResponse($request, $response);
    }

    /**
     * @param Credentials $credentials
     *
     * @return array
     */
    private function _getAuthParams(Credentials $credentials)
    {
        return array(
            'auth' => array(
                $credentials->getUsername(),
                $credentials->getPassword(),
            ),
        );
    }

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return \stdClass
     */
    private function _apiHandleResponse(RequestInterface $request, ResponseInterface $response)
    {
        $result = json_decode($response->getBody(), true);
        if ($result === false) {
            throw new RuntimeException('Unable decode response from URL '.$request->getUri().'; method: '.$request->getMethod());
        }

        if (isset($result['errorMessages']) || isset($result['errors'])) {
            $errorMessagesArr = array();
            if (isset($result['errorMessages'])) {
                foreach ($result['errorMessages'] as $field => $error) {
                    if (is_array($error)) {
                        $error = implode('; ', $error);
                    }

                    $errorMessagesArr[] = $field.' - '.$error;
                }
            }

            $errorArr = array();
            if (isset($result['errors'])) {
                foreach ($result['errors'] as $field => $error) {
                    if (is_array($error)) {
                        $error = implode('; ', $error);
                    }

                    $errorArr[] = $field.' - '.$error;
                }
            }

            throw new RuntimeException('The error occurred while processing data; error messages: "'.implode('; ', $errorMessagesArr).'"; errors: "'.implode('; ', $errorArr));
        }

        return $result;
    }

    private function _repairString($string)
    {
        $s = trim($string);
        $s = iconv('UTF-8', 'UTF-8//IGNORE', $s);
        $s = preg_replace('/(?>\xC2[\x80-\x9F]|\xE2[\x80-\x8F]{2}|\xE2\x80[\xA4-\xA8]|\xE2\x81[\x9F-\xAF])/', ' ', $s);

        return $s;
    }
}
