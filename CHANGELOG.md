CHANGELOG
=========

3.2.0
-----

* add serializer override support
* make filename argument optional in abstract command

3.1.0
-----

* move source files into a src folder
* downgrade docker PHP version to minimum version required by this package
* add an abstract command to create Symfony command

3.0.0
-----

* add Docker environment
* remove support to PHP 7.2 and PHP 7.3
* upgrade dependencies

2.3.0
-----

* support PHP 8.1

2.2.0
-----

* support Symfony 6
* fix dev dependencies version

2.1.0
-----

* support PHP 8.0

2.0.1
-----

* fix BatchProcessor methods that were never called

2.0.0
-----

* now data-importer manage only one file at a time
* add begin() and end() methods to processor
* add a new processor interface to manage batch process

1.2.0
-----

* fix header reset when multiple files
* move to Github Actions

1.1.1
-----

* check regex validity with explicit false
* limit Finder depth (No more recursive search)

1.1.0
-----

* added XmlReader

1.0.0
-----

* initial release
