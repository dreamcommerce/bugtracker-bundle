<?php

/**
 * (c) 2017-2020 DreamCommerce
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
    public function getEntryPoint(): string
    {
        if ($this->_entryPoint === null) {
            throw NotDefinedException::forVariable(__CLASS__.'::_entryPoint');
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
    public function setEntryPoint(string $entryPoint)
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
    public function getUsername(): string
    {
        if ($this->_username === null) {
            throw NotDefinedException::forVariable(__CLASS__.'::_username');
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
    public function setUsername(string $username)
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
    public function getPassword(): string
    {
        if ($this->_password === null) {
            throw NotDefinedException::forVariable(__CLASS__.'::_password');
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
    public function setPassword(string $password)
    {
        Assert::stringNotEmpty($password);

        $this->_password = $password;

        return $this;
    }
}
