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

namespace IQ2i\DataImporter\Bundle\Processor;

use IQ2i\DataImporter\Bundle\Exception\ItemHandlingException;
use IQ2i\DataImporter\Exchange\Message;
use IQ2i\DataImporter\Processor\BatchProcessorInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CliProcessor implements BatchProcessorInterface
{
    private readonly SymfonyStyle $io;

    private readonly ProgressBar $progressBar;

    private readonly bool $stepByStep;

    private readonly bool $pauseOnError;

    private readonly int $batchSize;

    private array $errors = [];

    public function __construct(
        private readonly InputInterface $input,
        private readonly OutputInterface $output,
        private readonly \Closure $handleBegin,
        private readonly \Closure $handleItem,
        private readonly \Closure $handleBatch,
        private readonly \Closure $handleEnd,
        private readonly ?Serializer $serializer = new Serializer([new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())]),
    ) {
        $this->io = new SymfonyStyle($this->input, $this->output);
        $this->progressBar = new ProgressBar($this->output);

        $this->stepByStep = (bool) $this->input->getOption('step');
        if ($this->stepByStep && $this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        }

        $this->pauseOnError = (bool) $this->input->getOption('pause-on-error');
        $this->batchSize = (int) $this->input->getOption('batch-size');
    }

    public function begin(Message $message)
    {
        if (OutputInterface::VERBOSITY_NORMAL === $this->output->getVerbosity()) {
            $this->progressBar->start();
        }

        ($this->handleBegin)($message);
    }

    public function item(Message $message)
    {
        try {
            ($this->handleItem)($message);
        } catch (ItemHandlingException $itemHandlingException) {
            if ($this->pauseOnError) {
                throw $itemHandlingException;
            }

            $this->errors[$message->getCurrentIteration()] = $itemHandlingException->getMessage();
        }

        switch ($this->output->getVerbosity()) {
            case OutputInterface::VERBOSITY_NORMAL:
                $this->progressBar->setMaxSteps($message->getTotalIteration());
                $this->progressBar->advance();
                break;

            case OutputInterface::VERBOSITY_VERBOSE:
            case OutputInterface::VERBOSITY_VERY_VERBOSE:
            case OutputInterface::VERBOSITY_DEBUG:
                $this->io->comment(\sprintf('Row %d/%d',
                    $message->getCurrentIteration(),
                    $message->getTotalIteration()
                ));
                $this->io->definitionList(...$this->formatDataToDebug($message->getData()));
                break;
        }

        if ($this->stepByStep && $message->getCurrentIteration() < $message->getTotalIteration() && !$this->io->confirm('Continue?')) {
            $this->io->error('Import cancelled');
            exit;
        }
    }

    public function end(Message $message)
    {
        if (OutputInterface::VERBOSITY_NORMAL === $this->output->getVerbosity()) {
            $this->progressBar->finish();
            $this->io->newLine(2);
        }

        ($this->handleEnd)($message, $this->errors);

        if ([] !== $this->errors) {
            $elements = [];
            foreach ($this->errors as $key => $value) {
                $elements[] = \sprintf('Line #%d: %s', $key, $value);
            }

            $this->io->error('Errors occured during import:');
            $this->io->listing($elements);
        }
    }

    public function batch(Message $message)
    {
        ($this->handleBatch)($message);
    }

    public function getBatchSize()
    {
        return $this->batchSize;
    }

    private function formatDataToDebug(mixed $data): array
    {
        if (null === $data) {
            return [];
        }

        if (\is_object($data)) {
            $data = $this->serializer->normalize($data);
        }

        $result = [];
        foreach ($data as $key => $value) {
            $result[] = [$key => $value];
        }

        return $result;
    }
}
