<?php

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <loic@sapone.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter\Tests\Processor;

use IQ2i\DataImporter\Exchange\Message;
use IQ2i\DataImporter\Processor\BatchProcessorInterface;

class TestBatchProcessor implements BatchProcessorInterface
{
    public const BATCH_SIZE = 2;

    /**
     * {@inheritdoc}
     */
    public function begin(Message $message): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function item(Message $message): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function end(Message $message): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function batch(Message $message)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getBatchSize()
    {
        return self::BATCH_SIZE;
    }
}
