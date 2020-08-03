<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm;

use Boesing\Laminas\Migration\PhpStorm\Command\GenerateCommand;
use Boesing\Laminas\Migration\PhpStorm\Command\GenerateCommandFactory;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder\ComposerJsonParser;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder\ComposerJsonParserInterface;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinderFactory;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasToZendNamespaceConverter;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasToZendNamespaceConverterFactory;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasToZendNamespaceConverterInterface;
use Boesing\Laminas\Migration\PhpStorm\Service\MetadataGenerator;
use Boesing\Laminas\Migration\PhpStorm\Service\Reflector\Reflector;
use Boesing\Laminas\Migration\PhpStorm\Service\Reflector\ReflectorInterface;
use PhpParser\ParserFactory;

final class ConfigProvider
{
    /**
     * @return array<string,mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getServiceDependencies(),
        ];
    }

    public function getServiceDependencies(): array
    {
        return [
            'factories' => [
                ComposerJsonParser::class => static function (): ComposerJsonParser {
                    return new ComposerJsonParser();
                },
                Reflector::class => static function(): Reflector {
                    return new Reflector((new ParserFactory())->create(ParserFactory::PREFER_PHP7));
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
                ReflectorInterface::class => Reflector::class,
                ComposerJsonParserInterface::class => ComposerJsonParser::class,
            ],
        ];
    }
}
