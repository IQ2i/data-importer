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

namespace IQ2i\DataImporter\Bundle\Console;

use IQ2i\DataImporter\Command\GenerateDtoCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('DataImporter');

        $generateDtoCommand = new GenerateDtoCommand();

        $this->add($generateDtoCommand);
        $this->setDefaultCommand($generateDtoCommand->getName(), true);
    }
}
