<?php

namespace DreamCommerce\Component\BugTracker\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use DreamCommerce\Component\BugTracker\Model\ErrorInterface;

interface ErrorRepositoryInterface extends ObjectRepository
{
    /**
     * @param string $token
     *
     * @return null|ErrorInterface
     */
    public function findByToken($token);
}
