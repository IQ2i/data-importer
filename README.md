# iq2i/data-importer

[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/quality/g/iq2i/data-importer/4.x?style=flat-square)](https://scrutinizer-ci.com/g/iq2i/data-importer/)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/iq2i/data-importer/4.x?style=flat-square)](https://scrutinizer-ci.com/g/iq2i/data-importer/)
[![Build Status](https://img.shields.io/github/actions/workflow/status/iq2i/data-importer/continuous-integration.yml?style=flat-square)](https://github.com/IQ2i/data-importer/actions)
[![License](https://img.shields.io/github/license/iq2i/data-importer?style=flat-square)](https://github.com/IQ2i/data-importer/blob/4.x/LICENSE)
[![Latest Stable Version](https://img.shields.io/packagist/v/iq2i/data-importer?style=flat-square)](https://packagist.org/packages/iq2i/data-importer)

A PHP library to easily manage and import large data file.

## Installation

```bash
composer require iq2i/data-importer
```

##### Choose the version you need

| Version (X.Y.Z) |    PHP     | Comment             |
|:---------------:|:----------:|:--------------------|
|     `4.0.*`     | `>= 8.1.0` | **Current version** |
|     `3.2.*`     | `>= 7.4.0` | Previous version    |

## Usage

DataImporter is based on 3 components:

* [Reader](/docs/reader.md): how to read your files
* [Processor](/docs/processor.md): what to do with your data
* [Archiver](/docs/archiver.md): where to store processed files (optional)

Once the required parts are initialized, you can create a DataImporter and use
it:

```php
<?php

use IQ2i\DataImporter\DataImporter;

$dataImporter = new DataImporter(
    $csvReader,
    $articleProcessor,
    // optional archiver here
);
$dataImporter->execute();
```

## Framework integration

Additionally, this package provides deeper integration into Symfony:

* [An abstract command to easily setup import jobs in your projects](/docs/command.md)

## Issues and feature requests

Please report issues and request features
at https://github.com/iq2i/data-importer/issues.

## License

This bundle is under the MIT license. For the whole copyright, see
the [LICENSE](LICENSE) file distributed with this source code.
