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

namespace IQ2i\DataImporter\Dto;

use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;

use function Symfony\Component\String\u;

class Generator
{
    /**
     * @var string
     */
    private const NAMESPACE = 'App\Dto';

    public function generate(string $class, array $columns, string $namespace = null): string
    {
        $file = new PhpFile();
        $file->setStrictTypes();

        $namespace = $file->addNamespace($namespace ?? self::NAMESPACE);
        $class = $namespace->addClass($class);

        foreach ($columns as $column) {
            $property = $class->addProperty($column['name'])
                ->setPrivate()
                ->setType('?'.$column['type'])
                ->setInitialized();

            if (null !== $column['serialized_name']) {
                $namespace->addUse(\Symfony\Component\Serializer\Annotation\SerializedName::class);

                $property->addAttribute(\Symfony\Component\Serializer\Annotation\SerializedName::class, [$column['serialized_name']]);
            }
        }

        foreach ($columns as $column) {
            $class->addMethod('get'.u($column['name'])->camel()->title())
                ->setPublic()
                ->setReturnType('?'.$column['type'])
                ->addBody('return $this->?;', [$column['name']]);

            $setter = $class->addMethod('set'.u($column['name'])->camel()->title())
                ->setPublic()
                ->setReturnType('self')
                ->addBody('$this->? = $?;', [$column['name'], $column['name']])
                ->addBody('')
                ->addBody('return $this;');

            $setter->addParameter($column['name'], null)
                ->setType($column['type']);
        }

        $printer = new PsrPrinter();

        return $printer->printFile($file);
    }
}
