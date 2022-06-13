<?php

namespace IQ2i\DataImporter\Bundle\Exception;

use Symfony\Component\Console\Command\Command;

class ItemHandlingException extends \RuntimeException
{
    public function __construct(string $message = '', int $code = Command::FAILURE, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
