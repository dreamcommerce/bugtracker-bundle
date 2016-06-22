<?php

namespace DreamCommerce\BugTrackerBundle\Collector;

use DreamCommerce\BugTrackerBundle\Exception\ContextInterface;
use DreamCommerce\BugTrackerBundle\Exception\InvalidArgumentException;
use Psr\Log\LogLevel;

abstract class BaseCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private $_logLevels;

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
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options = array())
    {
        foreach ($options as $option => $value) {
            $camelCase = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $option))));
            $funcName = 'set'.ucfirst($option);
            if (method_exists($this, $funcName)) {
                call_user_func(array($this, $funcName), $value);
                continue;
            }

            $funcName = 'set'.$camelCase;
            if (method_exists($this, $funcName)) {
                call_user_func(array($this, $funcName), $value);
                continue;
            }

            if (property_exists($this, $option)) {
                $this->$camelCase = $value;
                continue;
            }

            if (property_exists($this, '_'.$option)) {
                $this->$camelCase = $value;
                continue;
            }

            $camelCase = lcfirst($camelCase);
            if (property_exists($this, $camelCase)) {
                $this->$camelCase = $value;
                continue;
            }

            $camelCase = '_'.$camelCase;
            if (property_exists($this, $camelCase)) {
                $this->$camelCase = $value;
                continue;
            }
        }

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
     * @return array
     */
    public function getLogLevelPriorities()
    {
        if ($this->_logLevels === null) {
            $this->_logLevels = array_flip(
                array(
                    LogLevel::DEBUG,
                    LogLevel::INFO,
                    LogLevel::NOTICE,
                    LogLevel::WARNING,
                    LogLevel::ERROR,
                    LogLevel::CRITICAL,
                    LogLevel::ALERT,
                    LogLevel::EMERGENCY,
                )
            );
        }

        return $this->_logLevels;
    }

    /**
     * @param string $level
     *
     * @return int
     */
    public function getLogLevelPriority($level)
    {
        if (is_string($level)) {
            $level = strtolower($level);
            $prioLevels = $this->getLogLevelPriorities();
            if (!isset($prioLevels[$level])) {
                throw new InvalidArgumentException('Unknown log level "'.$level.'"');
            }
            $level = $prioLevels[$level];
        } else {
            throw new InvalidArgumentException('Unsupported type of variable (expected: string; got: '.gettype($level).')');
        }

        return (int) $level;
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
