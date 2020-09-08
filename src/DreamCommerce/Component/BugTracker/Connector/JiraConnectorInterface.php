<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Connector;

use DreamCommerce\Component\BugTracker\Model\Jira\Credentials;
use DreamCommerce\Component\BugTracker\Model\Jira\Issue;
use InvalidArgumentException;

interface JiraConnectorInterface
{
    /**
     * @param Credentials $credentials
     * @param Issue       $issue
     *
     * @return array
     */
    public function createIssue(Credentials $credentials, Issue $issue): array;

    /**
     * @param Credentials $credentials
     * @param string      $fieldName
     * @param string      $fieldValue
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function findIssuesByField(Credentials $credentials, string $fieldName, string $fieldValue): array;

    /**
     * @param Credentials $credentials
     * @param int         $issueId
     * @param array       $fields
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function updateIssueFields(Credentials $credentials, int $issueId, array $fields = array()): array;

    /**
     * @param Credentials $credentials
     * @param int         $issueId
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function getIssueTransitions(Credentials $credentials, int $issueId): array;

    /**
     * @param Credentials $credentials
     * @param int         $issueId
     * @param int         $transition
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function updateIssueTransition(Credentials $credentials, int $issueId, int $transition): array;
}
