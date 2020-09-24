<?php

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <contact@iq2i.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter\Tests\Exchange;

use IQ2i\DataImporter\Exchange\Message;
use IQ2i\DataImporter\Exchange\MessageFactory;
use IQ2i\DataImporter\Reader\CsvReader;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testMessage()
    {
        // init reader
        $reader = new CsvReader(__DIR__.'/../fixtures/csv/books_with_headers.csv');

        // create message
        $message = MessageFactory::create($reader, $reader->current());

        // test getter
        $this->assertEquals($reader->getFile()->getFilename(), $message->getFileName());
        $this->assertEquals($reader->getFile()->getPathname(), $message->getFilePath());
        $this->assertEquals($reader->index(), $message->getCurrentIteration());
        $this->assertEquals($reader->count(), $message->getTotalIteration());
        $this->assertEquals($reader->current(), $message->getData());
    }
}
