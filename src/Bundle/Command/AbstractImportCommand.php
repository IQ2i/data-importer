<?php

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <loic@sapone.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter\Bundle\Command;

use IQ2i\DataImporter\Archiver\ArchiverInterface;
use IQ2i\DataImporter\Bundle\Exception\ItemHandlingException;
use IQ2i\DataImporter\Bundle\Processor\CliProcessor;
use IQ2i\DataImporter\DataImporter;
use IQ2i\DataImporter\Processor\ProcessorInterface;
use IQ2i\DataImporter\Reader\ReaderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Serializer;

abstract class AbstractImportCommand extends Command
{
    protected InputInterface $input;
    protected OutputInterface $output;

    abstract protected function handleItem(): callable;

    abstract protected function getReader(?string $filename = null): ReaderInterface;

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::OPTIONAL, 'File to import')
            ->addOption('step', null, InputOption::VALUE_NONE, 'Step through each record one-by-one')
            ->addOption('pause-on-error', null, InputOption::VALUE_NONE, 'Pause if an exception is thrown')
            ->addOption('batch-size', null, InputOption::VALUE_REQUIRED, 'Batch size', 100)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        $io = new SymfonyStyle($input, $output);
        $io->title('Start importing data');

        try {
            (new DataImporter(
                $this->getReader($input->getArgument('filename')),
                $this->getProcessor($input, $output),
                $this->getArchiver(),
                $this->getSerializer()
            ))->execute();
        } catch (ItemHandlingException $exception) {
            $io->newLine(2);
            $io->error($exception->getMessage());

            return $exception->getCode();
        }

        return Command::SUCCESS;
    }

    protected function handleBatch(): callable
    {
        return function () {};
    }

    protected function getProcessor(InputInterface $input, OutputInterface $output): ProcessorInterface
    {
        return new CliProcessor($input, $output, $this->handleItem(), $this->handleBatch(), $this->getSerializer());
    }

    protected function getArchiver(): ?ArchiverInterface
    {
        return null;
    }

    protected function getSerializer(): ?Serializer
    {
        return null;
    }
}
