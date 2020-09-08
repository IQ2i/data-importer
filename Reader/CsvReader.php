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

    const FILE_REGEX_KEY = 'csv_file_regex';
    const DELIMITER_KEY = 'csv_delimiter';
    const ENCLOSURE_KEY = 'csv_enclosure';
    const ESCAPE_CHAR_KEY = 'csv_escape_char';
    const HEADERS_KEY = 'csv_headers';
    const NO_HEADERS_KEY = 'no_headers';

    private $file;
    private $count = 0;
    private $index = 1;
    private $defaultContext = [
        self::FILE_REGEX_KEY  => '/.csv/',
        self::DELIMITER_KEY   => ',',
        self::ENCLOSURE_KEY   => '"',
        self::ESCAPE_CHAR_KEY => '',
        self::HEADERS_KEY     => [],
        self::NO_HEADERS_KEY  => false,
    ];

    public function __construct(array $defaultContext = [])
    {
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);

        if (\PHP_VERSION_ID < 70400 && '' === $this->defaultContext[self::ESCAPE_CHAR_KEY]) {
            $this->defaultContext[self::ESCAPE_CHAR_KEY] = '\\';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(\SplFileObject $file): void
    {
        // update file attributes
        $this->file = $file;
        $this->file->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::DROP_NEW_LINE
        );
        $this->file->setCsvControl(
            $this->defaultContext[self::DELIMITER_KEY],
            $this->defaultContext[self::ENCLOSURE_KEY],
            $this->defaultContext[self::ESCAPE_CHAR_KEY]
        );

        // init headers
        $this->defaultContext[self::HEADERS_KEY] = [];
        if (!$this->defaultContext[self::NO_HEADERS_KEY]) {
            $this->rewind();
            $this->defaultContext[self::HEADERS_KEY] = $this->file->current();
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
        return !empty($this->defaultContext[self::HEADERS_KEY]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultFileRegex(): string
    {
        return $this->defaultContext[self::FILE_REGEX_KEY];
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
    public function current()
    {
        if (!$this->valid()) {
            return [];
        }

        if (!empty($this->defaultContext[self::HEADERS_KEY])) {
            return array_combine($this->defaultContext[self::HEADERS_KEY], $this->file->current());
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
        $this->index = 1;

        // skip headers
        if (!empty($this->defaultContext[self::HEADERS_KEY])) {
            $this->next();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->count;
    }
}
