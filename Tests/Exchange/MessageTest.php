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
        // init file
        $file = new \SplFileInfo(__DIR__.'/../fixtures/csv/books_with_headers.csv');

        // init reader
        $reader = new CsvReader();
        $reader->setFile($file->openFile());

        // create message
        $message = MessageFactory::create($file, $reader, $reader->current());

        // test getter
        $this->assertEquals($file->getFilename(), $message->getFileName());
        $this->assertEquals($file->getPathname(), $message->getFilePath());
        $this->assertEquals($reader->index(), $message->getCurrentIteration());
        $this->assertEquals($reader->count(), $message->getTotalIteration());
        $this->assertEquals($reader->current(), $message->getData());
    }
}
