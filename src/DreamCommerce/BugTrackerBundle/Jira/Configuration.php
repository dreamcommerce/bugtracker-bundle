<?php

namespace DreamCommerce\BugTrackerBundle\Jira;

use DreamCommerce\BugTrackerBundle\Exception\InvalidArgumentException;
use DreamCommerce\BugTrackerBundle\Traits\Options;

class Configuration
{
    use Options;

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
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * @return string
     */
    public function getEntryPoint()
    {
        if ($this->_entryPoint === null) {
            throw new InvalidArgumentException('Entry point has been not defined');
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
            throw new InvalidArgumentException('Username has been not defined');
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
            throw new InvalidArgumentException('Password has been not defined');
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
            throw new InvalidArgumentException('Project has been not defined');
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
            throw new InvalidArgumentException('Counter field ID has been not defined');
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
            throw new InvalidArgumentException('Hash field ID has been not defined');
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
     * @return array
     */
    public function getAuthParams()
    {
        return array(
            'auth' => array(
                $this->getUsername(),
                $this->getPassword()
            ),
        );
    }
}