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

namespace IQ2i\DataImporter\Bundle\Messenger;

use IQ2i\DataImporter\Processor\AsyncProcessorInterface;
use IQ2i\DataImporter\Processor\ProcessorInterface;

class MessageHandler
{
    public function __construct(
        private readonly ProcessorInterface&AsyncProcessorInterface $processor,
    ) {
    }

    public function __invoke(ProcessItemMessage $message): void
    {
        $this->processor->item($message->getMessage());
    }
}
