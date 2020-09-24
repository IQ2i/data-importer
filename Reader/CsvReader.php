<?php

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <contact@iq2i.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter\Reader;

use IQ2i\DataImporter\Traits\SerializerTrait;

class CsvReader implements ReaderInterface
{
    use SerializerTrait;

    const CONTEXT_DELIMITER = 'csv_delimiter';
    const CONTEXT_ENCLOSURE = 'csv_enclosure';
    const CONTEXT_ESCAPE_CHAR = 'csv_escape_char';
    const CONTEXT_HEADERS = 'csv_headers';
    const CONTEXT_NO_HEADERS = 'no_headers';

    private $file;
    private $count = 0;
    private $index = 1;
    private $defaultContext = [
        self::CONTEXT_DELIMITER   => ',',
        self::CONTEXT_ENCLOSURE   => '"',
        self::CONTEXT_ESCAPE_CHAR => '',
        self::CONTEXT_HEADERS     => [],
        self::CONTEXT_NO_HEADERS  => false,
    ];

    public function __construct(string $filePath, array $defaultContext = [])
    {
        // create a new SplInfo from path
        $fileInfo = new \SplFileInfo($filePath);

        // check if file is readable
        if (!$fileInfo->isReadable()) {
            throw new \InvalidArgumentException('The file '.$fileInfo->getFilename().' is not readable.');
        }

        // create SplObject from SplInfo
        $this->file = $fileInfo->openFile();

        // update default context
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
        if (\PHP_VERSION_ID < 70400 && '' === $this->defaultContext[self::CONTEXT_ESCAPE_CHAR]) {
            $this->defaultContext[self::CONTEXT_ESCAPE_CHAR] = '\\';
        }

        // update file attributes
        $this->file->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::DROP_NEW_LINE
        );
        $this->file->setCsvControl(
            $this->defaultContext[self::CONTEXT_DELIMITER],
            $this->defaultContext[self::CONTEXT_ENCLOSURE],
            $this->defaultContext[self::CONTEXT_ESCAPE_CHAR]
        );

        // init headers
        if (!$this->defaultContext[self::CONTEXT_NO_HEADERS]) {
            $this->rewind();
            $this->defaultContext[self::CONTEXT_HEADERS] = $this->file->current();
        }

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
    public function isDenormalizable(): bool
    {
        return !empty($this->defaultContext[self::CONTEXT_HEADERS]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(): \SplFileObject
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

        if (!empty($this->defaultContext[self::CONTEXT_HEADERS])) {
            $current = array_combine($this->defaultContext[self::CONTEXT_HEADERS], $this->file->current());

            return false !== $current ? $current : [];
        }

        return $this->file->current();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->file->next();
        ++$this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->file->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->file->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->file->rewind();

        // skip headers
        if (!empty($this->defaultContext[self::CONTEXT_HEADERS])) {
            $this->next();
        }

        $this->index = 1;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->count;
    }
}
