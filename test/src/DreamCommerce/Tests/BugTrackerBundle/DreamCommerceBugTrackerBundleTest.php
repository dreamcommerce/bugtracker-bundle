<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Tests\BugTrackerBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DreamCommerceBugTrackerBundleTest extends WebTestCase
{
    /**
     * @test
     */
    public function its_services_are_intitializable()
    {
        /** @var ContainerInterface $container */
        $container = self::createClient()->getContainer();

        $services = $container->getServiceIds();

        $services = array_filter($services, function ($serviceId) {
            return false !== strpos($serviceId, 'dream_commerce_bug_tracker');
        });

        $this->assertTrue(count($services) > 0);

        foreach ($services as $id) {
            $container->get($id);
        }
    }
}
