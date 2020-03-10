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
     * Process iteration.
     */
    public function process(Message $message);
}
