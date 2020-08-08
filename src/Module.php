<?php

declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm;

final class Module
{
    /**
     * @psalm-return array{
     *     service_manager:array{factories:array<string,mixed>,aliases:array<string,string>},
     *     laminas-cli:array{commands: array{'migration:phpstorm-extended-meta': Command\GenerateCommand::class}}
     * }
     */
    public function getConfig(): array
    {
        $config                    = (new ConfigProvider())->__invoke();
        $config['service_manager'] = $config['dependencies'];
        unset($config['dependencies']);

        return $config;
    }
}
