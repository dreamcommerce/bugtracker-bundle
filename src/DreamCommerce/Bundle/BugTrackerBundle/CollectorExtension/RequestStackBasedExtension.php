<?php
namespace DreamCommerce\Bundle\BugTrackerBundle\CollectorExtension;


use Symfony\Component\HttpFoundation\RequestStack;

abstract class RequestStackBasedExtension
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