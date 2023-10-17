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

namespace IQ2i\DataImporter\Tests\Dto;

use IQ2i\DataImporter\Dto\Generator;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $generator = new Generator();
        $generatedDto = $generator->generate('Book', [
            ['name' => 'author', 'serialized_name' => null, 'type' => 'string'],
            ['name' => 'title', 'serialized_name' => null, 'type' => 'string'],
            ['name' => 'genre', 'serialized_name' => null, 'type' => 'string'],
            ['name' => 'price', 'serialized_name' => null, 'type' => 'float'],
            ['name' => 'description', 'serialized_name' => null, 'type' => 'string'],
        ], 'IQ2i\DataImporter\Tests\fixtures\Dto');

        $this->assertStringEqualsFile(__DIR__.'/../fixtures/Dto/Book.php', $generatedDto);
    }
}
