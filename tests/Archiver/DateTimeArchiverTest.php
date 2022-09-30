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

namespace IQ2i\DataImporter\Tests\Archiver;

use IQ2i\DataImporter\Archiver\DateTimeArchiver;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class DateTimeArchiverTest extends TestCase
{
    private vfsStreamDirectory $fs;

    protected function setUp(): void
    {
        $this->fs = vfsStream::setup(
            'root',
            444,
            [
                'import' => [
                    'books.csv' => \file_get_contents(__DIR__.'/../fixtures/csv/books_with_headers.csv'),
                ],
                'archives' => [],
            ]
        );
    }

    public function testArchive()
    {
        $archiverMock = $this->getMockBuilder(DateTimeArchiver::class)
            ->setConstructorArgs([$this->fs->getChild('archives')->url()])
            ->onlyMethods(['now'])
            ->getMock();
        $archiverMock
            ->method('now')
            ->willReturn(new \DateTime('2016-09-10 07:24:00'));

        $archiveFilePath = $archiverMock->archive(new \SplFileObject($this->fs->getChild('import')->getChild('books.csv')->url()));

        $this->assertEquals($this->fs->getChild('archives/2016/09/10/20160910072400_books.csv')->url(), $archiveFilePath);
    }
}
