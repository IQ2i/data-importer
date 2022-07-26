<?php

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <loic@sapone.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter;

use IQ2i\DataImporter\Archiver\ArchiverInterface;
use IQ2i\DataImporter\Exchange\MessageFactory;
use IQ2i\DataImporter\Processor\BatchProcessorInterface;
use IQ2i\DataImporter\Processor\ProcessorInterface;
use IQ2i\DataImporter\Reader\ReaderInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DataImporter
{
    private ReaderInterface $reader;
    private ProcessorInterface $processor;
    private ?ArchiverInterface $archiver;
    private Serializer $serializer;

    public function __construct(ReaderInterface $reader, ProcessorInterface $processor, ?ArchiverInterface $archiver = null, ?Serializer $serializer = null)
    {
        $this->reader = $reader;
        $this->processor = $processor;
        $this->archiver = $archiver;
        $this->serializer = $serializer ?? new Serializer([new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())]);
    }

    public function execute(): void
    {
        // callback before file processing
        $this->processor->begin();

        // process file
        foreach ($this->reader as $data) {
            // serialize data if needed
            $this->serializeData($data);

            // create message
            $message = MessageFactory::create($this->reader, $data);

            // process message
            $this->processor->item($message);

            // call batch action
            if (
                $this->processor instanceof BatchProcessorInterface
                && (
                    0 === $message->getCurrentIteration() % $this->processor->getBatchSize()
                    || $message->getCurrentIteration() === $message->getTotalIteration()
                )
            ) {
                // callback at the end of batch
                $this->processor->batch();
            }
        }

        // callback before file processing
        $this->processor->end();

        // archive file
        $this->doArchive();
    }

    private function serializeData(array &$data): void
    {
        if (false === $this->reader->isDenormalizable()) {
            return;
        }
        try {
            // denormalize array data into DTO object
            $data = $this->serializer->denormalize($data, $this->reader->getDto());
        } catch (\Exception $exception) {
            throw new \InvalidArgumentException('An error occurred while denormalizing data: '.$exception->getMessage());
        }
    }

    private function doArchive(): void
    {
        if (null === $this->archiver) {
            return;
        }

        try {
            $this->archiver->archive($this->reader->getFile());
        } catch (IOException $exception) {
            throw new IOException('An error occurred while archiving file: '.$exception->getMessage());
        }
    }
}
