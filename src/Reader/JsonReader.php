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

    /**
     * {@inheritdoc}
     */
    public function getDto(): ?string
    {
        return $this->dto;
    }

    /**
     * {@inheritdoc}
     */
    public function isDenormalizable(): bool
    {
        return null !== $this->dto;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function index()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function current(): array
    {
        if (!$this->valid()) {
            return [];
        }

        return $this->iterator->current();
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        $this->iterator->next();
        ++$this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function key(): mixed
    {
        return $this->iterator->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return $this->iterator->count();
    }
}
