<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Repository;

use Doctrine\Persistence\ObjectRepository;
use DreamCommerce\Component\BugTracker\Model\ErrorInterface;

interface ErrorRepositoryInterface extends ObjectRepository
{
    /**
     * @param string $token
     *
     * @return null|ErrorInterface
     */
    public function findByToken(string $token);

    /**
     * @param ErrorInterface $entity
     *
     * @return $this
     */
    public function incrementCounter(ErrorInterface $entity);
}
