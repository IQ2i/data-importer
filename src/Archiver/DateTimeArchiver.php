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
    private string $rootPath;

    public function __construct(string $rootPath)
    {
        $this->rootPath = rtrim($rootPath, '/');
    }

    public function archive(\SplFileInfo $file): string
    {
        $now = $this->now();
        $archivePath = $this->rootPath.'/'.$now->format('Y/m/d');
        $archiveFileName = $archivePath.'/'.$now->format('YmdHis').'_'.$file->getFilename();

        $filesystem = new Filesystem();
        $filesystem->mkdir($archivePath);
        $filesystem->rename($file->getPathname(), $archiveFileName);

        return $archiveFileName;
    }

    public function now(): \DateTimeInterface
    {
        return new \DateTime();
    }
}
