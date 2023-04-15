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

namespace IQ2i\DataImporter\Tests\fixtures\Processor;

use IQ2i\DataImporter\Exchange\Message;
use IQ2i\DataImporter\Processor\BatchProcessorInterface;

class BatchProcessor implements BatchProcessorInterface
{
    /**
     * @var int
     */
    final public const BATCH_SIZE = 2;

    public function begin(Message $message): void
    {
    }

    public function item(Message $message): void
    {
    }

    public function end(Message $message): void
    {
    }

    public function batch(Message $message)
    {
    }

    public function getBatchSize()
    {
        return self::BATCH_SIZE;
    }
}
