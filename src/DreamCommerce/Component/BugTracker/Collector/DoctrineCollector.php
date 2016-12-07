<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use Doctrine\ORM\EntityManagerInterface;
use DreamCommerce\Component\BugTracker\BugHandler;
use DreamCommerce\Component\BugTracker\Exception\NotDefinedException;
use DreamCommerce\Component\BugTracker\Model\ErrorInterface;
use DreamCommerce\Component\BugTracker\Repository\ErrorRepositoryInterface;
use Psr\Log\LogLevel;
use Webmozart\Assert\Assert;

class DoctrineCollector extends BaseCollector implements DoctrineCollectorInterface
{
    /**
     * @var string
     */
    protected $_model;

    /**
     * @var EntityManagerInterface
     */
    protected $_entityManager;

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
    protected function _handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        $entityManager = $this->getEntityManager();
        $model = $this->getModel();
        /** @var ErrorRepositoryInterface $repository */
        $repository = $entityManager->getRepository($model);

        $entity = null;
        $token = null;

        if ($this->isUseToken()) {
            $token = $this->getTokenGenerator()->generate($exc, $level, $context);
            $entity = $repository->findByToken($token);
        }

        if ($entity !== null) {
            $maxValue = $this->getCounterMaxValue();
            if ($this->isUseCounter() && ($maxValue === null || $entity->getCounter() < $maxValue)) {
                $repository->incrementCounter($entity);
            }
        } else {
            /** @var ErrorInterface $entity */
            $entity = new $model();
            $this->_fillModel($entity, $exc, $level, $context);
            if ($token !== null) {
                $entity->setToken($token);
            }
        }

        $entityManager->persist($entity);
        $entityManager->flush();
    }

    /**
     * @param ErrorInterface        $entity
     * @param \Throwable|\Exception $exc
     * @param string                $level
     * @param array                 $context
     */
    protected function _fillModel(ErrorInterface $entity, $exc, $level = LogLevel::WARNING, array $context = array())
    {
        $levelPriority = BugHandler::getLogLevelPriority($level);

        $entity->setMessage($exc->getMessage())
            ->setCode($exc->getCode())
            ->setFile($exc->getFile())
            ->setLine($exc->getLine())
            ->setTrace($exc->getTraceAsString())
            ->setContext((array) $this->_prepareContext($context))
            ->setLevel($levelPriority);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManager()
    {
        if ($this->_entityManager === null) {
            throw new NotDefinedException(__CLASS__.'::_entityManager');
        }

        return $this->_entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->_entityManager = $entityManager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        if ($this->_model === null) {
            throw new NotDefinedException(__CLASS__.'::_model');
        }

        return $this->_model;
    }

    /**
     * {@inheritdoc}
     */
    public function setModel($model)
    {
        Assert::implementsInterface($model, ErrorInterface::class);

        $this->_model = $model;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isUseCounter()
    {
        return $this->_useCounter;
    }

    /**
     * {@inheritdoc}
     */
    public function setUseCounter($useCounter)
    {
        Assert::boolean($useCounter);

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
    public function setCounterMaxValue($counterMaxValue = null)
    {
        Assert::nullOrIntegerish($counterMaxValue);

        $this->_counterMaxValue = $counterMaxValue;

        return $this;
    }

    protected function _prepareContext(array $array)
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
