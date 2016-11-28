<?php

namespace DreamCommerce\Component\BugTracker\Connector;

use DreamCommerce\Component\BugTracker\Exception\InvalidArgumentException;
use DreamCommerce\Component\BugTracker\Model\Jira\Credentials;
use DreamCommerce\Component\BugTracker\Model\Jira\Issue;

interface JiraConnectorInterface
{
    /**
     * @param Credentials $credentials
     * @param Issue       $issue
     *
     * @return \stdClass
     */
    public function createIssue(Credentials $credentials, Issue $issue);

    /**
     * @param Credentials $credentials
     * @param string      $fieldName
     * @param string      $fieldValue
     * @throws InvalidArgumentException
     * @return array
     */
    public function findIssuesByField(Credentials $credentials, $fieldName, $fieldValue);

    /**
     * @param Credentials $credentials
     * @param int         $issueId
     * @param array       $fields
     * @throws InvalidArgumentException
     * @return \stdClass
     */
    public function updateIssueFields(Credentials $credentials, $issueId, array $fields = array());

    /**
     * @param Credentials $credentials
     * @param int         $issueId
     * @throws InvalidArgumentException
     * @return \stdClass
     */
    public function getIssueTransitions(Credentials $credentials, $issueId);

    /**
     * @param Credentials $credentials
     * @param int         $issueId
     * @param int         $transition
     * @throws InvalidArgumentException
     * @return \stdClass
     */
    public function updateIssueTransition(Credentials $credentials, $issueId, $transition);
}
