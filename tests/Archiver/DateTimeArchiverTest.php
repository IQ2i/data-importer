<?php

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
use PHPUnit\Framework\TestCase;

class DateTimeArchiverTest extends TestCase
{
    private $fs;

    protected function setUp(): void
    {
        $this->fs = vfsStream::setup(
            'root',
            444,
            [
                'import' => [
                    'books.csv' => file_get_contents(__DIR__.'/../fixtures/csv/books_with_headers.csv'),
                ],
                'archives' => [],
            ]
        );
    }

    public function testArchive()
    {
        // create archiver
        $archiver = new DateTimeArchiver($this->fs->getChild('archives')->url());

        // init datetime
        $now = new \DateTime();

        // archive file
        $archiver->archive(new \SplFileObject($this->fs->getChild('import')->getChild('books.csv')->url()));

        // test file move
        $this->assertTrue($this->fs
            ->getChild('archives')->hasChild($now->format('Y'))
        );
        $this->assertTrue($this->fs
            ->getChild('archives')
            ->getChild($now->format('Y'))->hasChild($now->format('m'))
        );
        $this->assertTrue($this->fs
            ->getChild('archives')
            ->getChild($now->format('Y'))
            ->getChild($now->format('m'))->hasChild($now->format('d'))
        );
        $this->assertTrue($this->fs
            ->getChild('archives')
            ->getChild($now->format('Y'))
            ->getChild($now->format('m'))
            ->getChild($now->format('d'))->hasChildren()
        );
    }
}
