<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

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
     * @var string
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
    public function setToken(string $token = null)
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
    public function setCounter(int $counter = 0)
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
    public function setMessage(string $message)
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
    public function setCode(int $code)
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
    public function setLine(int $line)
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
    public function setFile(string $file)
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
    public function setTrace(string $trace)
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
    public function setLevel(string $level)
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
