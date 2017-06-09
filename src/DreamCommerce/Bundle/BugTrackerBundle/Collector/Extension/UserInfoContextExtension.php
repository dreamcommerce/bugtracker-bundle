<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Bundle\BugTrackerBundle\Collector\Extension;

use DreamCommerce\Component\BugTracker\Collector\Extension\ContextCollectorExtensionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserInfoContextExtension implements ContextCollectorExtensionInterface
{

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getAdditionalContext(\Throwable $throwable): array
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            return array();
        }

        return array(
            'user_info' => $token->__toString()
        );
    }
}
