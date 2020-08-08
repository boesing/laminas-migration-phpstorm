<?php

declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service\FileParser;

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FindingVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;

use function assert;
use function file_get_contents;

final class ClassInterfaceTraitFinder implements ClassInterfaceTraitFinderInterface
{
    /** @var Parser */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function find(string $fileName): array
    {
        $statements = [];
        try {
            $statements = $this->parser->parse(file_get_contents($fileName));
        } catch (Error $error) {
        } finally {
            $statements = $statements ?? [];
        }

        if ($statements === []) {
            return [];
        }

        $traverser                 = new NodeTraverser();
        $classInterfaceTraitFinder = new FindingVisitor(static function (Node $node): bool {
            return $node instanceof Class_ || $node instanceof Interface_ || $node instanceof Trait_;
        });

        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor($classInterfaceTraitFinder);
        $traverser->traverse($statements);

        $nodes = $classInterfaceTraitFinder->getFoundNodes();

        $reflections = [];
        foreach ($nodes as $node) {
            assert($node instanceof Node\Stmt\ClassLike);
            /**
             * @psalm-suppress UndefinedPropertyFetch
             * @var Node\Name|null $namespacedName
             */
            $namespacedName = $node->namespacedName ?? null;
            if (! $namespacedName instanceof Node\Name) {
                continue;
            }

            /** @var class-string|trait-string $classInterfaceOrTrait */
            $classInterfaceOrTrait = $namespacedName->__toString();

            $reflections[] = $classInterfaceOrTrait;
        }

        return $reflections;
    }
}
