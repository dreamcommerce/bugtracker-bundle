<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Exception\ContextInterface;
use DreamCommerce\Component\BugTracker\Exception\InvalidArgumentException;
use DreamCommerce\Component\BugTracker\Generator\TokenGeneratorInterface;
use DreamCommerce\Component\BugTracker\Traits\Options;
use Psr\Log\LogLevel;
use Webmozart\Assert\Assert;

abstract class BaseCollector implements BaseCollectorInterface
{
    use Options;

    /**
     * @var bool
     */
    protected $_locked = false;

    /**
     * @var bool
     */
    protected $_collected = false;

    /**
     * @var array
     */
    protected $_exceptions = array();

    /**
     * @var array
     */
    protected $_ignoreExceptions = array();

    /**
     * @var bool
     */
    protected $_useToken = false;

    /**
     * @var TokenGeneratorInterface|null
     */
    protected $_tokenGenerator;

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
                $ignoredException = get_class($ignoredException);
            }
            if (is_string($ignoredException)) {
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
                    $includeException = get_class($includeException);
                }
                if (is_string($includeException)) {
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
        return (bool) $this->_collected;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsCollected($isCollected)
    {
        Assert::boolean($isCollected);

        $this->_collected = $isCollected;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIgnoreExceptions()
    {
        return $this->_ignoreExceptions;
    }

    /**
     * {@inheritdoc}
     */
    public function addIgnoreException($exc)
    {
        $this->_ignoreExceptions[] = $exc;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIgnoreExceptions(array $ignoreExceptions = array())
    {
        $this->_ignoreExceptions = $ignoreExceptions;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExceptions()
    {
        return $this->_exceptions;
    }

    /**
     * {@inheritdoc}
     */
    public function addException($exc)
    {
        $this->_exceptions[] = $exc;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setExceptions(array $exceptions = array())
    {
        $this->_exceptions = $exceptions;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked()
    {
        return (bool) $this->_locked;
    }

    /**
     * {@inheritdoc}
     */
    public function lock()
    {
        $this->_locked = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function unlock()
    {
        $this->_locked = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenGenerator()
    {
        return $this->_tokenGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenGenerator(TokenGeneratorInterface $tokenGenerator = null)
    {
        $this->_tokenGenerator = $tokenGenerator;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isUseToken()
    {
        return $this->_useToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setUseToken($useToken)
    {
        Assert::boolean($useToken);

        $this->_useToken = $useToken;

        return $this;
    }

    /**
     * {@inheritdoc}
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
