<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\Common\Exception\NotDefinedException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;

class Psr3Collector extends BaseCollector implements Psr3CollectorInterface
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var bool
     */
    protected $_formatException = true;

    /**
     * {@inheritdoc}
     */
    protected function _handle(Throwable $exception, string $level = LogLevel::WARNING, array $context = array())
    {
        $token = null;
        if ($this->isUseToken()) {
            $token = $this->getTokenGenerator()->generate($exception, $level, $context);
        }

        if ($this->_formatException) {
            $message = '';
            if ($this->isUseToken()) {
                $message .= '[ '.$token.' ] ';
            }
            $exception = $message."exception '".get_class($exception)."' with message '".$exception->getMessage()."' in '".$exception->getFile().':'.$exception->getLine().' Stack trace: '.$exception->getTraceAsString();
        } elseif ($this->isUseToken()) {
            $context['token'] = $token;
        }

        $logger = $this->getLogger();
        $logger->log($level, $exception, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getLogger(): LoggerInterface
    {
        if ($this->_logger === null) {
            throw NotDefinedException::forVariable(__CLASS__.'::_logger');
        }

        return $this->_logger;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isFormatException(): bool
    {
        return $this->_formatException;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormatException(bool $formatException)
    {
        $this->_formatException = $formatException;

        return $this;
    }
}
