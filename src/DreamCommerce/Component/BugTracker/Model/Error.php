<?php

namespace DreamCommerce\Component\BugTracker\Model;

class Error implements ErrorInterface
{
    /**
     * @var integer
     */
    protected $_id;

    /**
     * @var string|null
     */
    protected $_token;

    /**
     * @var string
     */
    protected $_message;

    /**
     * @var integer
     */
    protected $_code;

    /**
     * @var integer
     */
    protected $_line;

    /**
     * @var string
     */
    protected $_file;

    /**
     * @var array
     */
    protected $_trace;

    /**
     * @var integer
     */
    protected $_level;

    /**
     * @var array
     */
    protected $_context;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($token = null)
    {
        $this->_token = $token;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        $this->_message = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->_code = $code;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLine()
    {
        return $this->_line;
    }

    /**
     * {@inheritdoc}
     */
    public function setLine($line)
    {
        $this->_line = $line;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile($file)
    {
        $this->_file = $file;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTrace()
    {
        return $this->_trace;
    }

    /**
     * {@inheritdoc}
     */
    public function setTrace(array $trace = array())
    {
        $this->_trace = $trace;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return $this->_level;
    }

    /**
     * {@inheritdoc}
     */
    public function setLevel($level)
    {
        $this->_level = $level;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(array $context = array())
    {
        $this->_context = $context;

        return $this;
    }
}