# Abstract command

To make it easier for you to integrate DataImporter into your Symfony
projects, you can use an abstract command to easily create all your import
commands.

## Usage

A command would look like this:

```php
// src/Command/BookImportCommand.php
namespace App\Command;

use IQ2i\DataImporter\Bundle\Command\AbstractImportCommand;
use IQ2i\DataImporter\Exchange\Message;
use IQ2i\DataImporter\Reader\CsvReader;
use IQ2i\DataImporter\Reader\ReaderInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:import:book')]
class BookImportCommand extends AbstractImportCommand
{
    protected function handleItem(): callable
    {
        return static function (Message $message) {
            // put your logic here
        };
    }

    protected function getReader(?string $filename = null): ReaderInterface
    {
        // here is an example with a CsvReader, but you can use any of the possible readers
        return new CsvReader($filename, null, [CsvReader::CONTEXT_DELIMITER => ';']);
    }
}
```

To recap, here's what's expected from the two abstract methods:

``handleItem()``  
> This method allows you to describe the logic to apply for each line of the 
> file you import. This is simply the same logic as you would apply if you 
> were handling a [processor](/src/Processor/ProcessorInterface.php) 
> directly except that here the logic must be wrapped in a Closure.

``getReader(?string $filename = null)``  
> Here is where you create the reader to access the file you want to import.

These are the two abstract and mandatory methods.

There are, however, other methods you can override in order to have more 
control over how DataImporter should import your data: see the 
[AbstractImportCommand class](/src/Bundle/Command/AbstractImportCommand.php)

## Command arguments and options

The AbstractImportCommand provide some argument and options for your import 
commands:

#### Arguments

``filename``
> The path to the file that you want to import data.  
> This argument is optional, so you can provide you own logic to get file 
> path inside your commands. 

#### Options

``step``
> To help you debug your imports, this option allows you to browse the data 
> line by line 

``pause-on-error``
> When the line by line debug is too long, you can use this option to pause 
> the import in case of an error on a line 

``batch-size``
> Specify the size of your batches
