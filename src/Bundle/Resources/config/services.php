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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use IQ2i\DataImporter\Command\GenerateDtoCommand;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('iq2i_data_importer.generate_dto_command', GenerateDtoCommand::class)
            ->arg('$defaultPath', '%kernel.project_dir%')
            ->arg('$defaultNamespace', 'App\\Dto')
            ->tag('console.command', ['command' => 'iq2i:data-importer:generate-dto'])
    ;
};
