<?php

namespace IQ2i\DataImporter\Reader;

trait SerializableReaderTrait
{
    private $dto;

    public function getDto(): ?string
    {
        return $this->dto;
    }

    public function setDto(string $dto): void
    {
        $this->dto = $dto;
    }
}
