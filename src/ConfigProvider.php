<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm;

use Boesing\Laminas\Migration\PhpStorm\Command\GenerateCommand;
use Boesing\Laminas\Migration\PhpStorm\Command\GenerateCommandFactory;
use Boesing\Laminas\Migration\PhpStorm\Service\FileParser\ClassInterfaceTraitFinder;
use Boesing\Laminas\Migration\PhpStorm\Service\FileParser\ClassInterfaceTraitFinderInterface;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder\ComposerJsonParser;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder\ComposerJsonParserInterface;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinderFactory;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasToZendNamespaceConverter;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasToZendNamespaceConverterFactory;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasToZendNamespaceConverterInterface;
use Boesing\Laminas\Migration\PhpStorm\Service\MetadataGenerator;
use PhpParser\ParserFactory;

final class ConfigProvider
{

    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getServiceDependencies(),
            'laminas-cli' => $this->laminasCliConfiguration(),
        ];
    }

    /**
     * @psalm-return array{factories:array<string,mixed>,aliases:array<string,string>}
     */
    public function getServiceDependencies(): array
    {
        return [
            'factories' => [
                ComposerJsonParser::class => static function (): ComposerJsonParser {
                    return new ComposerJsonParser();
                },
                ClassInterfaceTraitFinder::class => static function (): ClassInterfaceTraitFinder {
                    return new ClassInterfaceTraitFinder(
                        (new ParserFactory())->create(ParserFactory::PREFER_PHP7)
                    );
                },
                LaminasFileFinder::class => LaminasFileFinderFactory::class,
                LaminasToZendNamespaceConverter::class => LaminasToZendNamespaceConverterFactory::class,
                MetadataGenerator::class => static function (): MetadataGenerator {
                    return new MetadataGenerator();
                },
                GenerateCommand::class => GenerateCommandFactory::class,
            ],
            'aliases' => [
                LaminasToZendNamespaceConverterInterface::class => LaminasToZendNamespaceConverter::class,
                ClassInterfaceTraitFinderInterface::class => ClassInterfaceTraitFinder::class,
                ComposerJsonParserInterface::class => ComposerJsonParser::class,
            ],
        ];
    }

    /**
     * @psalm-return array{commands: array{'migration:phpstorm-extended-meta': Command\GenerateCommand::class}}
     */
    private function laminasCliConfiguration(): array
    {
        return [
            'commands' => [
                'migration:phpstorm-extended-meta' => GenerateCommand::class,
            ],
        ];
    }
}
