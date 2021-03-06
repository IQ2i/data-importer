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

interface BatchProcessorInterface extends ProcessorInterface
{
    /**
     * Action to execute at the end of a batch.
     */
    public function batch();

    /**
     * Action to execute at the end of a batch.
     *
     * @return int
     */
    public function getBatchSize();
}
