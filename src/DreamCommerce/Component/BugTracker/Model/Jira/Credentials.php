<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Model\Jira;

use DreamCommerce\Component\BugTracker\Exception\NotDefinedException;
use DreamCommerce\Component\Common\Model\ArrayableInterface;
use DreamCommerce\Component\Common\Model\ArrayableTrait;
use Webmozart\Assert\Assert;

final class Credentials implements ArrayableInterface
{
    use ArrayableTrait;

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
        $this->fromArray($options);
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
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
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
