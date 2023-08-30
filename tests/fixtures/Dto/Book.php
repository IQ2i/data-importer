<?php

declare(strict_types=1);

namespace IQ2i\DataImporter\Tests\fixtures\Dto;

class Book
{
    private ?string $author = null;
    private ?string $title = null;
    private ?string $genre = null;
    private ?float $price = null;
    private ?string $description = null;

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author = null): self
    {
        $this->author = $author;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title = null): self
    {
        $this->title = $title;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre = null): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price = null): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description = null): self
    {
        $this->description = $description;

        return $this;
    }
}
