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

class JsonReader implements ReaderInterface
{
    private readonly \SplFileInfo $file;

    private readonly \ArrayIterator $iterator;

    private int $index = 1;

    public function __construct(
        string $filePath,
        private readonly ?string $dto = null,
    ) {
        $this->file = new \SplFileInfo($filePath);

        if (!$this->file->isReadable()) {
            throw new \InvalidArgumentException('The file '.$this->file->getFilename().' is not readable.');
        }

        $array = \json_decode(\file_get_contents($this->file->getRealPath()), true, 512, \JSON_THROW_ON_ERROR);
        $this->iterator = new \ArrayIterator($array);

        $this->rewind();
    }

    public function getDto(): ?string
    {
        return $this->dto;
    }

    public function isDenormalizable(): bool
    {
        return null !== $this->dto;
    }

    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    public function index(): mixed
    {
        return $this->index;
    }

    public function current(): array
    {
        if (!$this->valid()) {
            return [];
        }

        return $this->iterator->current();
    }

    public function next(): void
    {
        $this->iterator->next();
        ++$this->index;
    }

    public function key(): mixed
    {
        return $this->iterator->key();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    public function count(): int
    {
        return $this->iterator->count();
    }
}
