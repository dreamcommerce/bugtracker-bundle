<?php
namespace DreamCommerce\Bundle\BugTrackerBundle\CollectorExtension;


use DreamCommerce\Component\BugTracker\CollectorExtension\ContextCollectorExtensionInterface;
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
            return ['c'];
        }

        return [
            'user_info' => $token->__toString()
        ];
    }
}