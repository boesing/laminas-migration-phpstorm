<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm;

use DateTimeInterface;
use function implode;
use function sprintf;

final class Metadata
{
    private const TEMPLATE = <<<EOT
/**
 * This file is dynamically created on %s.
 * Please re-run `vendor/bin/laminas migration:phpstorm-extended-meta` if you want to update this. 
 */
 namespace PHPSTORM_META {
    %s
}
EOT;

    /**
     * @psalm-var list<string>
     */
    private $aliases = [];

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self;
    }

    public function withAlias(string $alias): self
    {
        $instance = new self;
        $instance->aliases[] = $alias;

        return $instance;
    }

    public function toString(): string
    {
        return sprintf(self::TEMPLATE, date(DateTimeInterface::ATOM), implode("\n", $this->aliases));
    }
}
