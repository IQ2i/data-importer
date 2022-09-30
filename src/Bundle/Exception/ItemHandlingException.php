<?php

declare(strict_types=1);

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <loic@sapone.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter\Bundle\Exception;

use Symfony\Component\Console\Command\Command;

class ItemHandlingException extends \RuntimeException
{
    public function __construct(string $message = '', int $code = Command::FAILURE, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
