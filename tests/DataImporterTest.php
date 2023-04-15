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

namespace IQ2i\DataImporter\Tests;

use IQ2i\DataImporter\Archiver\DateTimeArchiver;
use IQ2i\DataImporter\DataImporter;
use IQ2i\DataImporter\Reader\CsvReader;
use IQ2i\DataImporter\Reader\XmlReader;
use IQ2i\DataImporter\Tests\fixtures\Dto\Book;
use IQ2i\DataImporter\Tests\fixtures\Processor\BatchProcessor;
use IQ2i\DataImporter\Tests\fixtures\Processor\ItemProcessor;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\IOException;

class DataImporterTest extends TestCase
{
    private vfsStreamDirectory $fs;

    protected function setUp(): void
    {
        $this->fs = vfsStream::setup();

        $this->fs->addChild(vfsStream::newFile('books.csv', 0o755)->withContent(\file_get_contents(__DIR__.'/fixtures/csv/books_with_headers.csv')));
        $this->fs->addChild(vfsStream::newFile('books.xml', 0o755)->withContent(\file_get_contents(__DIR__.'/fixtures/xml/books_with_xpath.xml')));
        $this->fs->addChild(vfsStream::newFile('books_unreadable.csv', 0o111)->withContent(\file_get_contents(__DIR__.'/fixtures/csv/books_with_headers.csv')));

        $this->fs->addChild(vfsStream::newDirectory('archive', 0o755));
        $this->fs->addChild(vfsStream::newDirectory('archive_unreadable', 0o111));
    }

    #[DoesNotPerformAssertions]
    public function testItemProcessor()
    {
        $dataImporter = new DataImporter(
            new CsvReader(
                $this->fs->getChild('books.csv')->url(),
                null,
                [CsvReader::CONTEXT_DELIMITER => ';']
            ),
            new ItemProcessor(),
            new DateTimeArchiver($this->fs->getChild('archive')->url())
        );

        $dataImporter->execute();
    }

    #[DoesNotPerformAssertions]
    public function testBatchProcessor()
    {
        $dataImporter = new DataImporter(
            new XmlReader(
                $this->fs->getChild('books.xml')->url(),
                null,
                [XmlReader::CONTEXT_XPATH => 'shop/catalog']
            ),
            new BatchProcessor(),
            new DateTimeArchiver($this->fs->getChild('archive')->url())
        );

        $dataImporter->execute();
    }

    #[DoesNotPerformAssertions]
    public function testWithDto()
    {
        $dataImporter = new DataImporter(
            new CsvReader(
                $this->fs->getChild('books.csv')->url(),
                Book::class,
                [CsvReader::CONTEXT_DELIMITER => ';']
            ),
            new ItemProcessor()
        );

        $dataImporter->execute();
    }

    public function testWithWrongDto()
    {
        // test exception
        $this->expectException(\InvalidArgumentException::class);

        // set up data importer
        $dataImporter = new DataImporter(
            new CsvReader(
                $this->fs->getChild('books.csv')->url(),
                'bool',
                [CsvReader::CONTEXT_DELIMITER => ';']
            ),
            new ItemProcessor()
        );

        $dataImporter->execute();
    }

    public function testWithUnreadableFile()
    {
        // test exception
        $this->expectException(\InvalidArgumentException::class);

        // set up data importer
        $dataImporter = new DataImporter(
            new CsvReader(
                $this->fs->getChild('books_unreadable.csv')->url(),
                null,
                [CsvReader::CONTEXT_DELIMITER => ';']
            ),
            new ItemProcessor()
        );

        $dataImporter->execute();
    }

    public function testWithUnreadableArchivePath()
    {
        // test exception
        $this->expectException(IOException::class);

        // set up data importer
        $dataImporter = new DataImporter(
            new CsvReader(
                $this->fs->getChild('books.csv')->url(),
                null,
                [CsvReader::CONTEXT_DELIMITER => ';']
            ),
            new ItemProcessor(),
            new DateTimeArchiver($this->fs->getChild('archive_unreadable')->url())
        );

        $dataImporter->execute();
    }
}
