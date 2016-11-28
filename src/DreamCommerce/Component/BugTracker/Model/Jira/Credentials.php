<?php

namespace DreamCommerce\Component\BugTracker\Model\Jira;

use DreamCommerce\Component\BugTracker\Assert;
use DreamCommerce\Component\BugTracker\Exception\InvalidArgumentException;
use DreamCommerce\Component\BugTracker\Exception\NotDefinedException;
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
     * @return string
     *
     * @throws NotDefinedException
     */
    public function getEntryPoint()
    {
        if ($this->_entryPoint === null) {
            throw new NotDefinedException(__CLASS__.'::_entryPoint');
        }

        return $this->_entryPoint;
    }

    /**
     * @param string $entryPoint
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setEntryPoint($entryPoint)
    {
        Assert::stringNotEmpty($entryPoint);

        $this->_entryPoint = $entryPoint;

        return $this;
    }

    /**
     * @return string
     *
     * @throws NotDefinedException
     */
    public function getUsername()
    {
        if ($this->_username === null) {
            throw new NotDefinedException(__CLASS__.'::_username');
        }

        return $this->_username;
    }

    /**
     * @param string $username
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setUsername($username)
    {
        Assert::stringNotEmpty($username);

        $this->_username = $username;

        return $this;
    }

    /**
     * @return string
     *
     * @throws NotDefinedException
     */
    public function getPassword()
    {
        if ($this->_password === null) {
            throw new NotDefinedException(__CLASS__.'::_password');
        }

        return $this->_password;
    }

    /**
     * @param string $password
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setPassword($password)
    {
        Assert::stringNotEmpty($password);

        $this->_password = $password;

        return $this;
    }
}
