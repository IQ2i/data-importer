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
        $fs = vfsStream::setup();

        // classic folder
        $fs->addChild(vfsStream::newDirectory('tmp1', 0755));
        $fs->getChild('tmp1')->addChild(vfsStream::newFile('books.csv')->withContent(file_get_contents(__DIR__.'/fixtures/csv/books_with_headers.csv')));

        // empty folder
        $fs->addChild(vfsStream::newDirectory('tmp2', 0755));

        // folder with unreadable file
        $fs->addChild(vfsStream::newDirectory('tmp3', 0755));
        $fs->getChild('tmp3')->addChild(vfsStream::newFile('books.csv', 0111)->withContent(file_get_contents(__DIR__.'/fixtures/csv/books_with_headers.csv')));

        $this->fs = $fs;
    }

    private static function setupDataImporter(): DataImporter
    {
        $csvReader = new CsvReader([CsvReader::DELIMITER_KEY => ';']);
        $csvReader->setDto(Book::class);

        return new DataImporter(
            $csvReader,
            new TestProcessor()
        );
    }

    public function testExecute()
    {
        // get new data importer
        $dataImporter = self::setupDataImporter();

        $this->assertNotFalse($dataImporter->execute($this->fs->getChild('tmp1')->url()));
    }

    public function testInvalidRegexError()
    {
        // test exception
        $this->expectException(\InvalidArgumentException::class);

        // get new data importer
        $dataImporter = self::setupDataImporter();

        $dataImporter->execute($this->fs->getChild('tmp1')->url(), '..\test');
    }

    public function testUnknownFolder()
    {
        // test exception
        $this->expectException(\InvalidArgumentException::class);

        // get new data importer
        $dataImporter = self::setupDataImporter();

        $dataImporter->execute($this->fs->getChild('tmp1')->url().'test');
    }

    public function testEmptyFolder()
    {
        // get new data importer
        $dataImporter = self::setupDataImporter();

        $this->assertFalse($dataImporter->execute($this->fs->getChild('tmp2')->url()));
    }

    public function testWithUnreadableFiles()
    {
        // test exception
        $this->expectException(\InvalidArgumentException::class);

        // get new data importer
        $dataImporter = self::setupDataImporter();

        $dataImporter->execute($this->fs->getChild('tmp3')->url());
    }
}
