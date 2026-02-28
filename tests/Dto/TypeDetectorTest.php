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

use IQ2i\DataImporter\Dto\TypeDetector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TypeDetectorTest extends TestCase
{
    #[DataProvider('findTypeProvider')]
    public function testFindType(string $value, string $expectedType): void
    {
        $this->assertSame($expectedType, TypeDetector::findType($value));
    }

    public static function findTypeProvider(): array
    {
        return [
            // float
            'float decimal' => ['3.14', 'float'],
            'float negative' => ['-1.5', 'float'],
            'float zero' => ['0.0', 'float'],

            // int
            'int positive' => ['42', 'int'],
            'int negative' => ['-7', 'int'],
            'int large' => ['1000000', 'int'],

            // bool
            'bool zero string' => ['0', 'bool'],
            'bool one string' => ['1', 'bool'],
            'bool true string' => ['true', 'bool'],
            'bool false string' => ['false', 'bool'],

            // date
            'date Y-m-d' => ['2024-01-15', \DateTimeImmutable::class],
            'datetime with space' => ['2024-01-15 10:30:00', \DateTimeImmutable::class],
            'datetime ISO T' => ['2024-01-15T10:30:00', \DateTimeImmutable::class],
            'datetime with timezone' => ['2024-01-15T10:30:00+02:00', \DateTimeImmutable::class],

            // string
            'plain string' => ['hello', 'string'],
            'ambiguous date-like' => ['2024-99-99', 'string'],
            'empty string' => ['', 'string'],
            'uuid-like' => ['550e8400-e29b-41d4-a716-446655440000', 'string'],
        ];
    }

    #[DataProvider('resolveProvider')]
    public function testResolve(array $types, string $expectedType): void
    {
        $this->assertSame($expectedType, TypeDetector::resolve($types));
    }

    public static function resolveProvider(): array
    {
        return [
            'all same string' => [['string', 'string', 'string'], 'string'],
            'all same int' => [['int', 'int', 'int'], 'int'],
            'all same float' => [['float', 'float', 'float'], 'float'],
            'all same bool' => [['bool', 'bool', 'bool'], 'bool'],
            'all same date' => [[\DateTimeImmutable::class, \DateTimeImmutable::class], \DateTimeImmutable::class],

            // int + float widens to float
            'int and float' => [['int', 'float'], 'float'],
            'float and int' => [['float', 'int', 'int'], 'float'],

            // any other conflict falls back to string
            'int and string' => [['int', 'string'], 'string'],
            'float and string' => [['float', 'string'], 'string'],
            'bool and int' => [['bool', 'int'], 'string'],
            'date and string' => [[\DateTimeImmutable::class, 'string'], 'string'],
        ];
    }
}
