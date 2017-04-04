<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Connector;

use DreamCommerce\Component\BugTracker\Exception\Jira\ErrorMessageException;
use DreamCommerce\Component\BugTracker\Exception\Jira\UnableDecodeResponseException;
use DreamCommerce\Component\BugTracker\Exception\JiraException;
use DreamCommerce\Component\BugTracker\Model\Jira\Credentials;
use DreamCommerce\Component\BugTracker\Model\Jira\Issue;
use DreamCommerce\Component\Common\Exception\NotDefinedException;
use DreamCommerce\Component\Common\Http\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Webmozart\Assert\Assert;
use Zend\Json\Json;

final class JiraConnector implements JiraConnectorInterface
{
    /**
     * @var ClientInterface
     */
    private $_httpClient;

    /**
     * @param ClientInterface|null $httpClient
     */
    public function __construct(ClientInterface $httpClient = null)
    {
        $this->_httpClient = $httpClient;
    }

    /**
     * @throws NotDefinedException
     * @return ClientInterface
     */
    public function getHttpClient(): ClientInterface
    {
        if ($this->_httpClient === null) {
            throw NotDefinedException::forVariable(__CLASS__ . '::_httpClient');
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
    public function createIssue(Credentials $credentials, Issue $issue): array
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
    public function findIssuesByField(Credentials $credentials, string $fieldName, string $fieldValue): array
    {
        Assert::notEmpty($fieldName);
        Assert::notEmpty($fieldValue);

        $client = $this->getHttpClient();
        $uri = $credentials->getEntryPoint().'/rest/api/2/search?jql='.$fieldName.' ~ '.$fieldValue;
        $request = $client->createRequest('GET', $uri, array('Content-type' => 'application/json'));
        $response = $client->send($request, $this->_getAuthParams($credentials));

        $result = $this->_apiHandleResponse($request, $response);
        if(!isset($result['issues'])) {
            throw NotDefinedException::forVariable('issues');
        }

        return $result['issues'];
    }

    /**
     * {@inheritdoc}
     */
    public function updateIssueFields(Credentials $credentials, int $issueId, array $fields = array()): array
    {
        Assert::integerish($issueId);
        $issueId = (int) $issueId;

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
    public function getIssueTransitions(Credentials $credentials, int $issueId): array
    {
        $client = $this->getHttpClient();
        $uri = $credentials->getEntryPoint().'/rest/api/2/issue/'.$issueId.'/transitions';

        $request = $client->createRequest('GET', $uri, array('Content-type' => 'application/json'));
        $response = $client->send($request, $this->_getAuthParams($credentials));

        return $this->_apiHandleResponse($request, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function updateIssueTransition(Credentials $credentials, int $issueId, int $transition): array
    {
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
    private function _getAuthParams(Credentials $credentials): array
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
     * @throws JiraException
     *
     * @return array
     */
    private function _apiHandleResponse(RequestInterface $request, ResponseInterface $response): array
    {
        $body = $response->getBody();
        if(strlen($body) === 0 && $response->getStatusCode() === 204 && in_array($request->getMethod(), array('PUT', 'POST'))) {
            return array();
        }

        try {
            Json::$useBuiltinEncoderDecoder = true;
            $result = Json::decode($body);
        } catch(RuntimeException $exception) {
            throw UnableDecodeResponseException::forRequest($request, $exception);
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

            throw ErrorMessageException::forResponseErrors($errorArr, $errorMessagesArr);
        }

        return $result;
    }

    private function _repairString(string $string): string
    {
        $s = trim($string);
        $s = iconv('UTF-8', 'UTF-8//IGNORE', $s);
        $s = preg_replace('/(?>\xC2[\x80-\x9F]|\xE2[\x80-\x8F]{2}|\xE2\x80[\xA4-\xA8]|\xE2\x81[\x9F-\xAF])/', ' ', $s);

        return $s;
    }
}
