<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Model\Jira;

use DreamCommerce\Component\Common\Exception\NotDefinedException;
use DreamCommerce\Component\Common\Model\ArrayableInterface;
use DreamCommerce\Component\Common\Model\ArrayableTrait;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class Issue implements ArrayableInterface
{
    use ArrayableTrait;

    /**
     * @var string
     */
    private $_summary;

    /**
     * @var string
     */
    private $_description;

    /**
     * @var string
     */
    private $_project;

    /**
     * @var string
     */
    private $_assignee;

    /**
     * @var array
     */
    private $_labels = array();

    /**
     * @var array
     */
    private $_fields = array();

    /**
     * @var int
     */
    private $_priority;

    /**
     * @var int
     */
    private $_type;

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->fromArray($options);
    }

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getSummary(): string
    {
        if ($this->_summary === null) {
            throw NotDefinedException::forVariable(__CLASS__.'::_summary');
        }

        return $this->_summary;
    }

    /**
     * @param string $summary
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setSummary(string $summary)
    {
        Assert::stringNotEmpty($summary);

        $this->_summary = $summary;

        return $this;
    }

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getDescription(): string
    {
        if ($this->_description === null) {
            throw NotDefinedException::forVariable(__CLASS__.'::_description');
        }

        return $this->_description;
    }

    /**
     * @param string $description
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setDescription(string $description)
    {
        Assert::stringNotEmpty($description);

        $this->_description = $description;

        return $this;
    }

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getProject(): string
    {
        if ($this->_project === null) {
            throw NotDefinedException::forVariable(__CLASS__.'::_project');
        }

        return $this->_project;
    }

    /**
     * @param string $project
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setProject(string $project)
    {
        Assert::stringNotEmpty($project);

        $this->_project = $project;

        return $this;
    }

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getAssignee(): string
    {
        if ($this->_assignee === null) {
            throw NotDefinedException::forVariable(__CLASS__.'::_assignee');
        }

        return $this->_assignee;
    }

    /**
     * @param string $assignee
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setAssignee(string $assignee)
    {
        Assert::stringNotEmpty($assignee);

        $this->_assignee = $assignee;

        return $this;
    }

    /**
     * @return array
     */
    public function getLabels(): array
    {
        return $this->_labels;
    }

    /**
     * @param array $labels
     *
     * @return $this
     */
    public function setLabels(array $labels = array())
    {
        $this->_labels = $labels;

        return $this;
    }

    /**
     * @param string $label
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function addLabel(string $label)
    {
        Assert::stringNotEmpty($label);

        if (!in_array($label, $this->_labels)) {
            $this->_labels[] = $label;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->_fields;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields = array())
    {
        $this->_fields = $fields;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * @param int|null $priority
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setPriority(int $priority = null)
    {
        Assert::nullOrIntegerish($priority);

        $this->_priority = $priority;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param int|null $type
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setType(int $type = null)
    {
        Assert::nullOrIntegerish($type);

        $this->_type = $type;

        return $this;
    }
}
