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

namespace IQ2i\DataImporter\Dto;

class TypeDetector
{
    private const DATE_FORMATS = [
        'Y-m-d',
        'Y-m-d H:i:s',
        'Y-m-d\TH:i:s',
        'Y-m-d\TH:i:sP',
    ];

    public static function findType(string $value): string
    {
        if (\is_numeric($value) && \str_contains($value, '.')) {
            return 'float';
        }

        if (\is_numeric($value) && !\in_array($value, ['0', '1'])) {
            return 'int';
        }

        if (\in_array($value, ['0', '1', 'true', 'false'])) {
            return 'bool';
        }

        if (self::isDate($value)) {
            return \DateTimeImmutable::class;
        }

        return 'string';
    }

    public static function resolve(array $types): string
    {
        $unique = \array_unique($types, \SORT_REGULAR);

        if (1 === \count($unique)) {
            return $unique[0];
        }

        if ([] === \array_diff($unique, ['int', 'float'])) {
            return 'float';
        }

        return 'string';
    }

    private static function isDate(string $value): bool
    {
        foreach (self::DATE_FORMATS as $format) {
            $date = \DateTimeImmutable::createFromFormat($format, $value);
            if (false !== $date && $date->format($format) === $value) {
                return true;
            }
        }

        return false;
    }
}
