<?php

namespace IQ2i\DataImporter\Traits;

trait SerializerTrait
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
