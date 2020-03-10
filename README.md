# iq2i/data-importer

[![Build Status](https://travis-ci.org/IQ2i/data-importer.svg?branch=master)](https://travis-ci.org/IQ2i/data-importer)

A PHP library to easily manage and import large data file.

## Installation

```bash
composer require iq2i/data-importer
```

## Usage

The mandatory parts are the reader and the processor.

### Reader

The reader describes how DataImporter must to read data in your files.

This library offers you a default reader for CSV files.
By default, we consider that the CSV file has headers but you can change this behavior by passing a new context to the CsvReader.

```php
use IQ2i\DataImporter\Reader\CsvReader;

// read CSV files with header
$csvReader = new CsvReader();

// you can specify an other delimiter character
$csvReader = new CsvReader([
    CsvReader::DELIMITER_KEY => ';',
]);

// or custom headers
$csvReader = new CsvReader([
    CsvReader::NO_HEADERS_KEY => true,
    CsvReader::HEADERS_KEY => ['code', 'name', 'price'],
]);
```

By default, the reader will return an array with the headers as keys and the contents of each line as values.
But if you want to work with real object, you can set a DTO.

Create a class that matches your data:

```php
<?php

namespace App\DTO;

class ArticleDTO
{
    private $code;
    private $manufacturer;
    private $price;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }
}
```

And set the class to the DataImporter:

```php
use App\DTO\Article;
use IQ2i\DataImporter\Reader\CsvReader;

$csvReader = new CsvReader();
$csvReader->setDto(Article::class);
```

For more informations about the DTO, see the [Symfony Serializer documentation](https://symfony.com/doc/current/components/serializer.html).

It is possible to create your own reader, you just must implements the [ReaderInterface](Reader/ReaderInterface.php)

### Processor

Now, you need to create a processor. This is where you process each piece of file (a line in the case of CSV files).
Your processor must implements the [ProcessorInterface](Processor/ProcessorInterface.php).

This is an example processor :

```php
<?php

namespace App\Processor;

use IQ2i\DataImporter\Exchange\Message;
use IQ2i\DataImporter\Processor\ProcessorInterface;

class ArticleProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(Message $message)
    {
        var_dump($message);
    }
}
```

The only thing you need is a process method that takes a Message object as an argument.
The Message object gives you information about the file you are processing, the current iteration and of course the content of the iteration.

### DataImporter

Now that you have a reader and a processor, you can set up the DataImporter:

```php
use IQ2i\DataImporter\DataImporter;

$dataImporter = new DataImporter(
    $csvReader,
    $articleProcessor
);
```

Finally, execute your import by passing the path of the folder where the files are located.

```php
$dataImporter->execute('/path/to/the/folder');
```

By default, each reader defines default regex to find files thaht they can read, but you can specify a custom regex:

```php
$dataImporter->execute('/path/to/the/folder', '/.csv$');
```

### Archiver

This library comes with a file archiving mechanism that will store each file after processing.
The archiver provided is the DateTimeArchiver which will archive the files with a tree structure based on the date of import.

The setup is easy:

```php
use IQ2i\DataImporter\Archiver\DateTimeArchiver;
use IQ2i\DataImporter\DataImporter;

$archiver = new DateTimeArchiver('/path/to/storage');

$dataImporter = new DataImporter(
    $csvReader,
    $articleProcessor,
    $archiver
);
```

It is possible to create your own archiver, you just must implements the [ArchiverInterface](Archiver/ArchiverInterface.php)

## Issues and feature requests

Please report issues and request features at https://github.com/iq2i/data-importer/issues.

## License

This bundle is under the MIT license.
For the whole copyright, see the [LICENSE](LICENSE) file distributed with this source code.
