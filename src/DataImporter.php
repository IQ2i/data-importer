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
        $this->processor->begin(MessageFactory::create($this->reader));

        foreach ($this->reader as $data) {
            $this->processor->item(MessageFactory::create(
                $this->reader,
                $this->reader->isDenormalizable() ? $this->serializeData($data) : $data
            ));

            if ($this->processor instanceof BatchProcessorInterface && (
                0 === $this->reader->index() % $this->processor->getBatchSize() || $this->reader->index() === $this->reader->count()
            )) {
                $this->processor->batch(MessageFactory::create($this->reader));
            }
        }

        $this->processor->end(MessageFactory::create($this->reader));

        $this->doArchive();
    }

    /**
     * @return mixed
     */
    private function serializeData(array $data)
    {
        try {
            return $this->serializer->denormalize($data, $this->reader->getDto());
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
