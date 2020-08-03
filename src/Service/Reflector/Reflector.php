<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service\Reflector;

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use Roave\BetterReflection\Reflection\ReflectionClass;
use function array_merge;
use function file_get_contents;

final class Reflector implements ReflectorInterface
{
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function reflect(string $fileName): array
    {
        try {
            $statements = $this->parser->parse(file_get_contents($fileName));
        } catch (Error $error) {
            return [];
        }

        $finder = new NodeFinder();
        $nodes = [];
        $nodes[] = $finder->findInstanceOf($statements, Class_::class);
        $nodes[] = $finder->findInstanceOf($statements, Interface_::class);
        $nodes[] = $finder->findInstanceOf($statements, Trait_::class);

        /** @var Node\Stmt\ClassLike[] $nodes */
        $nodes = array_merge([], ...$nodes);

        $reflections = [];
        foreach ($nodes as $node) {
            $reflections[] = ReflectionClass::createFromName($node->namespacedName->__toString());
        }


    }
}
