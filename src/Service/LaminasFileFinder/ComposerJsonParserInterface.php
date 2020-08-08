<?php

declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder;

use Symfony\Component\Finder\SplFileInfo;

interface ComposerJsonParserInterface
{
    /**
     * Returns a list of directories to scan for interfaces, classes and traits.
     * Should only return directories if the `composer.json` replaces a zend package.
     *
     * @psalm-return list<string>
     */
    public function parse(SplFileInfo $composerJson): array;
}
