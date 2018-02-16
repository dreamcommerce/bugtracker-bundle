<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Tests\BugTrackerBundle;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\DreamCommerceBugTrackerExtension;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DreamCommerceBugTrackerBundleTest extends WebTestCase
{
    public function testInitServices()
    {
        /** @var ContainerInterface $container */
        $container = self::createClient()->getContainer();

        $services = array(
            'bug_tracker',
            DreamCommerceBugTrackerExtension::ALIAS . '.collector_queue',
            DreamCommerceBugTrackerExtension::ALIAS . '.symfony_handler',
            DreamCommerceBugTrackerExtension::ALIAS . '.token_generator',
            DreamCommerceBugTrackerExtension::ALIAS . '.jira_collector',
            DreamCommerceBugTrackerExtension::ALIAS . '.psr3_collector',
        );

        foreach ($services as $id) {
            $container->get($id);
        }
    }
}
