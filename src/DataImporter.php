<?php

declare(strict_types=1);

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
use IQ2i\DataImporter\Bundle\Messenger\ProcessItemMessage;
use IQ2i\DataImporter\Exchange\MessageFactory;
use IQ2i\DataImporter\Processor\AsyncProcessorInterface;
use IQ2i\DataImporter\Processor\BatchProcessorInterface;
use IQ2i\DataImporter\Processor\ProcessorInterface;
use IQ2i\DataImporter\Reader\ReaderInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class DataImporter
{
    private readonly SerializerInterface $serializer;

    public function __construct(
        private readonly ReaderInterface $reader,
        private readonly ProcessorInterface $processor,
        private readonly ?ArchiverInterface $archiver = null,
        SerializerInterface $serializer = null,
        private readonly ?MessageBusInterface $bus = null,
    ) {
        $this->serializer = $serializer ?? new Serializer([new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())]);
    }

    public function execute(): void
    {
        $this->processor->begin(MessageFactory::create($this->reader));

        foreach ($this->reader as $data) {
            $message = MessageFactory::create(
                $this->reader,
                $this->reader->isDenormalizable() ? $this->serializeData($data) : $data
            );

            if ($this->processor instanceof AsyncProcessorInterface && null !== $this->bus) {
                $this->bus->dispatch(new ProcessItemMessage(fn () => $this->processor->item($message), $message));

                continue;
            }

            $this->processor->item($message);

            if ($this->processor instanceof BatchProcessorInterface && (
                0 === $this->reader->index() % $this->processor->getBatchSize() || $this->reader->index() === $this->reader->count()
            )) {
                $this->processor->batch(MessageFactory::create($this->reader));
            }
        }

        $archivedFilePath = null;
        if (null !== $this->archiver) {
            $archivedFilePath = $this->doArchive();
        }

        $this->processor->end(MessageFactory::create($this->reader, null, $archivedFilePath));
    }

    private function serializeData(array $data): mixed
    {
        try {
            return $this->serializer->denormalize($data, $this->reader->getDto());
        } catch (\Exception $exception) {
            throw new \InvalidArgumentException('An error occurred while denormalizing data: '.$exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function doArchive(): string
    {
        try {
            return $this->archiver->archive($this->reader->getFile());
        } catch (IOException $ioException) {
            throw new IOException('An error occurred while archiving file: '.$ioException->getMessage());
        }
    }
}
