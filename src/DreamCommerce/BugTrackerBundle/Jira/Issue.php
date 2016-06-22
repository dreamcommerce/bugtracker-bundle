<?php

namespace DreamCommerce\BugTrackerBundle\Jira;

use DreamCommerce\BugTrackerBundle\Traits\Options;

class Issue
{
    use Options;

    /**
     * @var int
     */
    private $_issueId;

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
    private $_hash;

    /**
     * @var array
     */
    private $_labels;

    /**
     * @var string
     */
    private $_type;

    /**
     * @var string
     */
    private $_priority;

    /**
     * @var array
     */
    private $_fields;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * @return int
     */
    public function getIssueId()
    {
        return $this->_issueId;
    }

    /**
     * @param int $issueId
     */
    public function setIssueId($issueId)
    {
        $this->_issueId = $issueId;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->_summary;
    }

    /**
     * @param string $summary
     */
    public function setSummary($summary)
    {
        $this->_summary = $summary;
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
     */
    public function setDescription($description)
    {
        $this->_description = $description;
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
     */
    public function setLabels($labels)
    {
        $this->_labels = $labels;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * @param string $priority
     */
    public function setPriority($priority)
    {
        $this->_priority = $priority;
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
     */
    public function setFields($fields)
    {
        $this->_fields = $fields;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->_hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->_hash = $hash;
    }
}