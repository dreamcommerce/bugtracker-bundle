<?php

namespace DreamCommerce\BugTrackerBundle\Jira;

class OperationResult
{
    const STATUS_OK = 1;
    const STATUS_ERROR = 0;

    /**
     * @var int
     */
    private $_status;

    /**
     * @var array
     */
    private $_errors;

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->_status = $status;

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @param string $error
     * @return $this
     */
    public function addError($error)
    {
        $this->_errors[] = $error;

        return $this;
    }

    /**
     * @param array $errors
     *
     * @return $this
     */
    public function setErrors($errors)
    {
        $this->_errors = $errors;

        return $this;
    }
}
