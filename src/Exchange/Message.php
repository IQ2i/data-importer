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

namespace IQ2i\DataImporter\Exchange;

final class Message
{
    public function __construct(
        private string $fileName,
        private string $filePath,
        private int $currentIteration,
        private int $totalIteration,
        private mixed $data = null,
        private ?string $archiveFilePath = null,
    ) {
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

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getArchiveFilePath(): ?string
    {
        return $this->archiveFilePath;
    }
}
