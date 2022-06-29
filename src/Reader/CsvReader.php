<?php

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
    public const CONTEXT_DELIMITER = 'csv_delimiter';
    public const CONTEXT_ENCLOSURE = 'csv_enclosure';
    public const CONTEXT_ESCAPE_CHAR = 'csv_escape_char';
    public const CONTEXT_HEADERS = 'csv_headers';
    public const CONTEXT_NO_HEADERS = 'no_headers';

    private ?string $dto;
    private \SplFileInfo $file;
    private \SplFileObject $iterator;
    private int $count = 0;
    private int $index = 1;
    private array $defaultContext = [
        self::CONTEXT_DELIMITER => ',',
        self::CONTEXT_ENCLOSURE => '"',
        self::CONTEXT_ESCAPE_CHAR => '',
        self::CONTEXT_HEADERS => [],
        self::CONTEXT_NO_HEADERS => false,
    ];

    public function __construct(string $filePath, ?string $dto = null, array $defaultContext = [])
    {
        // create a new SplInfo from path
        $this->file = new \SplFileInfo($filePath);

        // check if file is readable
        if (!$this->file->isReadable()) {
            throw new \InvalidArgumentException('The file '.$this->file->getFilename().' is not readable.');
        }

        // create SplObject from SplInfo
        $this->iterator = $this->file->openFile();

        // update default context
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
        if (\PHP_VERSION_ID < 70400 && '' === $this->defaultContext[self::CONTEXT_ESCAPE_CHAR]) {
            $this->defaultContext[self::CONTEXT_ESCAPE_CHAR] = '\\';
        }

        // update file attributes
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

        // init headers
        if (!$this->defaultContext[self::CONTEXT_NO_HEADERS]) {
            $this->rewind();
            $this->defaultContext[self::CONTEXT_HEADERS] = $this->iterator->current();
        }

        // set dto
        $this->dto = $dto;

        // update counter
        $this->rewind();
        while ($this->valid()) {
            ++$this->count;
            $this->next();
        }
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
        return null !== $this->dto && !empty($this->defaultContext[self::CONTEXT_HEADERS]);
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

        if (empty($this->defaultContext[self::CONTEXT_HEADERS])) {
            return $this->iterator->current();
        }

        if (\count($this->defaultContext[self::CONTEXT_HEADERS]) === \count($this->iterator->current())) {
            return array_combine($this->defaultContext[self::CONTEXT_HEADERS], $this->iterator->current());
        }

        return [];
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
     *
     * @return mixed
     */
    public function key()
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

        // skip headers
        if (!empty($this->defaultContext[self::CONTEXT_HEADERS])) {
            $this->next();
        }

        $this->index = 1;
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return $this->count;
    }
}
