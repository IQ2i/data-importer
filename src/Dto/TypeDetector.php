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
    public static function findType(string $value): string
    {
        if (\is_numeric($value) && \str_contains($value, '.')) {
            return 'float';
        } elseif (\is_numeric($value) && !\in_array($value, ['0', '1'])) {
            return 'int';
        } elseif (\in_array($value, ['0', '1', 'true', 'false'])) {
            return 'bool';
        } else {
            return 'string';
        }
    }

    public static function resolve(array $types): string
    {
        return 1 === \count(\array_unique($types, \SORT_REGULAR)) ? $types[0] : 'string';
    }
}
