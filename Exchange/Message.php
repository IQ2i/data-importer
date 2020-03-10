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

class Message
{
    private $fileName;
    private $filePath;
    private $currentIteration;
    private $totalIteration;
    private $data;

    public function __construct(string $fileName, string $filePath, int $currentIteration, int $totalIteration, $data)
    {
        $this->fileName = $fileName;
        $this->filePath = $filePath;
        $this->currentIteration = $currentIteration;
        $this->totalIteration = $totalIteration;
        $this->data = $data;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getCurrentIteration(): int
    {
        return $this->currentIteration;
    }

    public function getTotalIteration(): int
    {
        return $this->totalIteration;
    }

    public function getData()
    {
        return $this->data;
    }
}
