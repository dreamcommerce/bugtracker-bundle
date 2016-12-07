<?php

namespace DreamCommerce\Component\BugTracker\Model;

abstract class Error implements ErrorInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $token;

    /**
     * @var int|null
     */
    protected $counter;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var int
     */
    protected $line;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var array
     */
    protected $trace;

    /**
     * @var int
     */
    protected $level;

    /**
     * @var array
     */
    protected $context;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($token = null)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * {@inheritdoc}
     */
    public function setCounter($counter = 0)
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * {@inheritdoc}
     */
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTrace()
    {
        return $this->trace;
    }

    /**
     * {@inheritdoc}
     */
    public function setTrace($trace)
    {
        $this->trace = $trace;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(array $context = array())
    {
        $this->context = $context;

        return $this;
    }
}
