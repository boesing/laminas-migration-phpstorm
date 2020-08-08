<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service;

use InvalidArgumentException;
use Laminas\ZendFrameworkBridge\RewriteRules;
use function array_flip;
use function strtr;

final class LaminasToZendNamespaceConverter implements LaminasToZendNamespaceConverterInterface
{
    public function convertToZendNamespace(string $className): string
    {
        /** @var array<string,string> $namespaces */
        $namespaces = RewriteRules::namespaceReverse();
        $converted = $this->convert($className, array_replace(
            $namespaces,
            ['Laminas\\ApiTools\\' => 'ZF\\Apigility\\']
        ));

        if ($converted) {
            return $converted;
        }

        /** @var array<string,string> $rewrites */
        $rewrites = RewriteRules::namespaceRewrite();

        return $this->convert($className, array_flip($rewrites));
    }

    /**
     * @param array<string,string> $namespaces
     * @return class-string|trait-string
     * @throws InvalidArgumentException If the className could not be converted.
     */
    private function convert(string $className, array $namespaces): string
    {
        $segments = explode('\\', $className);

        $i = 0;
        $check = '';

        while (isset($segments[$i + 1], $namespaces[$check . $segments[$i] . '\\'])) {
            $check .= $segments[$i] . '\\';
            ++$i;
        }

        if ($check === '') {
            throw new InvalidArgumentException(
                sprintf('Provided class/interface/trait "%s" could not be converted to a zend equivalent.', $className)
            );
        }

        /** @var class-string|trait-string $legacy */
        $legacy = $namespaces[$check]
            . strtr(substr($className, strlen($check)), [
                'ApiTools' => 'Apigility',
                'Mezzio' => 'Expressive',
                'Laminas' => 'Zend',
            ]);

        if ($legacy !== $className) {
            return $legacy;
        }

        throw new InvalidArgumentException(
            sprintf('Provided class/interface/trait "%s" could not be converted to a zend equivalent.', $className)
        );
    }
}
