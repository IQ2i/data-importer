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

namespace IQ2i\DataImporter\Reader;

interface ReaderInterface extends \Iterator, \Countable
{
    public function getDto(): ?string;

    public function isDenormalizable(): bool;

    public function getFile(): \SplFileInfo;

    public function index(): mixed;

    public function current(): array;

    public function next(): void;

    public function key(): mixed;

    public function valid(): bool;

    public function rewind(): void;

    public function count(): int;
}
