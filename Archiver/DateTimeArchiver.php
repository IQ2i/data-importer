<?php

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <loic@sapone.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter\Archiver;

use Symfony\Component\Filesystem\Filesystem;

class DateTimeArchiver implements ArchiverInterface
{
    private $rootPath;

    public function __construct(string $rootPath)
    {
        $this->rootPath = rtrim($rootPath, '/');
    }

    public function archive(\SplFileInfo $file): void
    {
        // init filesystem
        $filesystem = new Filesystem();

        // get current DateTime
        $now = new \DateTime();

        // init full archive path
        $archivePath = $this->rootPath.'/'.$now->format('Y/m/d');

        // create archive path
        $filesystem->mkdir($archivePath);

        // init new filename
        $newFilename = $now->format('YmdHis').'_'.$file->getFilename();

        // move file to archive
        $filesystem->rename($file->getPathname(), $archivePath.'/'.$newFilename);
    }
}
