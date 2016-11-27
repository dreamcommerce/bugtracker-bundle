<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use Doctrine\ORM\EntityManagerInterface;
use DreamCommerce\Component\BugTracker\Model\ErrorInterface;
use Psr\Log\LogLevel;
use Webmozart\Assert\Assert;

class DoctrineCollector extends BaseCollector implements DoctrineCollectorInterface
{
    /**
     * @var string
     */
    private $_model;

    /**
     * @var EntityManagerInterface
     */
    private $_entityManager;

    /**
     * {@inheritdoc}
     */
    protected function _handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        $model = $this->getModel();
        /** @var ErrorInterface $entity */
        $entity = new $model();
        $this->_fillModel($entity, $exc, $level, $context);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);
    }

    /**
     * @param ErrorInterface    $entity
     * @param \Error|\Exception $exc
     * @param string            $level
     * @param array             $context
     */
    protected function _fillModel(ErrorInterface $entity, $exc, $level = LogLevel::WARNING, array $context = array())
    {
        $entity->setMessage($exc->getMessage())
            ->setCode($exc->getCode())
            ->setFile($exc->getFile())
            ->setLine($exc->getLine())
            ->setTrace($exc->getTrace())
            ->setContext($context)
            ->setLevel($level);

        if ($this->isUseToken()) {
            $token = $this->getTokenGenerator()->generate($exc, $level, $context);
            $entity->setToken($token);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManager()
    {
        return $this->_entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityManager(EntityManagerInterface $entityManager = null)
    {
        $this->_entityManager = $entityManager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * {@inheritdoc}
     */
    public function setModel($model)
    {
        Assert::nullOrImplementsInterface($model, ErrorInterface::class);

        $this->_model = $model;

        return $this;
    }
}