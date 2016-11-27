<?php

namespace DreamCommerce\Component\BugTracker\Model\Jira;

use DreamCommerce\Component\BugTracker\Exception\RuntimeException;
use DreamCommerce\Component\BugTracker\Traits\Options;

final class Credentials
{
    use Options;

    /**
     * @var string
     */
    private $_entryPoint;

    /**
     * @var string
     */
    private $_username;

    /**
     * @var string
     */
    private $_password;

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntryPoint()
    {
        if ($this->_entryPoint === null) {
            throw new RuntimeException('Entry point has been not defined');
        }

        return $this->_entryPoint;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntryPoint($entryPoint)
    {
        $this->_entryPoint = $entryPoint;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        if ($this->_username === null) {
            throw new RuntimeException('Username has been not defined');
        }

        return $this->_username;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->_username = $username;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        if ($this->_password === null) {
            throw new RuntimeException('Password has been not defined');
        }

        return $this->_password;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->_password = $password;

        return $this;
    }
}