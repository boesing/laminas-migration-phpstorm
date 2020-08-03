<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder;

use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder;
use stdClass;
use Symfony\Component\Finder\SplFileInfo;
use function array_keys;
use function array_merge;
use function explode;
use function in_array;
use function is_string;
use function json_decode;
use const JSON_THROW_ON_ERROR;

final class ComposerJsonParser implements ComposerJsonParserInterface
{
    private const VENDORS = LaminasFileFinder::VENDORS;
    private const AUTOLOAD_TYPES = ['psr-0', 'psr-4'];

    public function parse(SplFileInfo $composerJson): array
    {
        $contents = json_decode($composerJson->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $autoload = $contents['autoload'] ?? [];

        if ($autoload === []) {
            return [];
        }

        $replaces = $contents['replaces'] ?? [];

        if (!$this->packageReplacesZendPackage($replaces)) {
            return [];
        }

        $directories = [];

        foreach (self::AUTOLOAD_TYPES as $autoloadType) {
            $autoloaderConfiguration = $autoload[$autoloadType] ?? [];
            if ($autoloaderConfiguration === []) {
                continue;
            }

            foreach ($autoloaderConfiguration as $directoriesFromAutoloader) {
                if (is_string($directoriesFromAutoloader)) {
                    $directoriesFromAutoloader = [$directoriesFromAutoloader];
                }

                $directories[] = $directoriesFromAutoloader;
            }
        }

        return array_merge([], ...$directories);
    }

    /**
     * @param array<string,string> $replaces
     */
    private function packageReplacesZendPackage(array $replaces): bool
    {
        foreach (array_keys((array) $replaces) as $packageName) {
            [$vendor] = explode('/', $packageName, 2);
            if (in_array($vendor, self::VENDORS, true)) {
                return true;
            }
        }

        return false;
    }
}
