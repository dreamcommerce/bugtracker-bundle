<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Connector\JiraConnectorInterface;
use DreamCommerce\Component\BugTracker\Exception\InvalidArgumentException;
use DreamCommerce\Component\BugTracker\Exception\NotDefinedException;
use DreamCommerce\Component\BugTracker\Model\Jira\Credentials;

interface JiraCollectorInterface extends CollectorInterface
{
    /**
     * @throws NotDefinedException
     *
     * @return Credentials
     */
    public function getCredentials();

    /**
     * @param Credentials $credentials
     *
     * @return $this
     */
    public function setCredentials(Credentials $credentials);

    /**
     * @throws NotDefinedException
     *
     * @return JiraConnectorInterface
     */
    public function getConnector();

    /**
     * @param JiraConnectorInterface $connector
     *
     * @return $this
     */
    public function setConnector(JiraConnectorInterface $connector);

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getOpenStatus();

    /**
     * @param int $openStatus
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setOpenStatus($openStatus);

    /**
     * @return array
     */
    public function getInProgressStatuses();

    /**
     * @param int $inProgressStatus
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function addInProgressStatus($inProgressStatus);

    /**
     * @param array $inProgressStatuses
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setInProgressStatuses(array $inProgressStatuses = array());

    /**
     * @throws NotDefinedException
     *
     * @return int
     */
    public function getReopenStatus();

    /**
     * @param int $reopenStatus
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setReopenStatus($reopenStatus);

    /**
     * @return array
     */
    public function getPriorities();

    /**
     * @param int $level
     * @param int $id
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function addPriority($level, $id);

    /**
     * @param array $priorities
     *
     * @return $this
     */
    public function setPriorities(array $priorities = array());

    /**
     * @throws NotDefinedException
     *
     * @return array
     */
    public function getLabels();

    /**
     * @param string $label
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function addLabel($label);

    /**
     * @param array $labels
     *
     * @return $this
     */
    public function setLabels(array $labels = array());

    /**
     * @throws NotDefinedException
     *
     * @return int
     */
    public function getCounterFieldId();

    /**
     * @param int $counterFieldId
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setCounterFieldId($counterFieldId);

    /**
     * @throws NotDefinedException
     *
     * @return int
     */
    public function getTokenFieldId();

    /**
     * @param int $tokenFieldId
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setTokenFieldId($tokenFieldId);

    /**
     * @throws NotDefinedException
     *
     * @return int
     */
    public function getTokenFieldName();

    /**
     * @throws InvalidArgumentException
     *
     * @param string $tokenFieldName
     *
     * @return $this
     */
    public function setTokenFieldName($tokenFieldName);

    /**
     * @return array
     */
    public function getFields();

    /**
     * @throws InvalidArgumentException
     *
     * @param string                $name
     * @param string|int|float|bool $value
     *
     * @return $this
     */
    public function addField($name, $value);

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields = array());

    /**
     * @return bool
     */
    public function isUseCounter();

    /**
     * @param bool $useCounter
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setUseCounter($useCounter);

    /**
     * @return bool
     */
    public function isUseReopen();

    /**
     * @param bool $useReopen
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setUseReopen($useReopen);

    /**
     * @return string|null
     */
    public function getDefaultType();

    /**
     * @param string|null $defaultType
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setDefaultType($defaultType = null);

    /**
     * @return int|null
     */
    public function getDefaultPriority();

    /**
     * @param null|int $defaultPriority
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setDefaultPriority($defaultPriority);

    /**
     * @return array
     */
    public function getTypes();

    /**
     * @param string $level
     * @param string $type
     *
     * @return $this
     */
    public function addType($level, $type);

    /**
     * @param array $types
     *
     * @return $this
     */
    public function setTypes(array $types = array());

    /**
     * @return null|int
     */
    public function getCounterMaxValue();

    /**
     * @param int|null $counterMaxValue
     *
     * @return $this
     */
    public function setCounterMaxValue($counterMaxValue = null);

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getProject();

    /**
     * @param string $project
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setProject($project);

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getAssignee();

    /**
     * @param string $assignee
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setAssignee($assignee);
}
