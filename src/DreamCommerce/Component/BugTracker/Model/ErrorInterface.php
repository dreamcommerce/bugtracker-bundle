<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Model;

interface ErrorInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return null|string
     */
    public function getToken();

    /**
     * @param null|string $token
     *
     * @return $this
     */
    public function setToken($token = null);

    /**
     * @return int
     */
    public function getCounter();

    /**
     * @param int $counter
     *
     * @return $this
     */
    public function setCounter($counter = 0);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message);

    /**
     * @return int
     */
    public function getCode();

    /**
     * @param int $code
     *
     * @return $this
     */
    public function setCode($code);

    /**
     * @return int
     */
    public function getLine();

    /**
     * @param int $line
     *
     * @return $this
     */
    public function setLine($line);

    /**
     * @return string
     */
    public function getFile();

    /**
     * @param string $file
     *
     * @return $this
     */
    public function setFile($file);

    /**
     * @return string
     */
    public function getTrace();

    /**
     * @param string $trace
     *
     * @return $this
     */
    public function setTrace($trace);

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @param int $level
     *
     * @return $this
     */
    public function setLevel($level);

    /**
     * @return array
     */
    public function getContext();

    /**
     * @param array $context
     *
     * @return $this
     */
    public function setContext(array $context = array());
}
