<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Connector\JiraConnectorInterface;
use DreamCommerce\Component\BugTracker\Exception\RuntimeException;
use DreamCommerce\Component\BugTracker\Model\Jira\Credentials;
use DreamCommerce\Component\BugTracker\Model\Jira\Issue;
use Psr\Log\LogLevel;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Webmozart\Assert\Assert;

class JiraCollector extends BaseCollector implements JiraCollectorInterface
{
    const SEPARATOR = "-----------------------------------------------------------\r\n";

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
     * @var int
     */
    protected $_counterFieldId;

    /**
     * @var int
     */
    protected $_counterMaxValue = PHP_INT_MAX;

    /**
     * @var int
     */
    protected $_tokenFieldId;

    /**
     * @var string
     */
    protected $_tokenFieldName;

    /**
     * @var array
     */
    protected $_fields = array();

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
     * @var bool
     */
    protected $_useCounter = true;

    /**
     * @var bool
     */
    protected $_useReopen = true;

    /**
     * @var JiraConnectorInterface
     */
    protected $_connector;

    /**
     * @var Credentials
     */
    protected $_credentials;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->_credentials = new Credentials();
        $this->setOptions($options, $this->_credentials);

        parent::__construct($options);
    }

    /**
     * @return Credentials
     */
    public function getCredentials()
    {
        return $this->_credentials;
    }

    /**
     * @param Credentials $credentials
     *
     * @return $this
     */
    public function setCredentials($credentials)
    {
        $this->_credentials = $credentials;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnector()
    {
        return $this->_connector;
    }

    /**
     * {@inheritdoc}
     */
    public function setConnector(JiraConnectorInterface $connector)
    {
        $this->_connector = $connector;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        $connector = $this->getConnector();
        $credentials = $this->getCredentials();

        if ($this->isUseToken()) {
            $token = $this->getTokenGenerator()->generate($exc, $level, $context);
            $result = $connector->findIssuesByField($credentials, $this->getTokenFieldName(), $token);
            if ($result === null) {
                $issue = new Issue();
                $this->_fillModel($issue, $exc, $level, $context);
                $connector->createIssue($credentials, $issue);
            } else {
                if ($this->isUseCounter()) {
                    $counterField = 'customfield_'.$this->getCounterFieldId();
                    if ($result['fields'][$counterField] < $this->getCounterMaxValue()) {
                        $connector->updateIssueFields(
                            $credentials,
                            $result['id'],
                            array(
                                $counterField => ++$result['fields'][$counterField],
                            )
                        );
                    }
                }

                if ($this->isUseReopen()) {
                    $statuses = $connector->getIssueTransitions($credentials, $result['id']);
                    $currentStatus = null;
                    foreach ($statuses['transitions'] as $transition) {
                        if ($transition['to']['id'] == $result['fields']['status']['id']) {
                            $currentStatus = $transition['id'];
                            break;
                        }
                    }
                    if ($currentStatus !== null && !in_array($currentStatus, $this->getInProgressStatuses())) {
                        $connector->updateIssueTransition($credentials, $result['id'], $this->getReopenStatus());
                    }
                }
            }
        } else {
            $issue = new Issue();
            $this->_fillModel($issue, $exc, $level, $context);
            $connector->createIssue($credentials, $issue);
        }

        $this->setIsCollected(true);
    }

    /**
     * @param Issue                 $issue
     * @param \Exception|\Throwable $exc
     * @param int                   $level
     * @param array                 $context
     *
     * @return string
     */
    protected function _fillModel(Issue $issue, $exc, $level, array $context = array())
    {
        $message = substr($exc->getMessage(), 0, 200).' (code: '.$exc->getCode().')';
        if (!($exc instanceof ContextErrorException)) {
            $message = get_class($exc).': '.$message;
        }

        $description = $exc->getMessage().' (code: '.$exc->getCode().')'.PHP_EOL.PHP_EOL.
            $exc->getFile().':'.$exc->getLine().PHP_EOL.
            static::SEPARATOR;

        if (!empty($context)) {
            $description .=
                'Parameters:'.PHP_EOL.PHP_EOL.
                $this->_prepareContext($context).PHP_EOL.
                static::SEPARATOR;
        }

        $description .= 'Stack trace:'.PHP_EOL.PHP_EOL;
        $description .= $exc->getTraceAsString();

        $issue->setSummary($message);
        $issue->setDescription($description);

        $issue->setAssignee($this->getAssignee());
        $issue->setLabels($this->getLabels());
        $issue->setFields($this->getFields());
        $issue->setProject($this->getProject());

        $priority = null;
        $priorities = $this->getPriorities();
        if (isset($priorities[$level])) {
            $priority = $priorities[$level];
        } else {
            $priority = $this->getDefaultPriority();
        }
        $issue->setPriority($priority);

        $type = null;
        $types = $this->getTypes();
        if (isset($types[$level])) {
            $type = $types[$level];
        } else {
            $type = $this->getDefaultType();
        }
        $issue->setType($type);
    }

    /**
     * {@inheritdoc}
     */
    public function getOpenStatus()
    {
        return $this->_openStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function setOpenStatus($openStatus)
    {
        $this->_openStatus = $openStatus;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInProgressStatuses()
    {
        return $this->_inProgressStatuses;
    }

    /**
     * {@inheritdoc}
     */
    public function setInProgressStatuses($inProgressStatuses)
    {
        $this->_inProgressStatuses = $inProgressStatuses;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReopenStatus()
    {
        return $this->_reopenStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function setReopenStatus($reopenStatus)
    {
        $this->_reopenStatus = $reopenStatus;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriorities()
    {
        return $this->_priorities;
    }

    /**
     * {@inheritdoc}
     */
    public function addPriority($level, $id)
    {
        $level = strtolower($level);
        $this->_priorities[$level] = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriorities($priorities)
    {
        $this->_priorities = $priorities;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabels()
    {
        return $this->_labels;
    }

    /**
     * {@inheritdoc}
     */
    public function addLabel($label)
    {
        $this->_labels[] = $label;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLabels(array $labels = array())
    {
        $this->_labels = $labels;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCounterFieldId()
    {
        if ($this->_counterFieldId === null) {
            throw new RuntimeException('Counter field ID has been not defined');
        }

        return $this->_counterFieldId;
    }

    /**
     * {@inheritdoc}
     */
    public function setCounterFieldId($counterFieldId)
    {
        $this->_counterFieldId = $counterFieldId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenFieldId()
    {
        if ($this->_tokenFieldId === null) {
            throw new RuntimeException('Token field ID has been not defined');
        }

        return $this->_tokenFieldId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenFieldId($tokenFieldId)
    {
        $this->_tokenFieldId = $tokenFieldId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenFieldName()
    {
        if ($this->_tokenFieldName === null) {
            throw new RuntimeException('Token field name has been not defined');
        }

        return $this->_tokenFieldName;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenFieldName($tokenFieldName)
    {
        $this->_tokenFieldName = $tokenFieldName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * {@inheritdoc}
     */
    public function addField($name, $value)
    {
        $this->_fields[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFields($fields)
    {
        $this->_fields = $fields;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isUseCounter()
    {
        return $this->_useCounter;
    }

    /**
     * {@inheritdoc}
     */
    public function setUseCounter($useCounter)
    {
        $this->_useCounter = $useCounter;
    }

    /**
     * {@inheritdoc}
     */
    public function isUseReopen()
    {
        return $this->_useReopen;
    }

    /**
     * {@inheritdoc}
     */
    public function setUseReopen($useReopen)
    {
        $this->_useReopen = (bool) $useReopen;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultType()
    {
        return $this->_defaultType;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultType($defaultType)
    {
        $this->_defaultType = $defaultType;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultPriority()
    {
        return $this->_defaultPriority;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultPriority($defaultPriority)
    {
        $this->_defaultPriority = $defaultPriority;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return $this->_types;
    }

    /**
     * {@inheritdoc}
     */
    public function addType($level, $type)
    {
        $this->_types[$level] = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setTypes(array $types = array())
    {
        $this->_types = $types;

        return $this;
    }

    /**
     * @return int
     */
    public function getCounterMaxValue()
    {
        return $this->_counterMaxValue;
    }

    /**
     * @param int $counterMaxValue
     *
     * @return $this
     */
    public function setCounterMaxValue($counterMaxValue)
    {
        Assert::integer($counterMaxValue);

        $this->_counterMaxValue = $counterMaxValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getProject()
    {
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

    protected function _prepareContext(array $array, $prefix = "\t")
    {
        $result = '';

        foreach ($array as $key => $value) {
            $result .= $key.': ';
            if (is_object($value)) {
                $value = '<'.get_class($value).'>';
            }
            if (is_array($value)) {
                $result .= PHP_EOL.static::_prepareContext($value, $prefix."\t");
            } else {
                $result .= $value;
            }
            $result .= PHP_EOL;
        }

        return $result;
    }
}
