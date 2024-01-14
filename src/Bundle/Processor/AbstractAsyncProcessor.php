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

namespace IQ2i\DataImporter\Bundle\Processor;

use IQ2i\DataImporter\Bundle\Messenger\AsyncMessage;
use IQ2i\DataImporter\Exchange\Message;
use IQ2i\DataImporter\Processor\ProcessorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractAsyncProcessor implements ProcessorInterface
{
    private ?MessageBusInterface $bus = null;

    public function begin(Message $message)
    {
    }

    public function item(Message $message)
    {
        $this->bus->dispatch(new AsyncMessage($message));
    }

    abstract public function processItem(AsyncMessage $message);

    public function end(Message $message)
    {
    }

    #[Required]
    public function setBus(MessageBusInterface $bus): void
    {
        $this->bus = $bus;
    }
}
