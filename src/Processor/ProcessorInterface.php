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

namespace IQ2i\DataImporter\Processor;

use IQ2i\DataImporter\Exchange\Message;

interface ProcessorInterface
{
    /**
     * Method called at the beginning of the file processing.
     *
     * @param Message $message Object containing information about the file being processed (without data)
     */
    public function begin(Message $message);

    /**
     * Process one line.
     *
     * @param Message $message Object containing information about the file being processed (with data)
     */
    public function item(Message $message);

    /**
     * Method called at the end of the file processing.
     *
     * @param Message $message Object containing information about the file being processed (without data)
     */
    public function end(Message $message);
}
