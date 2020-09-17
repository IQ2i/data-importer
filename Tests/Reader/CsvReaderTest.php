<?php

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <contact@iq2i.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter\Tests\Reader;

use IQ2i\DataImporter\Reader\CsvReader;
use PHPUnit\Framework\TestCase;

class CsvReaderTest extends TestCase
{
    public function testReadCsvFileWithHeader()
    {
        // init reader
        $reader = new CsvReader([
            CsvReader::DELIMITER_KEY => ';',
        ]);
        $reader->setFile(new \SplFileObject(__DIR__.'/../fixtures/csv/books_with_headers.csv'));

        // test default configuration
        $this->assertEquals('/.csv/', $reader->getDefaultFileRegex());

        // test denormalization
        $this->assertTrue($reader->isDenormalizable());

        // test count
        $this->assertEquals(2, count($reader));

        // test index
        $this->assertEquals(1, $reader->index());
        $this->assertEquals(1, $reader->key());

        // test headers
        $this->assertEquals(
            ['author', 'title', 'genre', 'price', 'description'],
            array_keys($reader->current())
        );
        $this->assertArrayHasKey('author', $reader->current());
        $this->assertNotNull($reader->current()['author']);
        $this->assertArrayHasKey('title', $reader->current());
        $this->assertNotNull($reader->current()['title']);
        $this->assertArrayHasKey('genre', $reader->current());
        $this->assertNotNull($reader->current()['genre']);
        $this->assertArrayHasKey('price', $reader->current());
        $this->assertNotNull($reader->current()['price']);
        $this->assertArrayHasKey('description', $reader->current());
        $this->assertNotNull($reader->current()['description']);

        // test line
        $reader->next();
        $this->assertEquals(2, $reader->index());
        $this->assertEquals(
            [
                'author'      => 'Ralls, Kim',
                'title'       => 'Midnight Rain',
                'genre'       => 'Fantasy',
                'price'       => '5.95',
                'description' => 'A former architect battles corporate zombies, an evil sorceress, and her own childhood to become queen of the world.',
            ],
            $reader->current()
        );

        // test and of file
        $reader->next();
        $this->assertEquals([], $reader->current());
    }

    public function testReadCsvWithoutHeader()
    {
        // init reader
        $reader = new CsvReader([
            CsvReader::DELIMITER_KEY  => ';',
            CsvReader::NO_HEADERS_KEY => true,
        ]);
        $reader->setFile(new \SplFileObject(__DIR__.'/../fixtures/csv/books_without_headers.csv'));

        // test default configuration
        $this->assertEquals('/.csv/', $reader->getDefaultFileRegex());

        // test denormalization
        $this->assertFalse($reader->isDenormalizable());

        // test count
        $this->assertEquals(2, count($reader));

        // test index
        $this->assertEquals(1, $reader->index());
        $this->assertEquals(0, $reader->key());

        // test headers
        $this->assertEquals(
            [0, 1, 2, 3, 4],
            array_keys($reader->current())
        );

        // test content
        $reader->next();
        $this->assertEquals(2, $reader->index());
        $this->assertEquals(
            [
                'Ralls, Kim',
                'Midnight Rain',
                'Fantasy',
                '5.95',
                'A former architect battles corporate zombies, an evil sorceress, and her own childhood to become queen of the world.',
            ],
            $reader->current()
        );

        // test and of file
        $reader->next();
        $this->assertEquals([], $reader->current());
    }
}
