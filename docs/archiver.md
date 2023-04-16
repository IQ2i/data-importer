# Archiver

This library comes with a file archiving mechanism that will store each file
after processing.

## DateTimeArchiver

The archiver provided is the DateTimeArchiver which will archive the files with
a tree structure based on the date of import:

```php
<?php

use IQ2i\DataImporter\Archiver\DateTimeArchiver;
use IQ2i\DataImporter\DataImporter;

$archiver = new DateTimeArchiver('/path/to/storage');

$dataImporter = new DataImporter(
    $csvReader,
    $articleProcessor,
    $archiver
);
```

## Create your own archiver

It is possible to create your own archiver by implementing
the [ArchiverInterface](/src/Archiver/ArchiverInterface.php)
