# Reader

The reader describes how DataImporter must read data in your files. This library
offers you two readers, one for CSV files and another for XML files.

## CsvReader

The CsvReader has only one mandatory parameter: the path of the CSV file to
import. By default, we consider that the CSV file has headers, but you can
change this behavior by passing a new context to the CsvReader as third
constructor's argument.

```php
<?php

use IQ2i\DataImporter\Reader\CsvReader;

// read CSV file with header
$csvReader = new CsvReader('/path/to/your/csv/file');

// you can specify another delimiter character
$csvReader = new CsvReader(
    '/path/to/your/csv/file',
    null,
    [CsvReader::CONTEXT_DELIMITER => ';']
);
```

## XmlReader

Just like the CsvReader, the XmlReader has only one mandatory parameter: the
path to the XML file to import.

```php
<?php

use IQ2i\DataImporter\Reader\XmlReader;

// read XML file
$xmlReader = new XmlReader('/path/to/your/xml/file');
```

XmlReader let you specify the node you want to process by passing a new context
to constructor. For example, take the following XML file:

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
                <description>An in-depth look at creating applications with
                    XML.
                </description>
            </book>
            <book>
                <title>Midnight Rain</title>
                <genre>Fantasy</genre>
                <price>5.95</price>
                <description>A former architect battles corporate zombies, an
                    evil sorceress, and her own childhood to become queen of the
                    world.
                </description>
            </book>
        </books>
    </author>
</catalog>
```

If you want to iterate on the `<book>` node, you must give the parent node to
the reader:

```php
<?php

use IQ2i\DataImporter\Reader\XmlReader;

$xmlReader = new XmlReader(
    '/path/to/your/xml/file',
    null,
    [XmlReader::CONTEXT_XPATH => 'catalog/author/books']
);
```

## JsonReader

The JsonReader has only one mandatory parameter: the path of the json file to
import.

```php
<?php

use IQ2i\DataImporter\Reader\JsonReader;

// read JSON file with header
$jsonReader = new JsonReader('/path/to/your/json/file');
```

Just like the XmlReader, the JsonReader allows you to specify a node you 
want to iterate over. Let's consider the following JSON file:

```json
{
    "author": {
        "firstname": "Kim",
        "lastname": "Ralls",
        "books": [
            {
                "title": "XML Developer's Guide",
                "genre": "Computer",
                "price": 44.95,
                "description": "An in-depth look at creating applications with XML."
            },
            {
                "title": "Midnight Rain",
                "genre": "Fantasy",
                "price": 5.95,
                "description": "A former architect battles corporate zombies, an evil sorceress, and her own childhood to become queen of the world."
            }
        ]
    }
}
```

To iterate over the "books" node, you simply need to specify a pointer:

```php
<?php

use IQ2i\DataImporter\Reader\JsonReader;

$xmlReader = new JsonReader(
    '/path/to/your/json/file',
    null,
    [JsonReader::POINTER => '/author/books']
);
```

## Create your own reader

It is possible to create your own reader by implementing
the [ReaderInterface](/src/Reader/ReaderInterface.php)

## DTO

By default, the reader will return an array with the headers as keys, and the
contents of each line as values. But if you want to work with real objects, you
can use a DTO.

Create a class that matches your data:

```php
<?php

namespace App\DTO;

class Book
{
    private $title;
    private $genre;
    private $price;
    private $description;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
```

And add the DTO in your reader's constructor:

```php
<?php

use App\DTO\Book;
use IQ2i\DataImporter\Reader\CsvReader;

$csvReader = new CsvReader(
    '/path/to/your/csv/file',
    Book::class
);
```

For more information about the DTO, see
the [Symfony Serializer documentation](https://symfony.com/doc/current/components/serializer.html).
