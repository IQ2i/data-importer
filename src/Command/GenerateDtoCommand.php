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

namespace IQ2i\DataImporter\Command;

use IQ2i\DataImporter\Dto\Generator;
use IQ2i\DataImporter\Reader\CsvReader;
use IQ2i\DataImporter\Reader\ReaderInterface;
use IQ2i\DataImporter\Reader\XmlReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

use function Symfony\Component\String\u;

class GenerateDtoCommand extends Command
{
    public function __construct(
        private ?string $defaultPath = null,
        private ?string $defaultNamespace = null,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('generate')
            ->setDescription('Generate DTO from file to import.')
            ->addArgument('file', InputArgument::REQUIRED, 'The file from which the DTO should be generated.')
            ->addOption('length', null, InputOption::VALUE_OPTIONAL, 'Number of lines to analyze.', 10)
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'Customize the path for generated DTOs')
            ->addOption('namespace', null, InputOption::VALUE_REQUIRED, 'Customize the namespace for generated DTOs')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();
        $io = new SymfonyStyle($input, $output);

        $file = $input->getArgument('file');
        if (!\file_exists($file)) {
            throw new \InvalidArgumentException(\sprintf('File "%s" does not exists.', $file));
        }

        if (null === $this->defaultPath || $input->hasOption('path')) {
            $this->defaultPath = $input->getOption('path') ?? $io->ask("Specify the DTO's path");
        }

        $this->defaultPath = \rtrim((string) $this->defaultPath, '/');

        if (null === $this->defaultNamespace || $input->hasOption('namespace')) {
            $this->defaultNamespace = $input->getOption('namespace') ?? $io->ask("Specify the DTO's namespace");
        }

        $dtoClass = $io->ask('Class name of the entity to create or update');
        $dtoFilename = $this->defaultPath.'/'.$dtoClass.'.php';
        if ($filesystem->exists($dtoFilename) && !$io->confirm(\sprintf('File %s already exists. Do you want to override it?', $dtoFilename))) {
            return Command::SUCCESS;
        }

        $readerClass = 'IQ2i\\DataImporter\\Reader\\'.$io->choice(
            'Which reader do you want to use?',
            ['CsvReader', 'JsonReader', 'XmlReader']
        );

        $context = match ($readerClass) {
            CsvReader::class => [
                CsvReader::CONTEXT_DELIMITER => $io->ask('Specify the delimiter', ','),
                CsvReader::CONTEXT_ENCLOSURE => $io->ask('Specify the enclosure', '"'),
                CsvReader::CONTEXT_ESCAPE_CHAR => $io->ask('Specify the escape character', ''),
            ],
            XmlReader::class => [
                XmlReader::CONTEXT_XPATH => $io->ask('Specify the xpath', ''),
            ],
            default => [],
        };

        /** @var ReaderInterface $reader */
        $reader = new $readerClass($file, null, $context);

        // init properties array
        $properties = [];
        foreach ($reader->current() as $key => $value) {
            $name = u($key)->camel()->toString();
            $properties[$key] = [
                'name' => $name,
                'serialized_name' => $name !== $key ? $key : null,
                'types' => [self::findPropertyType($value)],
            ];
        }

        for ($i = 0; $i < $input->getOption('length'); ++$i) {
            foreach ($reader->current() as $key => $value) {
                $properties[$key]['types'][] = self::findPropertyType($value);
            }

            $reader->next();
        }

        foreach ($properties as $property) {
            $property['type'] = 1 === \count(\array_unique($property['types'], \SORT_REGULAR))
                ? $property['types'][0]
                : 'string';
        }

        $generator = new Generator();
        $generatedDto = $generator->generate($dtoClass, [
            ['name' => 'author', 'serialized_name' => null, 'type' => 'string'],
            ['name' => 'title', 'serialized_name' => null, 'type' => 'string'],
            ['name' => 'genre', 'serialized_name' => null, 'type' => 'string'],
            ['name' => 'price', 'serialized_name' => null, 'type' => 'float'],
            ['name' => 'description', 'serialized_name' => null, 'type' => 'string'],
        ], $this->defaultNamespace);

        if (!$filesystem->exists($this->defaultPath)) {
            $filesystem->mkdir($this->defaultPath);
        }

        $filesystem->dumpFile($dtoFilename, $generatedDto);

        return Command::SUCCESS;
    }

    private static function findPropertyType(string $value): string
    {
        if (\is_numeric($value) && \str_contains($value, '.')) {
            return 'float';
        } elseif (\is_numeric($value) && !\in_array($value, ['0', '1'])) {
            return 'int';
        } elseif (\in_array($value, ['0', '1', 'true', 'false'])) {
            return 'bool';
        } else {
            return 'string';
        }
    }
}
