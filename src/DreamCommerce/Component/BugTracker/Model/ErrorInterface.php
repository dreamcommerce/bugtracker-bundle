<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Model;

interface ErrorInterface
{
    /**
     * @return null|string
     */
    public function getToken();

    /**
     * @param null|string $token
     *
     * @return $this
     */
    public function setToken(string $token = null);

    /**
     * @return int|null
     */
    public function getCounter();

    /**
     * @param int $counter
     *
     * @return $this
     */
    public function setCounter(int $counter = 0);

    /**
     * @return string|null
     */
    public function getMessage();

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage(string $message);

    /**
     * @return int|null
     */
    public function getCode();

    /**
     * @param int $code
     *
     * @return $this
     */
    public function setCode(int $code);

    /**
     * @return int|null
     */
    public function getLine();

    /**
     * @param int $line
     *
     * @return $this
     */
    public function setLine(int $line);

    /**
     * @return string|null
     */
    public function getFile();

    /**
     * @param string $file
     *
     * @return $this
     */
    public function setFile(string $file);

    /**
     * @return string|null
     */
    public function getTrace();

    /**
     * @param string $trace
     *
     * @return $this
     */
    public function setTrace(string $trace);

    /**
     * @return string|null
     */
    public function getLevel();

    /**
     * @param string $level
     *
     * @return $this
     */
    public function setLevel(string $level);

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
