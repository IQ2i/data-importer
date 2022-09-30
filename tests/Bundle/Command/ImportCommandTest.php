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

namespace IQ2i\DataImporter\Tests\Bundle\Command;

use IQ2i\DataImporter\Tests\fixtures\Command\BookImportCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class ImportCommandTest extends TestCase
{
    public function testThatCommandRunWithoutError()
    {
        $commandTester = new CommandTester(new BookImportCommand());
        $commandTester->execute([
            'filename' => __DIR__.'/../../fixtures/csv/books_with_headers.csv',
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Start importing data', $output);
        $this->assertStringContainsString('2/2 [============================] 100%', $output);
    }

    public function testThatCommandRunVerbosityToVerboseLevel()
    {
        $commandTester = new CommandTester(new BookImportCommand());
        $commandTester->execute([
            'filename' => __DIR__.'/../../fixtures/csv/books_with_headers.csv',
        ], [
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Start importing data', $output);
        $this->assertStringContainsString('author        Gambardella, Matthew', $output);
        $this->assertStringContainsString("title         XML Developer's Guide", $output);
        $this->assertStringContainsString('genre         Computer', $output);
        $this->assertStringContainsString('price         44.95', $output);
        $this->assertStringContainsString('description   An in-depth look at creating applications with XML.', $output);
    }
}
