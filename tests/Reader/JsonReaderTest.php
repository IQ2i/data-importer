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

namespace IQ2i\DataImporter\Tests\Reader;

use IQ2i\DataImporter\Reader\JsonReader;
use PHPUnit\Framework\TestCase;

class JsonReaderTest extends TestCase
{
    public function testReadJson()
    {
        // init reader
        $reader = new JsonReader(
            __DIR__.'/../fixtures/json/books.json',
            null,
        );

        // test denormalization
        $this->assertFalse($reader->isDenormalizable());

        // test file
        $this->assertEquals(
            new \SplFileInfo(__DIR__.'/../fixtures/json/books.json'),
            $reader->getFile()
        );

        // test count
        $this->assertCount(2, $reader);

        // test index
        $this->assertEquals(1, $reader->index());
        $this->assertEquals(0, $reader->key());

        // test headers
        $this->assertEquals(
            ['author', 'title', 'genre', 'price', 'description'],
            \array_keys($reader->current())
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
                'author' => [
                    'firstname' => 'Kim',
                    'lastname' => 'Ralls',
                ],
                'title' => 'Midnight Rain',
                'genre' => 'Fantasy',
                'price' => '5.95',
                'description' => 'A former architect battles corporate zombies, an evil sorceress, and her own childhood to become queen of the world.',
            ],
            $reader->current()
        );

        // test and of file
        $reader->next();
        $this->assertEquals([], $reader->current());
    }

    public function testReadJsonWithPointer()
    {
        // init reader
        $reader = new JsonReader(
            __DIR__.'/../fixtures/json/books.json',
            null,
            [JsonReader::POINTER => '/author/books']
        );

        // test denormalization
        $this->assertFalse($reader->isDenormalizable());

        // test file
        $this->assertEquals(
            new \SplFileInfo(__DIR__.'/../fixtures/json/books_with_pointer.json'),
            $reader->getFile()
        );

        // test count
        $this->assertCount(2, $reader);

        // test index
        $this->assertEquals(1, $reader->index());
        $this->assertEquals(0, $reader->key());

        // test headers
        $this->assertEquals(
            ['title', 'genre', 'price', 'description'],
            \array_keys($reader->current())
        );
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
                'title' => 'Midnight Rain',
                'genre' => 'Fantasy',
                'price' => '5.95',
                'description' => 'A former architect battles corporate zombies, an evil sorceress, and her own childhood to become queen of the world.',
            ],
            $reader->current()
        );

        // test and of file
        $reader->next();
        $this->assertEquals([], $reader->current());
    }
}
