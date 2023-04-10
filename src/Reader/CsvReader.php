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

class CsvReader implements ReaderInterface
{
    /**
     * @var string
     */
    final public const CONTEXT_DELIMITER = 'csv_delimiter';

    /**
     * @var string
     */
    final public const CONTEXT_ENCLOSURE = 'csv_enclosure';

    /**
     * @var string
     */
    final public const CONTEXT_ESCAPE_CHAR = 'csv_escape_char';

    /**
     * @var string
     */
    final public const CONTEXT_HEADERS = 'csv_headers';

    /**
     * @var string
     */
    final public const CONTEXT_NO_HEADERS = 'no_headers';

    private readonly \SplFileInfo $file;

    private readonly \SplFileObject $iterator;

    private int $count = 0;

    private int $index = 1;

    private array $defaultContext = [
        self::CONTEXT_DELIMITER => ',',
        self::CONTEXT_ENCLOSURE => '"',
        self::CONTEXT_ESCAPE_CHAR => '',
        self::CONTEXT_HEADERS => [],
        self::CONTEXT_NO_HEADERS => false,
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

        $this->iterator = $this->file->openFile();
        $this->iterator->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::DROP_NEW_LINE
        );
        $this->iterator->setCsvControl(
            $this->defaultContext[self::CONTEXT_DELIMITER],
            $this->defaultContext[self::CONTEXT_ENCLOSURE],
            $this->defaultContext[self::CONTEXT_ESCAPE_CHAR]
        );

        if (!$this->defaultContext[self::CONTEXT_NO_HEADERS]) {
            $this->rewind();
            $this->defaultContext[self::CONTEXT_HEADERS] = $this->iterator->current();
        }

        $this->rewind();
        while ($this->valid()) {
            ++$this->count;
            $this->next();
        }

        $this->rewind();
    }

    public function getDto(): ?string
    {
        return $this->dto;
    }

    public function isDenormalizable(): bool
    {
        return null !== $this->dto && !empty($this->defaultContext[self::CONTEXT_HEADERS]);
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

        if (empty($this->defaultContext[self::CONTEXT_HEADERS])) {
            return $this->iterator->current();
        }

        if ((\is_countable($this->defaultContext[self::CONTEXT_HEADERS]) ? \count($this->defaultContext[self::CONTEXT_HEADERS]) : 0) === (\is_countable($this->iterator->current()) ? \count($this->iterator->current()) : 0)) {
            return \array_combine($this->defaultContext[self::CONTEXT_HEADERS], $this->iterator->current());
        }

        return [];
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

        if (!empty($this->defaultContext[self::CONTEXT_HEADERS])) {
            $this->next();
        }

        $this->index = 1;
    }

    public function count(): int
    {
        return $this->count;
    }
}
