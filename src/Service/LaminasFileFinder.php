<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service;

use Boesing\Laminas\Migration\PhpStorm\Service\FileParser\ClassInterfaceTraitFinderInterface;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder\ComposerJsonParserInterface;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder\File;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use function is_dir;
use function is_string;

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
     * @var ClassInterfaceTraitFinderInterface
     */
    private $classInterfaceTraitFinder;

    /**
     * @var LaminasToZendNamespaceConverterInterface
     */
    private $laminasToZendNamespaceConverter;

    public function __construct(
        ComposerJsonParserInterface $composerJsonParser,
        ClassInterfaceTraitFinderInterface $classInterfaceTraitFinder,
        LaminasToZendNamespaceConverterInterface $laminasToZendNamespaceConverter
    ) {
        $this->parser = $composerJsonParser;
        $this->classInterfaceTraitFinder = $classInterfaceTraitFinder;
        $this->laminasToZendNamespaceConverter = $laminasToZendNamespaceConverter;
    }

    /**
     * @psalm-param non-empty-string $vendor
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

        $found = false;
        foreach ($composerJsonFinder->files() as $composerJson) {
            assert($composerJson instanceof SplFileInfo);
            $directories = $this->parser->parse($composerJson);
            if (!$directories) {
                continue;
            }

            $phpFileFinder->in($directories);
            $found = true;
        }

        if (!$found) {
            return [];
        }

        $files = [];
        foreach ($phpFileFinder->files() as $phpFile) {
            assert($phpFile instanceof SplFileInfo);
            $fileName = $phpFile->getRealPath();
            if (!$fileName) {
                continue;
            }

            $findings = $this->classInterfaceTraitFinder->find($fileName);

            foreach ($findings as $classInterfaceOrTrait) {
                try {
                    $zend = $this->laminasToZendNamespaceConverter->convertToZendNamespace(
                        $classInterfaceOrTrait
                    );
                } catch (InvalidArgumentException $exception) {
                    continue;
                }

                $files[] = File::create(
                    $classInterfaceOrTrait,
                    $zend
                );
            }
        }

        return $files;
    }

    /**
     * @psalm-param non-empty-string $vendor
     */
    private function createComposerJsonFinder(string $vendor): Finder
    {
        $finder = new Finder();
        $finder
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->ignoreUnreadableDirs(true)
            ->in($this->directories($vendor))
            ->name('composer.json');

        return $finder;
    }

    /**
     * @psalm-param non-empty-string $vendorRootDirectory
     * @psalm-return list<string>
     */
    private function directories(string $vendorRootDirectory): array
    {
        $directories = [];

        foreach (self::VENDORS as $projectVendor) {
            $directory = sprintf('%s/%s', $vendorRootDirectory, $projectVendor);
            if (!is_dir($directory)) {
                continue;
            }

            $directories[] = $directory;
        }

        return $directories;
    }
}
