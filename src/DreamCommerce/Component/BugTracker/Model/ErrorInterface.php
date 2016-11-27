<?php

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
     * @param array $trace
     *
     * @return $this
     */
    public function setTrace(array $trace = array());

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
