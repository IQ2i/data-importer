<?php

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <contact@iq2i.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter;

use IQ2i\DataImporter\Archiver\ArchiverInterface;
use IQ2i\DataImporter\Exchange\MessageFactory;
use IQ2i\DataImporter\Processor\ProcessorInterface;
use IQ2i\DataImporter\Reader\ReaderInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DataImporter
{
    private $reader;
    private $processor;
    private $archiver;

    public function __construct(ReaderInterface $reader, ProcessorInterface $processor, ?ArchiverInterface $archiver = null)
    {
        $this->reader = $reader;
        $this->processor = $processor;
        $this->archiver = $archiver;
    }

    public function execute(string $path, ?string $regex = null)
    {
        // check if regex is valid
        if (null !== $regex && false === @preg_match($regex, '')) {
            throw new \InvalidArgumentException('The regex "'.$regex.'" is invalid.');
        }

        // init finder
        $finder = $this->initFinder($path, $regex);

        // cut process if no result
        if (!$finder->hasResults()) {
            return false;
        }

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            // check if file is readable
            if (!$file->isReadable()) {
                throw new \InvalidArgumentException('The file '.$file->getFilename().' is not readable.');
            }

            // update reader
            $this->reader->setFile($file->openFile());

            foreach ($this->reader as $data) {
                // process
                $this->processIteration($file, $data);
            }

            // archive file
            $this->doArchive($file);
        }
    }

    private function initFinder(string $path, ?string $regex): Finder
    {
        // init finder
        $finder = new Finder();

        try {
            // search files
            $finder
                ->in($path)
                ->name($regex ?? $this->reader->getDefaultFileRegex())
                ->depth('== 0')
                ->sortByModifiedTime();
        } catch (DirectoryNotFoundException $exception) {
            throw new \InvalidArgumentException('The path "'.$path.'" is not a valid folder path.');
        } catch (AccessDeniedException $exception) {
            throw new \InvalidArgumentException('The directory located in "'.$path.'" is not readable.');
        }

        return $finder;
    }

    private function processIteration(\SplFileInfo $file, $data): void
    {
        try {
            if ($this->reader->isDenormalizable() && null !== $this->reader->getDto()) {
                // init serializer
                $serializer = new Serializer([new ObjectNormalizer()]);

                // denormalize array data into DTO object
                $data = $serializer->denormalize($data, $this->reader->getDto());
            }

            // create message
            $message = MessageFactory::create($file, $this->reader, $data);

            // process message
            $this->processor->process($message);
        } catch (NotNormalizableValueException $exception) {
            throw new \InvalidArgumentException('An error occurred while denormalizing data: '.$exception->getMessage());
        }
    }

    private function doArchive(\SplFileInfo $file): void
    {
        if (null !== $this->archiver) {
            return;
        }

        try {
            $this->archiver->archive($file);
        } catch (IOException $exception) {
            throw new IOException('An error occurred while archiving file: '.$exception->getMessage());
        }
    }
}
