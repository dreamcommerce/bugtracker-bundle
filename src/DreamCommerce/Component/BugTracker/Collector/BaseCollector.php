<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\BugHandler;
use DreamCommerce\Component\BugTracker\Collector\Extension\CollectorExtendable;
use DreamCommerce\Component\BugTracker\Collector\Extension\CollectorExtensionQueueInterface;
use DreamCommerce\Component\BugTracker\Generator\TokenGeneratorInterface;
use DreamCommerce\Component\Common\Exception\ContextInterface;
use DreamCommerce\Component\Common\Exception\NotDefinedException;
use DreamCommerce\Component\Common\Model\ArrayableTrait;
use InvalidArgumentException;
use Psr\Log\LogLevel;
use Throwable;
use Webmozart\Assert\Assert;

abstract class BaseCollector implements BaseCollectorInterface, CollectorExtendable
{
    use ArrayableTrait;

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
     * @var CollectorExtensionQueueInterface|null
     */
    private $_extensionChain;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->fromArray($options);
    }

    /**
     * {@inheritdoc}
     */
    public function hasSupportException(Throwable $exception, string $level = LogLevel::WARNING, array $context = array()): bool
    {
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
    public function handle(Throwable $exception, string $level = LogLevel::WARNING, array $context = array())
    {
        $level = strtolower($level);
        Assert::oneOf($level, BugHandler::getSupportedLogLevels());

        $additionalContext = ($this->_extensionChain) ? $this->_extensionChain->getAdditionalContext($exception) : [];

        $context = array_merge($context, $this->getContext($exception), $additionalContext);

        if (!$this->hasSupportException($exception, $level, $context)) {
            return false;
        }

        $this->_handle($exception, $level, $context);
        $this->setIsCollected(true);
    }

    /**
     * {@inheritdoc}
     */
    public function isCollected(): bool
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
    public function getIgnoreExceptions(): array
    {
        return $this->_ignoreExceptions;
    }

    /**
     * {@inheritdoc}
     */
    public function addIgnoreException($exception)
    {
        if (!is_string($exception) && !($exception instanceof Throwable)) {
            throw new InvalidArgumentException('Unsupported class of object (expected: Throwable|string)');
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
    public function getExceptions(): array
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
    public function isLocked(): bool
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
    public function getTokenGenerator(): TokenGeneratorInterface
    {
        if ($this->_tokenGenerator === null) {
            throw NotDefinedException::forVariable(__CLASS__.'::_tokenGenerator');
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
    public function isUseToken(): bool
    {
        return $this->_useToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setUseToken(bool $useToken)
    {
        $this->_useToken = $useToken;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext(Throwable $exception): array
    {
        $context = array();
        if ($exception instanceof ContextInterface) {
            $context = $exception->getExceptionContext();
        }

        return $context;
    }

    public function setExtensionQueue(CollectorExtensionQueueInterface $extensionChain = null)
    {
        $this->_extensionChain = $extensionChain;
    }

    /**
     * @param Throwable $exception
     * @param string                $level
     * @param array                 $context
     *
     * @return bool
     */
    protected function _hasSupportException(Throwable $exception, string $level = LogLevel::WARNING, array $context = array()): bool
    {
        return true;
    }

    /**
     * @param Throwable $exception
     * @param string                $level
     * @param array                 $context
     */
    abstract protected function _handle(Throwable $exception, string $level = LogLevel::WARNING, array $context = array());
}
