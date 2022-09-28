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

interface ArchiverInterface
{
    /**
     * Constructor.
     */
    public function __construct(string $rootPath);

    /**
     * Archive file after process.
     *
     * @return string Path of archived file
     */
    public function archive(\SplFileInfo $file): string;
}
