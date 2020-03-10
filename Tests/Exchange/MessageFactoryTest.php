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

class MessageFactoryTest extends TestCase
{
    public function testMessageCreation()
    {
        // init file
        $file = new \SplFileInfo(__DIR__.'/../fixtures/books_with_headers.csv');

        // init reader
        $reader = new CsvReader();
        $reader->setFile($file->openFile());

        // create message from factory
        $factoryMessage = MessageFactory::create($file, $reader, $reader->current());

        // create message manually
        $manuallyMessage = new Message(
            $file->getFilename(),
            $file->getPathname(),
            $reader->index(),
            $reader->count(),
            $reader->current()
        );

        // test correspondence
        $this->assertEquals($factoryMessage, $manuallyMessage);
    }
}
