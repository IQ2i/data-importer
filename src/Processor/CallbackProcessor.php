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

class CallbackProcessor implements ProcessorInterface
{
    private readonly ?\Closure $begin;

    private readonly ?\Closure $item;

    private readonly ?\Closure $end;

    public function __construct(callable $begin = null, callable $item = null, callable $end = null)
    {
        $this->begin = $begin ? $begin(...) : null;
        $this->item = $item ? $item(...) : null;
        $this->end = $end ? $end(...) : null;
    }

    public function begin(Message $message): void
    {
        if (null !== $this->begin) {
            ($this->begin)($message);
        }
    }

    public function item(Message $message): void
    {
        if (null !== $this->item) {
            ($this->item)($message);
        }
    }

    public function end(Message $message): void
    {
        if (null !== $this->end) {
            ($this->end)($message);
        }
    }
}
