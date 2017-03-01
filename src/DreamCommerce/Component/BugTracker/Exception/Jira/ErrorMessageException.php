<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Exception\Jira;

use DreamCommerce\Component\BugTracker\Exception\JiraException;

class ErrorMessageException extends JiraException
{
    const CODE_RESPONSE_ERRORS = 10;

    /**
     * @var array
     */
    private $errors = array();

    /**
     * @var array
     */
    private $errorMessages = array();

    /**
     * @param array $errors
     * @param array $errorMessages
     * @return ErrorMessageException
     */
    public static function forResponseErrors(array $errors, array $errorMessages): ErrorMessageException
    {
        $exception = new static('The error occurred while processing data', static::CODE_RESPONSE_ERRORS);
        $exception->errors = $errors;
        $exception->errorMessages = $errorMessages;

        return $exception;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }
}
