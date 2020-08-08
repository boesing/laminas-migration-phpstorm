<?php

declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder;

use Symfony\Component\Finder\SplFileInfo;

use function array_keys;
use function array_map;
use function array_merge;
use function dirname;
use function explode;
use function in_array;
use function is_string;
use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

final class ComposerJsonParser implements ComposerJsonParserInterface
{
    private const VENDORS        = [
        'zfcampus',
        'zendframework',
    ];
    private const AUTOLOAD_TYPES = ['psr-0', 'psr-4'];

    public function parse(SplFileInfo $composerJson): array
    {
        /**
         * @psalm-var array{
         *     autoload: array{
         *          psr-0?:array<string,string>,
         *          psr-4?:array<string,string>
         *     },
         *     replace:array<string,string>
         * } $contents
         */
        $contents = json_decode($composerJson->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $autoload = $contents['autoload'] ?? [];

        if ($autoload === []) {
            return [];
        }

        $replaces = $contents['replace'] ?? [];

        if (! $this->packageReplacesZendPackage($replaces)) {
            return [];
        }

        $directories = [];

        foreach (self::AUTOLOAD_TYPES as $autoloadType) {
            /** @psalm-var array<string,string|list<string>> $autoloaderConfiguration */
            $autoloaderConfiguration = $autoload[$autoloadType] ?? [];
            if ($autoloaderConfiguration === []) {
                continue;
            }

            foreach ($autoloaderConfiguration as $directoriesFromAutoloader) {
                if (is_string($directoriesFromAutoloader)) {
                    $directoriesFromAutoloader = [$directoriesFromAutoloader];
                }

                $directories[] = array_map(
                    static function (string $directory) use ($composerJson): string {
                        return sprintf('%s/%s', dirname($composerJson->getRealPath()), $directory);
                    },
                    $directoriesFromAutoloader
                );
            }
        }

        return array_merge([], ...$directories);
    }

    /**
     * @psalm-param array<string,string> $replaces
     */
    private function packageReplacesZendPackage(array $replaces): bool
    {
        foreach (array_keys($replaces) as $packageName) {
            [$vendor] = explode('/', $packageName, 2);
            if (in_array($vendor, self::VENDORS, true)) {
                return true;
            }
        }

        return false;
    }
}
