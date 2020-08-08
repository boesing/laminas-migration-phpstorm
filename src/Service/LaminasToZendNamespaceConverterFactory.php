<?php

declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service;

final class LaminasToZendNamespaceConverterFactory
{
    public function __invoke(): LaminasToZendNamespaceConverter
    {
        return new LaminasToZendNamespaceConverter();
    }
}
