<?php

namespace Phpactor\TypeInference\Adapter\TolerantParser;

use Microsoft\PhpParser\Node;
use Phpactor\TypeInference\Domain\InferredType;
use Microsoft\PhpParser\Node\NamespaceUseClause;
use Microsoft\PhpParser\Node\Statement\InterfaceDeclaration;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;

final class FullyQualifiedNameResolver
{
    public function resolveQualifiedName(Node $node, string $name = null): InferredType
    {
        $name = $name ?: $node->getText();

        if (substr($name, 0, 1) === '\\') {
            return InferredType::fromString($name);
        }

        if ($name == 'self') {
            $class = $node->getFirstAncestor(ClassDeclaration::class, InterfaceDeclaration::class);
            return InferredType::fromString($class->getNamespacedName());
        }

        $imports = $node->getImportTablesForCurrentScope();
        $classImports = $imports[0];

        if (isset($classImports[$name])) {
            return InferredType::fromString((string) $classImports[$name]);
        }

        if ($node->getParent() instanceof NamespaceUseClause) {
            return InferredType::fromString((string) $name);
        }

        if ($namespaceDefinition = $node->getNamespaceDefinition()) {
            return InferredType::fromParts([$namespaceDefinition->name->getText(), $name]);
        }

        return InferredType::fromString($name);
    }
}
