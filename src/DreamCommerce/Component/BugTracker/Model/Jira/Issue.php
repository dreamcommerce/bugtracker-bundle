<?php

namespace DreamCommerce\Component\BugTracker\Model\Jira;

use DreamCommerce\Component\BugTracker\Exception\NotDefinedException;
use DreamCommerce\Component\BugTracker\Traits\Options;
use Webmozart\Assert\Assert;

final class Issue
{
    use Options;

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
        $this->setOptions($options);
    }

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getSummary()
    {
        if ($this->_summary === null) {
            throw new NotDefinedException(__CLASS__.'::_summary');
        }

        return $this->_summary;
    }

    /**
     * @param string $summary
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setSummary($summary)
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
    public function getDescription()
    {
        if ($this->_description === null) {
            throw new NotDefinedException(__CLASS__.'::_description');
        }

        return $this->_description;
    }

    /**
     * @param string $description
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setDescription($description)
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
    public function getProject()
    {
        if ($this->_project === null) {
            throw new NotDefinedException(__CLASS__.'::_project');
        }

        return $this->_project;
    }

    /**
     * @param string $project
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setProject($project)
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
    public function getAssignee()
    {
        if ($this->_assignee === null) {
            throw new NotDefinedException(__CLASS__.'::_assignee');
        }

        return $this->_assignee;
    }

    /**
     * @param string $assignee
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setAssignee($assignee)
    {
        Assert::stringNotEmpty($assignee);

        $this->_assignee = $assignee;

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
     * @throws \InvalidArgumentException
     *
     * @return $this
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
     * @return array
     */
    public function getFields()
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
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setPriority($priority = null)
    {
        Assert::nullOrIntegerish($priority);

        $this->_priority = $priority;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param string|null $type
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setType($type = null)
    {
        Assert::nullOrString($type);

        $this->_type = $type;

        return $this;
    }
}
