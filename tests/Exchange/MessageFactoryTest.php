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

namespace IQ2i\DataImporter\Tests\Exchange;

use IQ2i\DataImporter\Exchange\Message;
use IQ2i\DataImporter\Exchange\MessageFactory;
use IQ2i\DataImporter\Reader\CsvReader;
use PHPUnit\Framework\TestCase;

class MessageFactoryTest extends TestCase
{
    public function testMessageCreation()
    {
        // init reader
        $reader = new CsvReader(__DIR__.'/../fixtures/csv/books_with_headers.csv');

        // create message from factory
        $factoryMessage = MessageFactory::create($reader, $reader->current());

        // create message manually
        $manuallyMessage = new Message(
            $reader->getFile()->getFilename(),
            $reader->getFile()->getPathname(),
            $reader->index(),
            $reader->count(),
            $reader->current()
        );

        // test correspondence
        $this->assertEquals($factoryMessage, $manuallyMessage);
    }
}
