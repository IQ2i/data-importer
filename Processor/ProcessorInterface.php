<?php

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <contact@iq2i.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter\Processor;

use IQ2i\DataImporter\Exchange\Message;

interface ProcessorInterface
{
    /**
     * Method called at the begin of the file processing.
     */
    public function begin();

    /**
     * Process one line.
     *
     * @param Message $message Object containing information about the file being processed
     */
    public function item(Message $message);

    /**
     * Method called at the end of the file processing.
     */
    public function end();
}
