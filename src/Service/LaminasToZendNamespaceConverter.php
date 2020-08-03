<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service;

use Laminas\ZendFrameworkBridge\RewriteRules;
use function array_flip;
use function strtr;

final class LaminasToZendNamespaceConverter implements LaminasToZendNamespaceConverterInterface
{
    /**
     * @return string
     */
    public function convertToZendNamespace(string $className): string
    {
        $converted = $this->convert($className, array_replace(
            RewriteRules::namespaceReverse(),
            ['Laminas\\ApiTools\\' => 'ZF\\Apigility\\']
        ));
        if ($converted) {
            return $converted;
        }

        $converted = $this->convert($className, array_flip(RewriteRules::namespaceRewrite()));
        if ($converted) {
            return $converted;
        }

        return '';
    }

    /**
     * @param array<string,string> $namespaceReverse
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
            return '';
        }

        $legacy = $namespaces[$check]
            . strtr(substr($className, strlen($check)), [
                'ApiTools' => 'Apigility',
                'Mezzio' => 'Expressive',
                'Laminas' => 'Zend',
            ]);

        if ($legacy !== $className) {
            return $legacy;
        }

        return '';
    }
}
