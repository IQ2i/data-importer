<?php

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

interface BatchProcessorInterface extends ProcessorInterface
{
    /**
     * Action to execute at the end of a batch.
     *
     * @param Message $message Object containing information about the file being processed (without data)
     */
    public function batch(Message $message);

    /**
     * Action to execute at the end of a batch.
     *
     * @return int
     */
    public function getBatchSize();
}
