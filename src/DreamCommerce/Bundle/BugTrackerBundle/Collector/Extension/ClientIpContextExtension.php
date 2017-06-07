<?php
namespace DreamCommerce\Bundle\BugTrackerBundle\Collector\Extension;



use Symfony\Component\HttpFoundation\Request;

class ClientIpContextExtension extends RequestStackBasedExtension
{
    public function getAdditionalContext(\Throwable $throwable): array
    {
        /** @var Request $request */
        $request = $this->requestStack->getMasterRequest();

        if ($request === null) {
            return [];
        }

        return [
            'client_ip' => $request->getClientIps(),
        ];
    }
}