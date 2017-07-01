<?php

namespace DTL\TypeInference\Adapter\TolerantParser;

use Microsoft\PhpParser\Parser;
use DTL\TypeInference\Domain\TypeInferer;
use DTL\TypeInference\Domain\SourceCode;
use DTL\TypeInference\Domain\Offset;
use DTL\TypeInference\Domain\InferredType;
use Microsoft\PhpParser\Node\QualifiedName;
use Microsoft\PhpParser\Node\NamespaceUseClause;
use Microsoft\PhpParser\Node;
use Microsoft\PhpParser\Node\Parameter;
use Microsoft\PhpParser\Node\Expression\Variable;
use Microsoft\PhpParser\Node\Statement\FunctionDeclaration;
use Microsoft\PhpParser\Node\MethodDeclaration;
use DTL\TypeInference\Adapter\TolerantParser\FrameBuilder;
use DTL\TypeInference\Domain\MethodTypeResolver;
use Microsoft\PhpParser\Node\Expression\MemberAccessExpression;
use DTL\TypeInference\Domain\MethodName;

class TolerantTypeInferer implements TypeInferer
{
    private $parser;
    private $typeResolver;

    public function __construct(Parser $parser = null, MethodTypeResolver $typeResolver)
    {
        $this->parser = $parser ?: new Parser();
        $this->typeResolver = $typeResolver;
    }

    public function inferTypeAtOffset(SourceCode $code, Offset $offset): InferredType
    {
        $node = $this->parser->parseSourceFile((string) $code);
        $node = $node->getDescendantNodeAtPosition($offset->asInt());

        return $this->resolveNode($node);
    }

    private function resolveNode(Node $node)
    {
        if ($node instanceof QualifiedName) {
            return $this->resolveQualifiedName($node);
        }

        if ($node instanceof Parameter) {
            return $this->resolveQualifiedName($node->typeDeclaration);
        }

        if ($node instanceof Variable) {
            return $this->resolveVariable($node);
        }

        if ($node instanceof MemberAccessExpression) {
            return $this->resolveMemberAccess($node);
        }

        return InferredType::unknown();

    }

    private function resolveQualifiedName(Node $node)
    {
        $imports = $node->getImportTablesForCurrentScope();
        $classImports = $imports[0];

        if (isset($classImports[$node->getText()])) {
            return InferredType::fromString((string) $classImports[$node->getText()]);
        }

        if ($node->getParent() instanceof NamespaceUseClause) {
            return InferredType::fromString((string) $node->getText());
        }

        if ($namespaceDefinition = $node->getNamespaceDefinition()) {
            return InferredType::fromParts([$namespaceDefinition->name->getText(), $node->getText()]);
        }

        return InferredType::fromString($node->getText());
    }

    private function resolveVariable(Variable $node)
    {
        $frame = (new FrameBuilder())->buildUntil($node);

        $variable = $frame->get($node->getText());

        return $variable ? $variable->type() : InferredType::unknown();
    }

    private function resolveMemberAccess(MemberAccessExpression $node)
    {
        $baseType = $this->resolveNode($node->dereferencableExpression);

        if (InferredType::unknown() === $baseType) {
            return InferredType::unknown();
        }

        $memberName = $node->memberName->getText($node->getFileContents());

        return $this->typeResolver->methodType($baseType, MethodName::fromString($memberName));
    }
}
