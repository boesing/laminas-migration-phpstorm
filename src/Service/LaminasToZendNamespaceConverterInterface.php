<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service;

interface LaminasToZendNamespaceConverterInterface
{
    /**
     * Converts laminas namespace to zend namespace.
     * MUST return an empty string when zend namespace could not properly be detected.
     *
     * @psalm-param class-string $className
     */
    public function convertToZendNamespace(string $className): string;
}
