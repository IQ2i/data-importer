<?php

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <contact@iq2i.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter\Tests;

use IQ2i\DataImporter\DataImporter;
use IQ2i\DataImporter\Reader\CsvReader;
use IQ2i\DataImporter\Tests\Dto\Book;
use IQ2i\DataImporter\Tests\Processor\TestProcessor;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class DataImporterTest extends TestCase
{
    private $fs;

    public function setUp(): void
    {
        $this->fs = vfsStream::setup();
        $this->fs->addChild(vfsStream::newFile('books.csv', 0755)->withContent(file_get_contents(__DIR__.'/fixtures/csv/books_with_headers.csv')));
        $this->fs->addChild(vfsStream::newFile('books_unreadable.csv', 0111)->withContent(file_get_contents(__DIR__.'/fixtures/csv/books_with_headers.csv')));
    }

    private static function setupDataImporter(string $filePath): DataImporter
    {
        $csvReader = new CsvReader(
            $filePath,
            [CsvReader::CONTEXT_DELIMITER => ';']
        );
        $csvReader->setDto(Book::class);

        return new DataImporter(
            $csvReader,
            new TestProcessor()
        );
    }

    public function testExecuteWithoutDto()
    {
        // set up data importer
        $csvReader = new CsvReader(
            $this->fs->getChild('books.csv')->url(),
            [CsvReader::CONTEXT_DELIMITER => ';']
        );
        $dataImporter = new DataImporter($csvReader, new TestProcessor());

        $this->assertNotFalse($dataImporter->execute());
    }

    public function testExecuteWithDto()
    {
        // set up data importer
        $csvReader = new CsvReader(
            $this->fs->getChild('books.csv')->url(),
            [CsvReader::CONTEXT_DELIMITER => ';']
        );
        $csvReader->setDto(Book::class);
        $dataImporter = new DataImporter($csvReader, new TestProcessor());

        $this->assertNotFalse($dataImporter->execute());
    }

    public function testExecuteWithUnreadableFile()
    {
        // test exception
        $this->expectException(\InvalidArgumentException::class);
        // set up data importer
        $csvReader = new CsvReader(
            $this->fs->getChild('books_unreadable.csv')->url(),
            [CsvReader::CONTEXT_DELIMITER => ';']
        );
        $dataImporter = new DataImporter($csvReader, new TestProcessor());

        $dataImporter->execute();
    }
}
