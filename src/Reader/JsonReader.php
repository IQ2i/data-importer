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

use JsonMachine\Items;
use JsonMachine\JsonDecoder\ExtJsonDecoder;

class JsonReader implements ReaderInterface
{
    /**
     * @var string
     */
    final public const POINTER = 'pointer';

    private readonly \SplFileInfo $file;

    private readonly \Generator $iterator;

    private int $index = 1;

    private int $count = 0;

    private array $defaultContext = [
        self::POINTER => null,
    ];

    public function __construct(
        string $filePath,
        private readonly ?string $dto = null,
        array $defaultContext = [],
    ) {
        $this->file = new \SplFileInfo($filePath);

        if (!$this->file->isReadable()) {
            throw new \InvalidArgumentException('The file '.$this->file->getFilename().' is not readable.');
        }

        $this->defaultContext = \array_merge($this->defaultContext, $defaultContext);
        $options = [
            'decoder' => new ExtJsonDecoder(true),
        ];

        if (null !== $this->defaultContext[self::POINTER]) {
            $options['pointer'] = $this->defaultContext[self::POINTER];
        }

        $items = Items::fromFile($this->file->getRealPath(), $options);

        $this->iterator = $items->getIterator();
        $this->count = \iterator_count($this->iterator);

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
        return $this->count;
    }
}
