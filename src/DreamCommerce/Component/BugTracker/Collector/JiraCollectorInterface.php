<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Connector\JiraConnectorInterface;
use DreamCommerce\Component\BugTracker\Model\Jira\Credentials;
use DreamCommerce\Component\Common\Exception\NotDefinedException;
use InvalidArgumentException;

interface JiraCollectorInterface extends CollectorInterface
{
    /**
     * @throws NotDefinedException
     *
     * @return Credentials
     */
    public function getCredentials(): Credentials;

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
    public function getConnector(): JiraConnectorInterface;

    /**
     * @param JiraConnectorInterface $connector
     *
     * @return $this
     */
    public function setConnector(JiraConnectorInterface $connector);

    /**
     * @throws NotDefinedException
     *
     * @return int
     */
    public function getOpenStatus(): int;

    /**
     * @param int $openStatus
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setOpenStatus(int $openStatus);

    /**
     * @return array
     */
    public function getInProgressStatuses(): array;

    /**
     * @param int $inProgressStatus
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function addInProgressStatus(int $inProgressStatus);

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
    public function getReopenStatus(): int;

    /**
     * @param int $reopenStatus
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setReopenStatus(int $reopenStatus);

    /**
     * @return array
     */
    public function getPriorities(): array;

    /**
     * @param string $level
     * @param int $id
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function addPriority(string $level, int $id);

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
    public function getLabels(): array;

    /**
     * @param string $label
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function addLabel(string $label);

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
    public function getCounterFieldId(): int;

    /**
     * @param int $counterFieldId
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setCounterFieldId(int $counterFieldId);

    /**
     * @throws NotDefinedException
     *
     * @return int
     */
    public function getTokenFieldId(): int;

    /**
     * @param int $tokenFieldId
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setTokenFieldId(int $tokenFieldId);

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getTokenFieldName(): string;

    /**
     * @throws InvalidArgumentException
     *
     * @param string $tokenFieldName
     *
     * @return $this
     */
    public function setTokenFieldName(string $tokenFieldName);

    /**
     * @return array
     */
    public function getFields(): array;

    /**
     * @throws InvalidArgumentException
     *
     * @param string                $name
     * @param string|int|float|bool $value
     *
     * @return $this
     */
    public function addField(string $name, $value);

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields = array());

    /**
     * @return bool
     */
    public function isUseCounter(): bool;

    /**
     * @param bool $useCounter
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setUseCounter(bool $useCounter);

    /**
     * @return bool
     */
    public function isUseReopen(): bool;

    /**
     * @param bool $useReopen
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setUseReopen(bool $useReopen);

    /**
     * @throws NotDefinedException
     * @return string
     */
    public function getDefaultType(): string;

    /**
     * @param string|null $defaultType
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setDefaultType(string $defaultType = null);

    /**
     * @throws NotDefinedException
     * @return int
     */
    public function getDefaultPriority(): int;

    /**
     * @param null|int $defaultPriority
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setDefaultPriority(int $defaultPriority = null);

    /**
     * @return array
     */
    public function getTypes(): array ;

    /**
     * @param string $level
     * @param string $type
     *
     * @return $this
     */
    public function addType(string $level, string $type);

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
    public function setCounterMaxValue(int $counterMaxValue = null);

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getProject(): string;

    /**
     * @param string $project
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setProject(string $project);

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getAssignee(): string;

    /**
     * @param string $assignee
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setAssignee(string $assignee);
}
