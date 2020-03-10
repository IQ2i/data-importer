<?php

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <contact@iq2i.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter\Exchange;

use IQ2i\DataImporter\Reader\ReaderInterface;

class MessageFactory
{
    public static function create(\SplFileInfo $file, ReaderInterface $reader, $data): Message
    {
        return new Message(
            $file->getFilename(),
            $file->getPathname(),
            $reader->index(),
            $reader->count(),
            $data
        );
    }
}
