<?php

declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service\FileParser;

interface ClassInterfaceTraitFinderInterface
{
    /**
     * The reflector MUST only return classes/interfaces/traits which have to be aliased by this package.
     *
     * @psalm-param non-empty-string $fileName
     * @psalm-return array<int,class-string|trait-string>
     */
    public function find(string $fileName): array;
}
