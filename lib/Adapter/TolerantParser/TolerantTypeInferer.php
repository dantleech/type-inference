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
use DTL\TypeInference\Domain\MemberTypeResolver;
use Microsoft\PhpParser\Node\Expression\MemberAccessExpression;
use DTL\TypeInference\Domain\MethodName;
use DTL\TypeInference\Adapter\Dummy\DummyMethodTypeResolver;
use Microsoft\PhpParser\Node\Expression\CallExpression;
use Microsoft\PhpParser\Node\Expression;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\Statement\InterfaceDeclaration;
use Microsoft\PhpParser\Node\Expression\ObjectCreationExpression;
use Microsoft\PhpParser\Node\Expression\SubscriptExpression;

class TolerantTypeInferer implements TypeInferer
{
    private $parser;
    private $typeResolver;
    private $nameResolver;

    public function __construct(Parser $parser = null, MemberTypeResolver $typeResolver = null)
    {
        $this->parser = $parser ?: new Parser();
        $this->typeResolver = $typeResolver ?: new DummyMethodTypeResolver();
        $this->fqnResolver = new FullyQualifiedNameResolver();
    }

    public function inferTypeAtOffset(SourceCode $code, Offset $offset): InferredType
    {
        $node = $this->parser->parseSourceFile((string) $code);
        $node = $node->getDescendantNodeAtPosition($offset->asInt());
        $frame = (new FrameBuilder())->buildUntil($node);

        return $this->resolveNode($frame, $node);
    }

    private function resolveNode(Frame $frame, Node $node)
    {
        if ($node instanceof QualifiedName) {
            return $this->fqnResolver->resolveQualifiedName($node);
        }

        if ($node instanceof Parameter) {
            return $this->fqnResolver->resolveQualifiedName($node->typeDeclaration);
        }

        if ($node instanceof Variable) {
            return $this->resolveVariable($frame, $node->getText());
        }

        if ($node instanceof MemberAccessExpression || $node instanceof CallExpression) {
            return $this->resolveMemberAccess($frame, $node);
        }

        if ($node instanceof ClassDeclaration || $node instanceof InterfaceDeclaration) {
            return InferredType::fromString($node->getNamespacedName());
        }

        if ($node instanceof ObjectCreationExpression) {
            return $this->fqnResolver->resolveQualifiedName($node->classTypeDesignator);
        }

        if ($node instanceof SubscriptExpression) {
            return $this->resolveVariable($frame, $node->getText());
        }

        return InferredType::unknown();
    }


    private function resolveVariable(Frame $frame, string $name)
    {
        $assignedNode = $frame->get($name);

        if (null === $assignedNode) {
            return InferredType::unknown();
        }

        return $this->resolveNode($frame, $assignedNode);
    }

    private function resolveMemberAccess(Frame $frame, Expression $node, $list = [])
    {
        $ancestors = [  ];
        while ($node instanceof MemberAccessExpression || $node instanceof CallExpression) {
            if ($node instanceof CallExpression) {
                $node = $node->callableExpression;
                continue;
            }

            $ancestors[] = $node;
            $node = $node->dereferencableExpression;
        }

        $ancestors[] = $node;

        $ancestors = array_reverse($ancestors);

        $context = null;
        foreach ($ancestors as $ancestor) {
            if ($context === null) {
                $context = $this->resolveNode($frame, $ancestor);

                if (InferredType::unknown() == $context) {
                    return InferredType::unknown();
                }

                continue;
            }

            $type = $this->resolveMemberType($context, $ancestor);
            $context = $type;
        }

        return $type;
    }

    private function resolveMemberType(InferredType $context, $node)
    {
        $memberName = $node->memberName->getText($node->getFileContents());

        $type = $this->typeResolver->methodType($context, MethodName::fromString($memberName));

        if (InferredType::unknown() != $type) {
            return $type;
        }

        $type = $this->typeResolver->propertyType($context, MethodName::fromString($memberName));

        return $type;
    }
}
