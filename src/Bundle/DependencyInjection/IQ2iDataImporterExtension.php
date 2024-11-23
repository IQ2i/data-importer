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

namespace IQ2i\DataImporter\Bundle\DependencyInjection;

use IQ2i\DataImporter\Command\GenerateDtoCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class IQ2iDataImporterExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container
            ->register('iq2i_data_importer.generate_dto_command', GenerateDtoCommand::class)
            ->addTag('console.command', ['command' => 'iq2i:data-importer:generate-dto'])
            ->setArguments([
                '%kernel.project_dir%/src/Dto',
                'App\\Dto',
            ]);
    }
}
