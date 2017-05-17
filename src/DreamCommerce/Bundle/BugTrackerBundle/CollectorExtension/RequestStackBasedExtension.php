<?php
namespace DreamCommerce\Bundle\BugTrackerBundle\Collector\Extension;


use Symfony\Component\HttpFoundation\RequestStack;

abstract class RequestStackBasedExtension implements ContextCollectorExtensionInterface
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
}