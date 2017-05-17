<?php
namespace DreamCommerce\Tests\BugTrackerBundle\Collector\Extension;


use Symfony\Component\HttpFoundation\RequestStack;

interface RequestStackMockInterface
{
    /**
     * Get RequestStack with one Request inside. Request has got sample date get by function from this interface
     *
     * @return RequestStack
     */
    public function getRequestStack(): RequestStack;

    /**
     *  Get sample params for $request->query
     *
     * @return array
     */
    public function getQueryMockData(): array;

    /**
     * Get sample params for $request->request
     *
     * @return array
     */
    public function getRequestMockData(): array;

    /**
     *  Get sample params for $request->server
     *
     * @return array
     */
    public function getServerMockData(): array;
}