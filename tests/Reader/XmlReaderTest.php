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

use IQ2i\DataImporter\Reader\XmlReader;
use PHPUnit\Framework\TestCase;

class XmlReaderTest extends TestCase
{
    public function testReadXmlWithUnreadableFile()
    {
        // test exception
        $this->expectException(\InvalidArgumentException::class);

        // init reader
        new XmlReader(
            __DIR__.'/../fixtures/xml/books_with_wrong_path.xml',
            null,
            [XmlReader::CONTEXT_XPATH => 'shop/catalog']
        );
    }

    public function testReadXmlWithXpath()
    {
        // init reader
        $reader = new XmlReader(
            __DIR__.'/../fixtures/xml/books_with_xpath.xml',
            null,
            [XmlReader::CONTEXT_XPATH => 'shop/catalog']
        );

        // test denormalization
        $this->assertFalse($reader->isDenormalizable());

        // test count
        $this->assertCount(2, $reader);

        // test index
        $this->assertEquals(1, $reader->index());
        $this->assertEquals('book', $reader->key());

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

        // test content
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

    public function testReadXmlWithoutXpath()
    {
        // init reader
        $reader = new XmlReader(
            __DIR__.'/../fixtures/xml/books_without_xpath.xml',
            null
        );

        // test denormalization
        $this->assertFalse($reader->isDenormalizable());

        // test count
        $this->assertCount(2, $reader);

        // test index
        $this->assertEquals(1, $reader->index());
        $this->assertEquals('book', $reader->key());

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

        // test content
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

    public function testReadXmlWithIncorrectRootXpath()
    {
        // test exception
        $this->expectException(\InvalidArgumentException::class);

        // init reader
        new XmlReader(
            __DIR__.'/../fixtures/xml/books_with_xpath.xml',
            null,
            [XmlReader::CONTEXT_XPATH => 'foo']
        );
    }

    public function testReadXmlWithIncorrectXpath()
    {
        // test exception
        $this->expectException(\InvalidArgumentException::class);

        // init reader
        new XmlReader(
            __DIR__.'/../fixtures/xml/books_with_xpath.xml',
            null,
            [XmlReader::CONTEXT_XPATH => 'shop/foo']
        );
    }

    public function testReadXmlWithAttributePredicate()
    {
        // init reader
        $reader = new XmlReader(
            __DIR__.'/../fixtures/xml/books_with_attribute_xpath.xml',
            null,
            [XmlReader::CONTEXT_XPATH => "shop/catalog[@type='books']"]
        );

        // only the books catalog is selected, not the music one
        $this->assertCount(2, $reader);

        // test index
        $this->assertEquals(1, $reader->index());

        // test content of first item
        $this->assertEquals(
            ['author', 'title', 'genre', 'price', 'description'],
            \array_keys($reader->current())
        );

        // test second item
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

        // test end of file
        $reader->next();
        $this->assertEquals([], $reader->current());
    }

    public function testReadXmlWithNamespace()
    {
        // init reader
        $reader = new XmlReader(
            __DIR__.'/../fixtures/xml/books_with_namespace.xml',
            null,
            [
                XmlReader::CONTEXT_XPATH => 'shop/bk:catalog',
                XmlReader::CONTEXT_NAMESPACES => ['bk' => 'http://example.com/books'],
            ]
        );

        // both books are found despite the namespace on the container element
        $this->assertCount(2, $reader);

        // test index
        $this->assertEquals(1, $reader->index());

        // test content of first item
        $this->assertEquals(
            ['author', 'title', 'genre', 'price', 'description'],
            \array_keys($reader->current())
        );

        // test second item
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
    }
}
