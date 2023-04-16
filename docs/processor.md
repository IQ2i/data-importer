# Processor

Now, you need to create a processor. This is where you process each piece of
file (a line in the case of CSV files).

## Simple processor

When you just need to process line by line, you can implement
the [ProcessorInterface](/src/Processor/ProcessorInterface.php).

This is an example processor:

```php
<?php

namespace App\Processor;

use IQ2i\DataImporter\Exchange\Message;
use IQ2i\DataImporter\Processor\ProcessorInterface;

class ArticleProcessor implements ProcessorInterface
{
    public function begin(Message $message): void
    {
        // Do something before process file content
    }

    public function item(Message $message): void
    {
        var_dump($message);
    }

    public function end(Message $message): void
    {
        // Do something after process file content
    }
}
```

## Batch processor

When you need to process line by line and execute an action X processed lines,
you have to implement
the [BatchProcessorInterface](/src/Processor/BatchProcessorInterface.php).

Here is an example:

```php
<?php

namespace App\Processor;

use IQ2i\DataImporter\Exchange\Message;
use IQ2i\DataImporter\Processor\BatchProcessorInterface;

class ArticleProcessor implements BatchProcessorInterface
{
    const BATCH_SIZE = 100;

    public function begin(Message $message): void
    {
        // Do something before process file content
    }

    public function item(Message $message): void
    {
        var_dump($message);
    }

    public function batch(Message $message): void
    {
        // Do something at the end of a batch
        // ex: $this->entityManager->flush()
    }

    public function end(Message $message): void
    {
        // Do something after process file content
    }

    public function getBatchSize(): int
    {
        return self::BATCH_SIZE;
    }
}
```
