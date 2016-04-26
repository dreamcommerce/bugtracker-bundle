<?php

namespace DreamCommerce\BugTracker\Collector;

use Psr\Log\LogLevel;

abstract class BaseCollector implements CollectorInterface
{
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
        if($this->_isLocked) {
            return false;
        }

        foreach($this->_ignoreExceptions as $ignoredException) {
            if(is_object($ignoredException)) {
                if($ignoredException === $exc) {
                    return false;
                }
            } elseif(is_string($ignoredException)) {
                if($exc instanceof $ignoredException) {
                    return false;
                }
            } else {
                throw new \RuntimeException('Unsupported type of exception condition');
            }
        }

        if(is_array($this->_exceptions) && count($this->_exceptions) > 0) {
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
                    throw new \RuntimeException('Unsupported type of exception condition');
                }
            }

            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        if($this->hasSupportException($exc, $level, $context)) {
            return $this->_handle($exc, $level, $context);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCollected()
    {
        return (bool)$this->_isCollected;
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
     * @return $this
     */
    public function addIgnoreException($exc)
    {
        $this->_ignoreExceptions[] = $exc;
        return $this;
    }

    /**
     * @param array $ignoreExceptions
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
     * @return $this
     */
    public function addException($exc)
    {
        $this->_exceptions[] = $exc;
        return $this;
    }

    /**
     * @param array $exceptions
     * @return $this
     */
    public function setExceptions($exceptions)
    {
        $this->_exceptions = $exceptions;
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options = array())
    {
        foreach($options as $option => $value)
        {
            $camelCase = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $option))));
            $funcName = 'set' . ucfirst($option);
            if(method_exists($this, $funcName)) {
                call_user_func(array($this, $funcName), $value);
                continue;
            }

            $funcName = 'set' . $camelCase;
            if (method_exists($this, $funcName)) {
                call_user_func(array($this, $funcName), $value);
                continue;
            }

            if(property_exists($this, $option)) {
                $this->$camelCase = $value;
                continue;
            }

            if(property_exists($this, '_' . $option)) {
                $this->$camelCase = $value;
                continue;
            }

            $camelCase = lcfirst($camelCase);
            if(property_exists($this, $camelCase)) {
                $this->$camelCase = $value;
                continue;
            }

            $camelCase = '_' . $camelCase;
            if(property_exists($this, $camelCase)) {
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
        return (bool)$this->_isLocked;
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
     * @param \Error|\Exception $exc
     * @param string $level
     * @param array $context
     * @return bool
     */
    abstract protected function _handle($exc, $level = LogLevel::WARNING, array $context = array());
}