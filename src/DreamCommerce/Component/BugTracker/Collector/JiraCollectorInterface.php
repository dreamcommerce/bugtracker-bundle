<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Http\Client\ClientInterface;

interface JiraCollectorInterface extends CollectorInterface
{
    /**
     * @return ClientInterface
     */
    public function getHttpClient();

    /**
     * @param ClientInterface $httpClient
     *
     * @return $this
     */
    public function setHttpClient(ClientInterface $httpClient);

    /**
     * @return string
     */
    public function getEntryPoint();

    /**
     * @param string $entryPoint
     *
     * @return $this
     */
    public function setEntryPoint($entryPoint);

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username);

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password);

    /**
     * @return int
     */
    public function getMaxCounter();

    /**
     * @param int $maxCounter
     *
     * @return $this
     */
    public function setMaxCounter($maxCounter);

    /**
     * @return string
     */
    public function getOpenStatus();

    /**
     * @param string $openStatus
     *
     * @return $this
     */
    public function setOpenStatus($openStatus);

    /**
     * @return array
     */
    public function getInProgressStatuses();

    /**
     * @param array $inProgressStatuses
     *
     * @return $this
     */
    public function setInProgressStatuses($inProgressStatuses);

    /**
     * @return string
     */
    public function getReopenStatus();

    /**
     * @param string $reopenStatus
     *
     * @return $this
     */
    public function setReopenStatus($reopenStatus);

    /**
     * @return string
     */
    public function getProject();

    /**
     * @param string $project
     *
     * @return $this
     */
    public function setProject($project);

    /**
     * @return string
     */
    public function getAssignee();

    /**
     * @param string $assignee
     *
     * @return $this
     */
    public function setAssignee($assignee);

    /**
     * @return array
     */
    public function getPriorities();

    /**
     * @param string $level
     * @param int    $id
     *
     * @return $this
     */
    public function addPriority($level, $id);

    /**
     * @param array $priorities
     *
     * @return $this
     */
    public function setPriorities($priorities);

    /**
     * @return array
     */
    public function getLabels();

    /**
     * @param string $label
     *
     * @return $this
     */
    public function addLabel($label);

    /**
     * @param array $labels
     *
     * @return $this
     */
    public function setLabels($labels);

    /**
     * @return int
     */
    public function getCounterFieldId();

    /**
     * @param int $counterFieldId
     *
     * @return $this
     */
    public function setCounterFieldId($counterFieldId);

    /**
     * @return int
     */
    public function getHashFieldId();

    /**
     * @param int $hashFieldId
     *
     * @return $this
     */
    public function setHashFieldId($hashFieldId);

    /**
     * @return array
     */
    public function getFields();

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function addField($name, $value);

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields($fields);

    /**
     * @return bool
     */
    public function isUseCounter();

    /**
     * @param bool $useCounter
     */
    public function setUseCounter($useCounter);

    /**
     * @return bool
     */
    public function isUseHash();

    /**
     * @param bool $useHash
     *
     * @return $this
     */
    public function setUseHash($useHash);

    /**
     * @return bool
     */
    public function isUseReopen();

    /**
     * @param bool $useReopen
     *
     * @return $this
     */
    public function setUseReopen($useReopen);

    /**
     * @return int
     */
    public function getDefaultType();

    /**
     * @param int $defaultType
     *
     * @return $this
     */
    public function setDefaultType($defaultType);

    /**
     * @return int
     */
    public function getDefaultPriority();

    /**
     * @param int $defaultPriority
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
     * @param int    $type
     *
     * @return $this
     */
    public function addType($level, $type);

    /**
     * @param array $types
     *
     * @return $this
     */
    public function setTypes($types);
}
