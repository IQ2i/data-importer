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

interface ReaderInterface extends \Iterator, \Countable
{
    /**
     * Get DTO.
     */
    public function getDto(): ?string;

    /**
     * Check if file can be denormalized.
     */
    public function isDenormalizable(): bool;

    /**
     * Get file.
     */
    public function getFile(): \SplFileInfo;

    /**
     * Get current index.
     */
    public function index();

    /**
     * {@inheritdoc}
     */
    public function current(): array;

    /**
     * {@inheritdoc}
     */
    public function next();

    /**
     * {@inheritdoc}
     */
    public function key();

    /**
     * {@inheritdoc}
     */
    public function valid();

    /**
     * {@inheritdoc}
     */
    public function rewind();

    /**
     * {@inheritdoc}
     */
    public function count();
}
