<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder;

use function ltrim;

final class File
{
    /**
     * @var class-string|trait-string
     */
    public $laminas;

    /**
     * @var string
     */
    public $zend;

    /**
     * @param class-string|trait-string $laminas
     */
    private function __construct(string $laminas, string $zend)
    {
        $this->laminas = $this->prefixNamespace($laminas);
        $this->zend = $zend;
    }

    /**
     * @param class-string|trait-string $laminas
     */
    public static function create(string $laminas, string $zend): self
    {
        return new self($laminas, $zend);
    }

    /**
     * @psalm-param class-string|trait-string $classInterfaceOrTrait
     *
     * @psalm-return class-string|trait-string
     * @psalm-suppress MoreSpecificReturnType
     */
    private function prefixNamespace(string $classInterfaceOrTrait): string
    {
        /** @psalm-suppress LessSpecificReturnStatement */
        return sprintf('\\%s', ltrim($classInterfaceOrTrait, '\\'));
    }
}
