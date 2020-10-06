<?php

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <contact@iq2i.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter\Tests\Processor;

use IQ2i\DataImporter\Exchange\Message;
use IQ2i\DataImporter\Processor\ProcessorInterface;

class TestItemProcessor implements ProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function begin(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function item(Message $message): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function end(): void
    {
    }
}
