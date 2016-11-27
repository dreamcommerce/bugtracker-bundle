<?php

namespace DreamCommerce\Component\BugTracker\Model\Jira;

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
     * @var integer
     */
    private $_priority;

    /**
     * @var integer
     */
    private $_type;

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->_summary;
    }

    /**
     * @param string $summary
     * @return $this
     */
    public function setSummary($summary)
    {
        $this->_summary = $summary;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->_description = $description;

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
    public function getLabels()
    {
        return $this->_labels;
    }

    /**
     * @param array $labels
     * @return $this
     */
    public function setLabels(array $labels = array())
    {
        $this->_labels = $labels;

        return $this;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function addLabel($label)
    {
        Assert::string($label);

        if(!in_array($label, $this->_labels)) {
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
     * @return $this
     */
    public function setFields($fields)
    {
        $this->_fields = $fields;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * @param int $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->_priority = $priority;

        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param int $type
     * @return $this
     */
    public function setType($type)
    {
        $this->_type = $type;

        return $this;
    }
}