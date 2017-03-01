<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use Doctrine\Common\Persistence\ObjectManager;
use DreamCommerce\Component\BugTracker\Model\ErrorInterface;
use DreamCommerce\Component\BugTracker\Repository\ErrorRepositoryInterface;
use DreamCommerce\Component\Common\Exception\NotDefinedException;
use Psr\Log\LogLevel;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Throwable;
use Webmozart\Assert\Assert;

class DoctrineCollector extends BaseCollector implements DoctrineCollectorInterface
{
    /**
     * @var string
     */
    protected $_model;

    /**
     * @var FactoryInterface
     */
    protected $_modelFactory;

    /**
     * @var ObjectManager
     */
    protected $_persistManager;

    /**
     * @var bool
     */
    protected $_useCounter = true;

    /**
     * @var int|null
     */
    protected $_counterMaxValue;

    /**
     * {@inheritdoc}
     */
    protected function _handle(Throwable $exception, string $level = LogLevel::WARNING, array $context = array())
    {
        $model = $this->getModel();
        $persistManager = $this->getPersistManager();
        /** @var ErrorRepositoryInterface $repository */
        $repository = $persistManager->getRepository($model);

        $object = null;
        $token = null;

        if ($this->isUseToken()) {
            $token = $this->getTokenGenerator()->generate($exception, $level, $context);
            $object = $repository->findByToken($token);
        }

        if ($object !== null) {
            $maxValue = $this->getCounterMaxValue();
            if ($this->isUseCounter() && ($maxValue === null || $object->getCounter() < $maxValue)) {
                $repository->incrementCounter($object);
            }
        } else {
            /** @var ErrorInterface $object */
            if ($this->_modelFactory === null) {
                $object = new $model();
            } else {
                $modelFactory = $this->getModelFactory();
                $object = $modelFactory->createNew();
                if ($object instanceof $model) {
                    throw new \Exception(); // TODO
                }
            }

            $this->_fillModel($object, $exception, $level, $context);
            if ($token !== null) {
                $object->setToken($token);
            }
        }

        $persistManager->persist($object);
        $persistManager->flush();
    }

    /**
     * @param ErrorInterface        $entity
     * @param Throwable $exception
     * @param string                $level
     * @param array                 $context
     */
    protected function _fillModel(ErrorInterface $entity, Throwable $exception, string $level = LogLevel::WARNING, array $context = array())
    {
        $entity->setMessage($exception->getMessage())
            ->setCode($exception->getCode())
            ->setFile($exception->getFile())
            ->setLine($exception->getLine())
            ->setTrace($exception->getTraceAsString())
            ->setContext((array) $this->_prepareContext($context))
            ->setLevel($level);
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistManager(): ObjectManager
    {
        if ($this->_persistManager === null) {
            throw NotDefinedException::forVariable(__CLASS__.'::_persistManager');
        }

        return $this->_persistManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setPersistManager(ObjectManager $persistManager)
    {
        $this->_persistManager = $persistManager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModel(): string
    {
        if ($this->_model === null) {
            throw NotDefinedException::forVariable(__CLASS__.'::_model');
        }

        return $this->_model;
    }

    /**
     * {@inheritdoc}
     */
    public function setModel(string $model)
    {
        Assert::implementsInterface($model, ErrorInterface::class);

        $this->_model = $model;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModelFactory(): FactoryInterface
    {
        if ($this->_modelFactory === null) {
            throw NotDefinedException::forVariable(__CLASS__.'::_modelFactory');
        }

        return $this->_modelFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function setModelFactory(FactoryInterface $modelFactory)
    {
        $this->_modelFactory = $modelFactory;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isUseCounter(): bool
    {
        return $this->_useCounter;
    }

    /**
     * {@inheritdoc}
     */
    public function setUseCounter(bool $useCounter)
    {
        $this->_useCounter = $useCounter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCounterMaxValue()
    {
        return $this->_counterMaxValue;
    }

    /**
     * {@inheritdoc}
     */
    public function setCounterMaxValue(int $counterMaxValue = null)
    {
        $this->_counterMaxValue = $counterMaxValue;

        return $this;
    }

    protected function _prepareContext(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_object($value)) {
                $array[$key] = '<'.get_class($value).'>';
            } elseif (is_array($value)) {
                $array[$key] = static::_prepareContext($array[$key]);
            } else {
                $array[$key] = (string) $value;
            }
        }

        return $array;
    }
}
