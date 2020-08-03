<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service;

use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder\ComposerJsonParserInterface;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder\File;
use Boesing\Laminas\Migration\PhpStorm\Service\Reflector\ReflectorInterface;
use Symfony\Component\Finder\Finder;

final class LaminasFileFinder
{
    public const VENDORS = [
        'laminas',
        'laminas-api-tools',
        'mezzio',
    ];

    /**
     * @var ComposerJsonParserInterface
     */
    private $parser;

    /**
     * @var ReflectorInterface
     */
    private $reflector;

    /**
     * @var LaminasToZendNamespaceConverterInterface
     */
    private $laminasToZendNamespaceConverter;

    /**
     * @var ComposerJsonParserInterface
     */
    private $composerJsonParser;

    public function __construct(
        ComposerJsonParserInterface $composerJsonParser,
        ReflectorInterface $reflector,
        LaminasToZendNamespaceConverterInterface $laminasToZendNamespaceConverter
    ) {
        $this->composerJsonParser = $composerJsonParser;
        $this->reflector = $reflector;
        $this->laminasToZendNamespaceConverter = $laminasToZendNamespaceConverter;
    }

    /**
     * @param string $vendor
     *
     * @return File[]
     *
     * @psalm-return list<File>
     */
    public function find(string $vendor): array
    {
        $composerJsonFinder = $this->createComposerJsonFinder($vendor);
        $phpFileFinder = new Finder();
        $phpFileFinder->name('*.php');

        foreach ($composerJsonFinder->files() as $composerJson) {
            $directories = $this->parser->parse($composerJson);
            if (!$directories) {
                continue;
            }

            $phpFileFinder->in($directories);
        }

        $files = [];
        foreach ($phpFileFinder->files() as $phpFile) {
            $reflections = $this->reflector->reflect($phpFile->getRealPath());

            foreach ($reflections as $reflection) {
                $zend = $this->laminasToZendNamespaceConverter->convertToZendNamespace(
                    $reflection->getName()
                );

                if (!$zend) {
                    continue;
                }

                $files[] = File::create(
                    $reflection->getName(),
                    $zend
                );
            }
        }

        return $files;
    }

    private function createComposerJsonFinder(string $vendor): Finder
    {
        $finder = new Finder();
        $finder
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->in($this->directories($vendor))
            ->name('composer.json');

        return $finder;
    }

    /**
     * @return string[]
     *
     * @psalm-return non-empty-list<string>
     */
    private function directories(string $vendorRootDirectory): array
    {
        $directories = [];

        foreach (self::VENDORS as $projectVendor) {
            $directories[] = sprintf('%s/%s', $vendorRootDirectory, $projectVendor);
        }

        return $directories;
    }
}
