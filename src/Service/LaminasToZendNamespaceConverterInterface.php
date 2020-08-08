<?php

declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service;

use InvalidArgumentException;

interface LaminasToZendNamespaceConverterInterface
{
    /**
     * Converts laminas namespace to zend namespace.
     * MUST return an empty string when zend namespace could not properly be detected.
     *
     * @psalm-param class-string|trait-string $className
     * @psalm-return class-string|trait-string
     * @throws InvalidArgumentException If the class name could not be converted to zend equivalent.
     */
    public function convertToZendNamespace(string $className): string;
}
