<?php

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

class BookImportCommand extends AbstractImportCommand
{
    protected static $defaultName = 'app:import:book';

    protected function handleItem(): callable
    {
        return function(Message $message) {};
    }

    protected function getReader(string $filename): ReaderInterface
    {
        return new CsvReader($filename, null, [CsvReader::CONTEXT_DELIMITER => ';']);
    }
}
