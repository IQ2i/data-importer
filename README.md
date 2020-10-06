# iq2i/data-importer

[![Continuous Integration](https://github.com/IQ2i/data-importer/workflows/Continuous%20Integration/badge.svg?branch=master)](https://github.com/IQ2i/data-importer/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/IQ2i/data-importer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/IQ2i/data-importer/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/IQ2i/data-importer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/IQ2i/data-importer/?branch=master)

A PHP library to easily manage and import large data file.

## Installation

```bash
composer require iq2i/data-importer
```

## Usage

The mandatory parts are the reader and the processor.

### Reader

The reader describes how DataImporter must to read data in your files.
This library offers you two readers, one for CSV files and another for XML files.

#### CsvReader

By default, we consider that the CSV file has headers but you can change this behavior by passing a new context to the CsvReader.

```php
use IQ2i\DataImporter\Reader\CsvReader;

// read CSV files with header
$csvReader = new CsvReader('/path/to/your/csv/file');

// you can specify an other delimiter character
$csvReader = new CsvReader(
    '/path/to/your/csv/file',
    [
        CsvReader::DELIMITER_KEY => ';',
    ]
);

// or custom headers
$csvReader = new CsvReader(
    '/path/to/your/csv/file',
    [
        CsvReader::NO_HEADERS_KEY => true,
        CsvReader::HEADERS_KEY => ['code', 'name', 'price'],
    ]
);
```

It is possible to create your own reader, you just must implements the [ReaderInterface](Reader/ReaderInterface.php)

#### XmlReader

XmlReader let you specify the node you want to process by passing a new context in constructor.
Take the following XML file:

```xml
<?xml version="1.0"?>
<catalog>
    <author>
        <name>Gambardella, Matthew</name>
        <books>
            <book>
                <title>XML Developer's Guide</title>
                <genre>Computer</genre>
                <price>44.95</price>
                <description>An in-depth look at creating applications with XML.</description>
            </book>
            <book>
                <title>Midnight Rain</title>
                <genre>Fantasy</genre>
                <price>5.95</price>
                <description>A former architect battles corporate zombies, an evil sorceress, and her own childhood to become queen of the world.</description>
            </book>
        </books>
    </author>
</catalog>
```

If you want to iterate on the `<book>` node, you must give the parent node to the reader:

```php
use IQ2i\DataImporter\Reader\XmlReader;

// you can specify an other delimiter character
$xmlReader = new XmlReader(
    '/path/to/your/xml/file',
    [
        XmlReader::XPATH_KEY => 'catalog/author/books',
    ]);
```

### DTO

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

$csvReader = new CsvReader('/path/to/your/csv/file');
$csvReader->setDto(Article::class);
```

For more informations about the DTO, see the [Symfony Serializer documentation](https://symfony.com/doc/current/components/serializer.html).

### Processor

Now, you need to create a processor. This is where you process each piece of file (a line in the case of CSV files).

#### ItemProcessor

When you just need to process line by line, you can implements the [ProcessorInterface](Processor/ProcessorInterface.php).

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
    public function begin(): void
    {
        // Do something before process file content
    }

    /**
     * {@inheritdoc}
     */
    public function item(Message $message): void
    {
        var_dump($message);
    }

    /**
     * {@inheritdoc}
     */
    public function end(): void
    {
        // Do something after process file content
    }
}
```

#### ItemProcessor

When you need to process line by line and execute an action X processed lines, you can implements the [BatchProcessorInterface](Processor/BatchProcessorInterface.php).

Here is an example:

```php
<?php

namespace App\Processor;

use IQ2i\DataImporter\Exchange\Message;
use IQ2i\DataImporter\Processor\BatchProcessorInterface;

class ArticleProcessor implements BatchProcessorInterface
{
    const BATCH_SIZE = 100;

    /**
     * {@inheritdoc}
     */
    public function begin(): void
    {
        // Do something before process file content
    }

    /**
     * {@inheritdoc}
     */
    public function item(Message $message): void
    {
        var_dump($message);
    }

    /**
     * {@inheritdoc}
     */
    public function batch(): void
    {
        // Do something at the end of a batch
        // ex: $this->entityManager->flush()
    }

    /**
     * {@inheritdoc}
     */
    public function end(): void
    {
        // Do something after process file content
    }

    /**
     * {@inheritdoc}
     */
    public function getBatchSize(): int
    {
        // Return the size of each batch
    }
}
```

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
$dataImporter->execute();
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
