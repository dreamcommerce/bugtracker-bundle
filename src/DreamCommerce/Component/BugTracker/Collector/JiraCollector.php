<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\BugHandler;
use DreamCommerce\Component\BugTracker\Connector\JiraConnectorInterface;
use DreamCommerce\Component\BugTracker\Exception\NotDefinedException;
use DreamCommerce\Component\BugTracker\Model\Jira\Credentials;
use DreamCommerce\Component\BugTracker\Model\Jira\Issue;
use Psr\Log\LogLevel;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Webmozart\Assert\Assert;

class JiraCollector extends BaseCollector implements JiraCollectorInterface
{
    const SEPARATOR = '-----------------------------------------------------------';

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
     * @var int|null
     */
    protected $_defaultType;

    /**
     * @var array
     */
    protected $_priorities = array();

    /**
     * @var int|null
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
     * @var int|null
     */
    protected $_counterMaxValue;

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
     * {@inheritdoc}
     */
    protected function _handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        $connector = $this->getConnector();
        $credentials = $this->getCredentials();

        if ($this->isUseToken()) {
            $token = $this->getTokenGenerator()->generate($exc, $level, $context);
            $result = $connector->findIssuesByField($credentials, $this->getTokenFieldName(), $token);

            if ($result === null || count($result) === 0) {
                $issue = new Issue();
                $this->_fillModel($issue, $exc, $level, $context, $token);
                $connector->createIssue($credentials, $issue);
            } else {
                $result = $result[0];

                if ($this->isUseCounter()) {
                    $counterField = 'customfield_'.$this->getCounterFieldId();
                    $counterMaxValue = $this->getCounterMaxValue();

                    if ($counterMaxValue === null || $result['fields'][$counterField] < $counterMaxValue) {
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
     * @param string|null           $token
     */
    protected function _fillModel(Issue $issue, $exc, $level, array $context = array(), $token = null)
    {
        $message = substr($exc->getMessage(), 0, 200).' (code: '.$exc->getCode().')';
        if (!($exc instanceof ContextErrorException)) {
            $message = get_class($exc).': '.$message;
        }

        $description = $exc->getMessage().' (code: '.$exc->getCode().')'.PHP_EOL.PHP_EOL.
            $exc->getFile().':'.$exc->getLine().PHP_EOL.
            static::SEPARATOR.PHP_EOL;

        if (!empty($context)) {
            $description .=
                'Parameters:'.PHP_EOL.PHP_EOL.
                $this->_prepareContext($context).PHP_EOL.
                static::SEPARATOR.PHP_EOL;
        }

        $description .= 'Stack trace:'.PHP_EOL.PHP_EOL;
        $description .= $exc->getTraceAsString();

        $issue->setSummary($message);
        $issue->setDescription($description);

        $issue->setAssignee($this->getAssignee());
        $issue->setLabels($this->getLabels());
        $issue->setProject($this->getProject());

        $fields = $this->getFields();
        if ($token !== null) {
            $fields[$this->getTokenFieldId()] = $token;
        }
        if ($this->isUseCounter()) {
            $fields[$this->getCounterFieldId()] = 1;
        }

        $issue->setFields($fields);

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
    public function getCredentials()
    {
        if ($this->_credentials === null) {
            throw new NotDefinedException(__CLASS__.'::_credentials');
        }

        return $this->_credentials;
    }

    /**
     * {@inheritdoc}
     */
    public function setCredentials(Credentials $credentials)
    {
        $this->_credentials = $credentials;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnector()
    {
        if ($this->_connector === null) {
            throw new NotDefinedException(__CLASS__.'::_connector');
        }

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
    public function getOpenStatus()
    {
        if ($this->_openStatus === null) {
            throw new NotDefinedException(__CLASS__.'::_openStatus');
        }

        return $this->_openStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function setOpenStatus($openStatus)
    {
        Assert::integerish($openStatus);

        $this->_openStatus = (int) $openStatus;

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
    public function addInProgressStatus($inProgressStatus)
    {
        Assert::integerish($inProgressStatus);

        if (!in_array($inProgressStatus, $this->_inProgressStatuses)) {
            $this->_inProgressStatuses[] = $inProgressStatus;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setInProgressStatuses(array $inProgressStatuses = array())
    {
        $this->_inProgressStatuses = $inProgressStatuses;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReopenStatus()
    {
        if ($this->_reopenStatus === null) {
            throw new NotDefinedException(__CLASS__.'::_reopenStatus');
        }

        return $this->_reopenStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function setReopenStatus($reopenStatus)
    {
        Assert::integerish($reopenStatus);

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
        Assert::stringNotEmpty($level);
        $level = strtolower($level);
        Assert::oneOf($level, BugHandler::getSupportedLogLevels());

        $this->_priorities[$level] = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriorities(array $priorities = array())
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
        Assert::stringNotEmpty($label);

        if (!in_array($label, $this->_labels)) {
            $this->_labels[] = $label;
        }

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
            throw new NotDefinedException(__CLASS__.'::_counterFieldId');
        }

        return $this->_counterFieldId;
    }

    /**
     * {@inheritdoc}
     */
    public function setCounterFieldId($counterFieldId)
    {
        Assert::integerish($counterFieldId);

        $this->_counterFieldId = (int) $counterFieldId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenFieldId()
    {
        if ($this->_tokenFieldId === null) {
            throw new NotDefinedException(__CLASS__.'::_tokenFieldId');
        }

        return $this->_tokenFieldId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenFieldId($tokenFieldId)
    {
        Assert::integerish($tokenFieldId);

        $this->_tokenFieldId = $tokenFieldId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenFieldName()
    {
        if ($this->_tokenFieldName === null) {
            throw new NotDefinedException(__CLASS__.'::_tokenFieldName');
        }

        return $this->_tokenFieldName;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenFieldName($tokenFieldName)
    {
        Assert::stringNotEmpty($tokenFieldName);

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
        Assert::stringNotEmpty($name);
        Assert::scalar($value);

        $this->_fields[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFields(array $fields = array())
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
        Assert::boolean($useCounter);

        $this->_useCounter = $useCounter;

        return $this;
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
        Assert::boolean($useReopen);

        $this->_useReopen = $useReopen;

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
    public function setDefaultType($defaultType = null)
    {
        Assert::nullOrIntegerish($defaultType);

        $this->_defaultType = $defaultType;

        return $this;
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
    public function setDefaultPriority($defaultPriority = null)
    {
        Assert::nullOrIntegerish($defaultPriority);

        $this->_defaultPriority = (int) $defaultPriority;

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
        Assert::stringNotEmpty($level);
        $level = strtolower($level);
        Assert::oneOf($level, BugHandler::getSupportedLogLevels());

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
     * {@inheritdoc}
     */
    public function getCounterMaxValue()
    {
        return $this->_counterMaxValue;
    }

    /**
     * {@inheritdoc}
     */
    public function setCounterMaxValue($counterMaxValue = null)
    {
        Assert::nullOrIntegerish($counterMaxValue);

        $this->_counterMaxValue = $counterMaxValue;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProject()
    {
        if ($this->_project === null) {
            throw new NotDefinedException(__CLASS__.'::_project');
        }

        return $this->_project;
    }

    /**
     * {@inheritdoc}
     */
    public function setProject($project)
    {
        Assert::stringNotEmpty($project);

        $this->_project = $project;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssignee()
    {
        if ($this->_assignee === null) {
            throw new NotDefinedException(__CLASS__.'::_assignee');
        }

        return $this->_assignee;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssignee($assignee)
    {
        Assert::stringNotEmpty($assignee);

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
