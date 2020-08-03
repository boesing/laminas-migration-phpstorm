<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Command;

use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder;
use Boesing\Laminas\Migration\PhpStorm\Service\MetadataGenerator;
use Psr\Container\ContainerInterface;

final class GenerateCommandFactory
{
    public function __invoke(ContainerInterface $container): GenerateCommand
    {
        return new GenerateCommand(
            $container->get(LaminasFileFinder::class),
            $container->get(MetadataGenerator::class)
        );
    }
}
