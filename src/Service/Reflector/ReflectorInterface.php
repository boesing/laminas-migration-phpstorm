<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service\Reflector;

use Roave\BetterReflection\Reflection\ReflectionClass;

interface ReflectorInterface
{
    /**
     * The reflector MUST only return classes/interfaces/traits which have to be aliased by this package.
     *
     * @return array<int,ReflectionClass>
     */
    public function reflect(string $fileName): array;
}
