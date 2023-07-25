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

namespace IQ2i\DataImporter\Tests\fixtures\Command;

use IQ2i\DataImporter\Bundle\Command\AbstractImportCommand;
use IQ2i\DataImporter\Exchange\Message;
use IQ2i\DataImporter\Reader\CsvReader;
use IQ2i\DataImporter\Reader\ReaderInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:import:book')]
class BookImportCommand extends AbstractImportCommand
{
    protected function handleItem(): callable
    {
        return static function (Message $message) {
        };
    }

    protected function getReader(string $filename = null): ReaderInterface
    {
        return new CsvReader($filename, null, [CsvReader::CONTEXT_DELIMITER => ';']);
    }
}
