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

        return $this->resolveNode($node);
    }

    private function resolveNode(Node $node)
    {
        if ($node instanceof QualifiedName) {
            return $this->fqnResolver->resolveQualifiedName($node);
        }

        if ($node instanceof Parameter) {
            return $this->fqnResolver->resolveQualifiedName($node->typeDeclaration);
        }

        if ($node instanceof Variable) {
            return $this->resolveVariable($node);
        }

        if ($node instanceof MemberAccessExpression) {
            return $this->resolveMemberAccess($node);
        }

        return InferredType::unknown();

    }


    private function resolveVariable(Variable $node)
    {
        $frame = (new FrameBuilder())->buildUntil($node);

        $variable = $frame->get($node->getText());

        return $variable ? $variable->type() : InferredType::unknown();
    }

    private function resolveMemberAccess(MemberAccessExpression $node, $list = [])
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
                $context = $this->resolveNode($ancestor);

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
