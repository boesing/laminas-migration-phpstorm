<?php

declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service;

use Boesing\Laminas\Migration\PhpStorm\Service\FileParser\ClassInterfaceTraitFinderInterface;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder\ComposerJsonParserInterface;
use Psr\Container\ContainerInterface;

final class LaminasFileFinderFactory
{
    public function __invoke(ContainerInterface $container): LaminasFileFinder
    {
        return new LaminasFileFinder(
            $container->get(ComposerJsonParserInterface::class),
            $container->get(ClassInterfaceTraitFinderInterface::class),
            $container->get(LaminasToZendNamespaceConverterInterface::class)
        );
    }
}
