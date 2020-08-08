<?php

declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm;

use DateTimeImmutable;
use DateTimeInterface;

use function implode;
use function sprintf;

final class Metadata
{
    private const TEMPLATE = <<<EOT
<?php
declare(strict_types=1);

/**
 * This file was dynamically created on %s.
 * Please re-run `vendor/bin/laminas migration:phpstorm-extended-meta` if you want to update this. 
 */
namespace PHPSTORM_META {
    %s
}
EOT;

    /** @psalm-var list<string> */
    private $aliases = [];

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function withAlias(string $alias): self
    {
        $instance            = clone $this;
        $instance->aliases[] = $alias;

        return $instance;
    }

    public function toString(): string
    {
        return sprintf(
            self::TEMPLATE,
            (new DateTimeImmutable())->format(DateTimeInterface::RFC3339),
            implode("\n", $this->aliases)
        );
    }
}
