<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\BugHandler;
use DreamCommerce\Component\BugTracker\Exception\ContextInterface;
use DreamCommerce\Component\BugTracker\Exception\NotDefinedException;
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
    private $_locked = false;

    /**
     * @var bool
     */
    private $_collected = false;

    /**
     * @var array
     */
    private $_exceptions = array();

    /**
     * @var array
     */
    private $_ignoreExceptions = array();

    /**
     * @var bool
     */
    private $_useToken = false;

    /**
     * @var TokenGeneratorInterface|null
     */
    private $_tokenGenerator;

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
    public function hasSupportException($exception, $level = LogLevel::WARNING, array $context = array())
    {
        Assert::object($exception);
        if (!($exception instanceof \Exception) && !(interface_exists('\Throwable') && $exception instanceof \Throwable)) {
            throw new \InvalidArgumentException('Unsupported class of object (expected: \Exception|\Throwable)');
        }

        Assert::string($level);
        $level = strtolower($level);
        Assert::oneOf($level, BugHandler::getSupportedLogLevels());

        if ($this->isLocked()) {
            return false;
        }

        foreach ($this->getIgnoreExceptions() as $ignoredException) {
            if (is_object($ignoredException)) {
                if ($ignoredException === $exception) {
                    return false;
                }
                $ignoredException = get_class($ignoredException);
            }

            Assert::string($ignoredException);

            if ($exception instanceof $ignoredException) {
                return false;
            }
        }

        $exceptions = $this->getExceptions();
        if (is_array($exceptions) && count($exceptions) > 0) {
            foreach ($exceptions as $includeException) {
                if (is_object($includeException)) {
                    if ($includeException === $exception) {
                        return true;
                    }
                    $includeException = get_class($includeException);
                }

                Assert::string($includeException);

                if ($exception instanceof $includeException) {
                    return true;
                }
            }

            return false;
        }

        $context = array_merge($context, $this->getContext($exception));

        return $this->_hasSupportException($exception, $level, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function handle($exception, $level = LogLevel::WARNING, array $context = array())
    {
        Assert::object($exception);
        if (!($exception instanceof \Exception) && !(interface_exists('\Throwable') && $exception instanceof \Throwable)) {
            throw new \InvalidArgumentException('Unsupported class of object (expected: \Exception|\Throwable)');
        }

        Assert::string($level);
        $level = strtolower($level);
        Assert::oneOf($level, BugHandler::getSupportedLogLevels());

        $context = array_merge($context, $this->getContext($exception));

        if (!$this->hasSupportException($exception, $level, $context)) {
            return false;
        }

        $this->_handle($exception, $level, $context);
        $this->setIsCollected(true);
    }

    /**
     * {@inheritdoc}
     */
    public function isCollected()
    {
        return $this->_collected;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsCollected($isCollected)
    {
        Assert::boolean($isCollected);

        $this->_collected = (bool) $isCollected;

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
    public function addIgnoreException($exception)
    {
        if (!is_string($exception) && (!($exception instanceof \Exception) && !(interface_exists('\Throwable') && $exception instanceof \Throwable))) {
            throw new \InvalidArgumentException('Unsupported class of object (expected: \Exception|\Throwable|string)');
        }

        $this->_ignoreExceptions[] = $exception;

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
    public function addException($exception)
    {
        if (!is_string($exception) && (!($exception instanceof \Exception) && !(interface_exists('\Throwable') && $exception instanceof \Throwable))) {
            throw new \InvalidArgumentException('Unsupported class of object (expected: \Exception|\Throwable|string)');
        }

        $this->_exceptions[] = $exception;

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
        if ($this->_tokenGenerator === null) {
            throw new NotDefinedException(__CLASS__.'::_tokenGenerator');
        }

        return $this->_tokenGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenGenerator(TokenGeneratorInterface $tokenGenerator)
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
    public function getContext($exception)
    {
        Assert::object($exception);
        if (!($exception instanceof \Exception) && !(interface_exists('\Throwable') && $exception instanceof \Throwable)) {
            throw new \InvalidArgumentException('Unsupported class of object (expected: \Exception|\Throwable)');
        }

        $context = array();
        if ($exception instanceof ContextInterface) {
            $context = $exception->getExceptionContext();
        }

        return $context;
    }

    /**
     * @param \Throwable|\Exception $exception
     * @param string                $level
     * @param array                 $context
     *
     * @return bool
     */
    protected function _hasSupportException($exception, $level = LogLevel::WARNING, array $context = array())
    {
        return true;
    }

    /**
     * @param \Throwable|\Exception $exception
     * @param string                $level
     * @param array                 $context
     */
    abstract protected function _handle($exception, $level = LogLevel::WARNING, array $context = array());
}
