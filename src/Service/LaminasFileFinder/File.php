<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder;

use function class_exists;
use function interface_exists;
use function ltrim;
use function trait_exists;

final class File
{
    /**
     * @var class-string|trait-string
     */
    public $laminas;

    /**
     * @var class-string|trait-string
     */
    public $zend;

    /**
     * @param class-string|trait-string $laminas
     * @param class-string|trait-string $zend
     */
    private function __construct(string $laminas, string $zend)
    {
        $this->laminas = $this->prefixNamespace($laminas);
        $this->zend = $this->prefixNamespace($zend);
    }

    /**
     * @param class-string|trait-string $laminas
     * @param class-string|trait-string $zend
     */
    public static function create(string $laminas, string $zend): self
    {
        return new self($laminas, $zend);
    }

    /**
     * @psalm-param class-string|trait-string $classInterfaceOrTrait
     *
     * @psalm-return class-string|trait-string
     */
    private function prefixNamespace(string $classInterfaceOrTrait): string
    {
        $converted = sprintf('\\%s', ltrim($classInterfaceOrTrait, '\\'));
        assert(
            class_exists($converted)
            || interface_exists($converted)
            || trait_exists($converted)
        );
        return $converted;
    }
}
