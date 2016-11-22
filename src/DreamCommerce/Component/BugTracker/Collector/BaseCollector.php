<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Exception\ContextInterface;
use DreamCommerce\Component\BugTracker\Exception\InvalidArgumentException;
use DreamCommerce\Component\BugTracker\Traits\Options;
use Psr\Log\LogLevel;

abstract class BaseCollector implements BaseCollectorInterface
{
    use Options;

    /**
     * @var bool
     */
    protected $_isLocked = false;

    /**
     * @var bool
     */
    protected $_isCollected = false;

    /**
     * @var array
     */
    protected $_exceptions = array();

    /**
     * @var array
     */
    protected $_ignoreExceptions = array();

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function hasSupportException($exc, $level = LogLevel::WARNING, array $context = array())
    {
        if (!is_object($exc)) {
            throw new InvalidArgumentException('Unsupported type of variable (expected: object; got: '.gettype($exc).')');
        }

        if (!($exc instanceof \Exception) && !(interface_exists('\Throwable') && $exc instanceof \Throwable)) {
            throw new InvalidArgumentException('Unsupported class of object (expected: \Exception|\Throwable; got: '.get_class($exc).')');
        }

        if ($this->isLocked()) {
            return false;
        }

        foreach ($this->_ignoreExceptions as $ignoredException) {
            if (is_object($ignoredException)) {
                if ($ignoredException === $exc) {
                    return false;
                }
            } elseif (is_string($ignoredException)) {
                if ($exc instanceof $ignoredException) {
                    return false;
                }
            } else {
                throw new InvalidArgumentException('Unsupported type of exception condition');
            }
        }

        if (is_array($this->_exceptions) && count($this->_exceptions) > 0) {
            foreach ($this->_exceptions as $includeException) {
                if (is_object($includeException)) {
                    if ($includeException === $exc) {
                        return true;
                    }
                } elseif (is_string($includeException)) {
                    if ($exc instanceof $includeException) {
                        return true;
                    }
                } else {
                    throw new InvalidArgumentException('Unsupported type of exception condition');
                }
            }

            return false;
        }

        $context = array_merge($context, $this->getContext($exc));

        return $this->_hasSupportException($exc, $level, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        if (!is_object($exc)) {
            throw new InvalidArgumentException('Unsupported type of variable (expected: object; got: '.gettype($exc).')');
        }

        if (!($exc instanceof \Exception) && !(interface_exists('\Throwable') && $exc instanceof \Throwable)) {
            throw new InvalidArgumentException('Unsupported class of object (expected: \Exception|\Throwable; got: '.get_class($exc).')');
        }

        $context = array_merge($context, $this->getContext($exc));

        if (!$this->hasSupportException($exc, $level, $context)) {
            return false;
        }

        return $this->_handle($exc, $level, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function isCollected()
    {
        return (bool) $this->_isCollected;
    }

    /**
     * @return array
     */
    public function getIgnoreExceptions()
    {
        return $this->_ignoreExceptions;
    }

    /**
     * @param \Error|\Exception|string $exc
     *
     * @return $this
     */
    public function addIgnoreException($exc)
    {
        $this->_ignoreExceptions[] = $exc;

        return $this;
    }

    /**
     * @param array $ignoreExceptions
     *
     * @return $this
     */
    public function setIgnoreExceptions($ignoreExceptions)
    {
        $this->_ignoreExceptions = $ignoreExceptions;

        return $this;
    }

    /**
     * @return array
     */
    public function getExceptions()
    {
        return $this->_exceptions;
    }

    /**
     * @param \Error|\Exception|string $exc
     *
     * @return $this
     */
    public function addException($exc)
    {
        $this->_exceptions[] = $exc;

        return $this;
    }

    /**
     * @param array $exceptions
     *
     * @return $this
     */
    public function setExceptions($exceptions)
    {
        $this->_exceptions = $exceptions;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return (bool) $this->_isLocked;
    }

    /**
     * @return $this
     */
    public function lock()
    {
        $this->_isLocked = true;
    }

    /**
     * @return $this
     */
    public function unlock()
    {
        $this->_isLocked = false;
    }

    /**
     * @param \Exception|\Throwable $exc
     *
     * @return array
     */
    public function getContext($exc)
    {
        if (!is_object($exc)) {
            throw new InvalidArgumentException('Unsupported type of variable (expected: object; got: '.gettype($exc).')');
        }

        if (!($exc instanceof \Exception) && !(interface_exists('\Throwable') && $exc instanceof \Throwable)) {
            throw new InvalidArgumentException('Unsupported class of object (expected: \Exception|\Throwable; got: '.get_class($exc).')');
        }

        $context = array();
        if ($exc instanceof ContextInterface) {
            $context = $exc->getExceptionContext();
        }

        return array_merge(
            $context,
            array(
                'message' => $exc->getMessage(),
                'code' => $exc->getCode(),
                'line' => $exc->getLine(),
                'file' => $exc->getFile(),
            )
        );
    }

    /**
     * @param \Error|\Exception $exc
     * @param string            $level
     * @param array             $context
     *
     * @return bool
     */
    protected function _hasSupportException($exc, $level = LogLevel::WARNING, array $context = array())
    {
        return true;
    }

    /**
     * @param \Error|\Exception $exc
     * @param string            $level
     * @param array             $context
     *
     * @return bool
     */
    abstract protected function _handle($exc, $level = LogLevel::WARNING, array $context = array());
}
