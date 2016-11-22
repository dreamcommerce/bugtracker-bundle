<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Exception\RuntimeException;
use DreamCommerce\Component\BugTracker\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Debug\Exception\ContextErrorException;

class JiraCollector extends BaseCollector
{
    /**
     * @var ClientInterface
     */
    protected $_httpClient;

    /**
     * @var string
     */
    protected $_entryPoint;

    /**
     * @var string
     */
    protected $_username;

    /**
     * @var string
     */
    protected $_password;

    /**
     * @var int
     */
    protected $_maxCounter = PHP_INT_MAX;

    /**
     * @var string
     */
    protected $_openStatus;

    /**
     * @var array
     */
    protected $_inProgressStatuses = array();

    /**
     * @var string
     */
    protected $_reopenStatus;

    /**
     * @var string
     */
    protected $_project;

    /**
     * @var string
     */
    protected $_assignee;

    /**
     * @var array
     */
    protected $_types;

    /**
     * @var int
     */
    protected $_defaultType;

    /**
     * @var array
     */
    protected $_priorities = array();

    /**
     * @var int
     */
    protected $_defaultPriority;

    /**
     * @var array
     */
    protected $_labels = array();

    /**
     * @var bool
     */
    protected $_useCounter = true;

    /**
     * @var bool
     */
    protected $_useHash = true;

    /**
     * @var bool
     */
    protected $_useReopen = true;

    /**
     * @var int
     */
    protected $_counterFieldId;

    /**
     * @var int
     */
    protected $_hashFieldId;

    /**
     * @var array
     */
    protected $_fields = array();

    /**
     * @param ClientInterface $httpClient
     * @param array           $options
     */
    public function __construct(ClientInterface $httpClient, array $options = array())
    {
        $this->_httpClient = $httpClient;
        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    protected function _handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        if ($this->isUseHash()) {
            $hash = $this->_getJiraHash($exc, $level, $context);
            $result = $this->_apiGetIssueByHash($hash);
            if ($result === null) {
                $this->_apiCreateIssue($exc, $level, $context, $hash);
            } else {
                if ($this->isUseCounter()) {
                    $counterField = 'customfield_'.$this->getCounterFieldId();
                    if ($result['fields'][$counterField] < $this->getMaxCounter()) {
                        $this->_apiUpdateCounterForIssue($result['id'], ++$result['fields'][$counterField]);
                    }
                }

                if ($this->isUseReopen()) {
                    $statuses = $this->_apiGetStatusesForIssue($result['id']);
                    $currentStatus = null;
                    foreach ($statuses['transitions'] as $transition) {
                        if ($transition['to']['id'] == $result['fields']['status']['id']) {
                            $currentStatus = $transition['id'];
                            break;
                        }
                    }
                    if ($currentStatus !== null && !in_array($currentStatus, $this->getInProgressStatuses())) {
                        $this->_apiUpdateStatusForIssue($result['id'], $this->getReopenStatus());
                    }
                }
            }
        } else {
            $this->_apiCreateIssue($exc, $level, $context);
        }

        $this->_isCollected = true;
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
     *
     * @return $this
     */
    public function setHttpClient(ClientInterface $httpClient)
    {
        $this->_httpClient = $httpClient;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntryPoint()
    {
        if ($this->_entryPoint === null) {
            throw new RuntimeException('Entry point has been not defined');
        }

        return $this->_entryPoint;
    }

    /**
     * @param string $entryPoint
     *
     * @return $this
     */
    public function setEntryPoint($entryPoint)
    {
        $this->_entryPoint = $entryPoint;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        if ($this->_username === null) {
            throw new RuntimeException('Username has been not defined');
        }

        return $this->_username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->_username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        if ($this->_password === null) {
            throw new RuntimeException('Password has been not defined');
        }

        return $this->_password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->_password = $password;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxCounter()
    {
        return $this->_maxCounter;
    }

    /**
     * @param int $maxCounter
     *
     * @return $this
     */
    public function setMaxCounter($maxCounter)
    {
        $this->_maxCounter = $maxCounter;

        return $this;
    }

    /**
     * @return string
     */
    public function getOpenStatus()
    {
        return $this->_openStatus;
    }

    /**
     * @param string $openStatus
     *
     * @return $this
     */
    public function setOpenStatus($openStatus)
    {
        $this->_openStatus = $openStatus;

        return $this;
    }

    /**
     * @return array
     */
    public function getInProgressStatuses()
    {
        return $this->_inProgressStatuses;
    }

    /**
     * @param array $inProgressStatuses
     *
     * @return $this
     */
    public function setInProgressStatuses($inProgressStatuses)
    {
        $this->_inProgressStatuses = $inProgressStatuses;

        return $this;
    }

    /**
     * @return string
     */
    public function getReopenStatus()
    {
        return $this->_reopenStatus;
    }

    /**
     * @param string $reopenStatus
     *
     * @return $this
     */
    public function setReopenStatus($reopenStatus)
    {
        $this->_reopenStatus = $reopenStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getProject()
    {
        if ($this->_entryPoint === null) {
            throw new RuntimeException('Project has been not defined');
        }

        return $this->_project;
    }

    /**
     * @param string $project
     *
     * @return $this
     */
    public function setProject($project)
    {
        $this->_project = $project;

        return $this;
    }

    /**
     * @return string
     */
    public function getAssignee()
    {
        if ($this->_assignee === null) {
            $this->_assignee = $this->getUsername();
        }

        return $this->_assignee;
    }

    /**
     * @param string $assignee
     *
     * @return $this
     */
    public function setAssignee($assignee)
    {
        $this->_assignee = $assignee;

        return $this;
    }

    /**
     * @return array
     */
    public function getPriorities()
    {
        return $this->_priorities;
    }

    /**
     * @param string $level
     * @param int    $id
     *
     * @return $this
     */
    public function addPriority($level, $id)
    {
        $level = strtolower($level);
        $this->_priorities[$level] = $id;

        return $this;
    }

    /**
     * @param array $priorities
     *
     * @return $this
     */
    public function setPriorities($priorities)
    {
        $this->_priorities = $priorities;

        return $this;
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->_labels;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function addLabel($label)
    {
        $this->_labels[] = $label;

        return $this;
    }

    /**
     * @param array $labels
     *
     * @return $this
     */
    public function setLabels($labels)
    {
        $this->_labels = $labels;

        return $this;
    }

    /**
     * @return int
     */
    public function getCounterFieldId()
    {
        return $this->_counterFieldId;
    }

    /**
     * @param int $counterFieldId
     *
     * @return $this
     */
    public function setCounterFieldId($counterFieldId)
    {
        if ($this->_entryPoint === null) {
            throw new RuntimeException('Counter field ID has been not defined');
        }

        $this->_counterFieldId = $counterFieldId;

        return $this;
    }

    /**
     * @return int
     */
    public function getHashFieldId()
    {
        return $this->_hashFieldId;
    }

    /**
     * @param int $hashFieldId
     *
     * @return $this
     */
    public function setHashFieldId($hashFieldId)
    {
        if ($this->_entryPoint === null) {
            throw new RuntimeException('Hash field ID has been not defined');
        }

        $this->_hashFieldId = $hashFieldId;

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function addField($name, $value)
    {
        $this->_fields[$name] = $value;

        return $this;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields($fields)
    {
        $this->_fields = $fields;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUseCounter()
    {
        return $this->_useCounter;
    }

    /**
     * @param bool $useCounter
     */
    public function setUseCounter($useCounter)
    {
        $this->_useCounter = $useCounter;
    }

    /**
     * @return bool
     */
    public function isUseHash()
    {
        return $this->_useHash;
    }

    /**
     * @param bool $useHash
     *
     * @return $this
     */
    public function setUseHash($useHash)
    {
        $this->_useHash = (bool) $useHash;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUseReopen()
    {
        return $this->_useReopen;
    }

    /**
     * @param bool $useReopen
     *
     * @return $this
     */
    public function setUseReopen($useReopen)
    {
        $this->_useReopen = (bool) $useReopen;

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultType()
    {
        return $this->_defaultType;
    }

    /**
     * @param int $defaultType
     *
     * @return $this
     */
    public function setDefaultType($defaultType)
    {
        $this->_defaultType = $defaultType;
    }

    /**
     * @return int
     */
    public function getDefaultPriority()
    {
        return $this->_defaultPriority;
    }

    /**
     * @param int $defaultPriority
     *
     * @return $this
     */
    public function setDefaultPriority($defaultPriority)
    {
        $this->_defaultPriority = $defaultPriority;

        return $this;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->_types;
    }

    /**
     * @param string $level
     * @param int    $type
     *
     * @return $this
     */
    public function addType($level, $type)
    {
        $this->_types[$level] = $type;

        return $this;
    }

    /**
     * @param array $types
     *
     * @return $this
     */
    public function setTypes($types)
    {
        $this->_types = $types;

        return $this;
    }

    /**
     * @param \Exception|\Throwable $exc
     * @param int                   $level
     * @param array                 $context
     *
     * @return string
     */
    protected function _getJiraSummary($exc, $level, array $context = array())
    {
        $message = substr($exc->getMessage(), 0, 200).' (code: '.$exc->getCode().')';
        if (!($exc instanceof ContextErrorException)) {
            $message = get_class($exc).': '.$message;
        }

        return substr($message, 0, 255);
    }

    /**
     * @param \Exception|\Throwable $exc
     * @param int                   $level
     * @param array                 $context
     *
     * @return string
     */
    protected function _getJiraDescription($exc, $level, array $context = array())
    {
        return substr(
            $exc->getMessage().' (code: '.$exc->getCode().')'.PHP_EOL.PHP_EOL.
            $exc->getTraceAsString(),
            0, 4000
        );
    }

    /**
     * @param \Exception|\Throwable $exc
     * @param int                   $level
     * @param array                 $context
     *
     * @return string
     */
    protected function _getJiraHash($exc, $level, array $context = array())
    {
        $hashParams = array();
        foreach ($this->_getJiraHashParams($exc, $level, $context) as $paramName) {
            if (isset($context[$paramName])) {
                $hashParams[$paramName] = $context[$paramName];
            }
        }

        if (count($hashParams) > 0) {
            ksort($hashParams);
            $hash = md5(serialize($hashParams));
        } else {
            $hash = md5(uniqid(rand(), true));
        }

        return $hash;
    }

    /**
     * @param \Exception|\Throwable $exc
     * @param int                   $level
     * @param array                 $context
     *
     * @return array
     */
    protected function _getJiraHashParams($exc, $level, array $context = array())
    {
        return array('message', 'code', 'line', 'file');
    }

    /**
     * @param \Exception|\Throwable $exc
     * @param string                $level
     * @param array                 $context
     * @param string|null           $hash
     *
     * @return \stdClass
     */
    private function _apiCreateIssue($exc, $level, array $context = array(), $hash = null)
    {
        $summary = preg_replace('/\s+/', ' ', $this->_repairString($this->_getJiraSummary($exc, $level, $context)));
        $description = $this->_repairString($this->_getJiraDescription($exc, $level, $context));

        $data = array(
            'project' => array(
                'key' => $this->getProject(),
            ),
            'summary' => $summary,
            'description' => $description,
            'assignee' => array(
                'name' => $this->getAssignee(),
            ),
            'labels' => $this->getLabels(),
        );

        if ($this->isUseCounter()) {
            $data['customfield_'.$this->getCounterFieldId()] = 1;
        }
        if ($this->isUseHash() && $hash !== null) {
            $data['customfield_'.$this->getHashFieldId()] = $hash;
        }

        $priority = null;
        $priorities = $this->getPriorities();
        if (isset($priorities[$level])) {
            $priority = $priorities[$level];
        } else {
            $priority = $this->getDefaultPriority();
        }
        if ($priority !== null) {
            $data['priority'] = array(
                'id' => (string) $priority,
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

        $data = array_merge($data, $this->getFields());
        $client = $this->getHttpClient();
        $uri = $this->getEntryPoint().'/rest/api/2/issue';

        $request = $client->createRequest('POST', $uri, array('Content-type' => 'application/json'), json_encode(array('fields' => $data)));
        $response = $client->send($request, $this->_getAuthParams());

        return $this->_apiHandleResponse($request, $response);
    }

    /**
     * @param string $hash
     *
     * @return \stdClass
     */
    private function _apiGetIssueByHash($hash)
    {
        $client = $this->getHttpClient();
        $uri = $this->getEntryPoint().'/rest/api/2/search?jql=hash ~ '.$hash;
        $request = $client->createRequest('GET', $uri, array('Content-type' => 'application/json'));
        $response = $client->send($request, $this->_getAuthParams());

        $result = $this->_apiHandleResponse($request, $response);
        if ($result['total'] == 0) {
            return;
        }

        return $result['issues'][0];
    }

    /**
     * @param int $issueId
     * @param int $counter
     *
     * @return \stdClass
     */
    private function _apiUpdateCounterForIssue($issueId, $counter)
    {
        $counterField = 'customfield_'.$this->getCounterFieldId();
        $data = array(
            'fields' => array(
                $counterField => $counter,
            ),
        );

        $client = $this->getHttpClient();
        $uri = $this->getEntryPoint().'/rest/api/2/issue/'.$issueId;

        $request = $client->createRequest('PUT', $uri, array('Content-type' => 'application/json'), json_encode($data));
        $response = $client->send($request, $this->_getAuthParams());

        return $this->_apiHandleResponse($request, $response);
    }

    /**
     * @param int $issueId
     *
     * @return \stdClass
     */
    private function _apiGetStatusesForIssue($issueId)
    {
        $client = $this->getHttpClient();
        $uri = $this->getEntryPoint().'/rest/api/2/issue/'.$issueId.'/transitions';

        $request = $client->createRequest('GET', $uri, array('Content-type' => 'application/json'));
        $response = $client->send($request, $this->_getAuthParams());

        return $this->_apiHandleResponse($request, $response);
    }

    /**
     * @param int $issueId
     * @param int $status
     *
     * @return \stdClass
     */
    private function _apiUpdateStatusForIssue($issueId, $status)
    {
        $data = array(
            'transition' => array(
                'id' => $status,
            ),
        );

        $client = $this->getHttpClient();
        $uri = $this->getEntryPoint().'/rest/api/2/issue/'.$issueId.'/transitions';

        $request = $client->createRequest('POST', $uri, array('Content-type' => 'application/json'), json_encode($data));
        $response = $client->send($request, $this->_getAuthParams());

        return $this->_apiHandleResponse($request, $response);
    }

    /**
     * @return array
     */
    private function _getAuthParams()
    {
        return array(
            'auth' => array(
                $this->getUsername(),
                $this->getPassword(),
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
