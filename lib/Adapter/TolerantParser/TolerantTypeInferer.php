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
use DTL\TypeInference\Domain\MessageLog;
use DTL\TypeInference\Domain\InferredTypeResult;

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

    public function inferTypeAtOffset(SourceCode $code, Offset $offset): InferredTypeResult
    {
        $node = $this->parser->parseSourceFile((string) $code);
        $node = $node->getDescendantNodeAtPosition($offset->asInt());
        $frame = (new FrameBuilder())->buildUntil($node);
        $log = new MessageLog();

        return InferredTypeResult::fromTypeFrameAndMessageLog(
            $this->resolveNode($log, $frame, $node), new ResolvedFrame($this, $frame), $log
        );
    }

    public function resolveNode(MessageLog $log, Frame $frame, Node $node)
    {
        if ($node instanceof QualifiedName) {
            return $this->fqnResolver->resolveQualifiedName($node);
        }

        if ($node instanceof Parameter) {
            return $this->fqnResolver->resolveQualifiedName($node->typeDeclaration);
        }

        if ($node instanceof Variable) {
            return $this->resolveVariable($log, $frame, $node->getText());
        }

        if ($node instanceof MemberAccessExpression || $node instanceof CallExpression) {
            return $this->resolveMemberAccess($log, $frame, $node);
        }

        if ($node instanceof ClassDeclaration || $node instanceof InterfaceDeclaration) {
            return InferredType::fromString($node->getNamespacedName());
        }

        if ($node instanceof ObjectCreationExpression) {
            return $this->fqnResolver->resolveQualifiedName($node->classTypeDesignator);
        }

        if ($node instanceof SubscriptExpression) {
            return $this->resolveVariable($log, $frame, $node->getText());
        }

        $log->log(sprintf('Could not infer type for node of type "%s"', get_class($node)));

        return InferredType::unknown();
    }


    private function resolveVariable(MessageLog $log, Frame $frame, string $name)
    {
        $assignedNode = $frame->get($name);

        if (null === $assignedNode) {
            $log->log(sprintf('Variable "%s" was not assigned', $name));

            return InferredType::unknown();
        }

        return $this->resolveNode($log, $frame, $assignedNode);
    }

    private function resolveMemberAccess(MessageLog $log, Frame $frame, Expression $node, $list = [])
    {
        $ancestors = [];
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

        $parent = null;
        foreach ($ancestors as $ancestor) {
            if ($parent === null) {
                $parent = $this->resolveNode($log, $frame, $ancestor);

                if (InferredType::unknown() == $parent) {
                    return InferredType::unknown();
                }

                continue;
            }

            $type = $this->resolveMemberType($log, $parent, $ancestor);
            $parent = $type;
        }

        return $type;
    }

    private function resolveMemberType(MessageLog $log, InferredType $parent, $node)
    {
        $memberName = $node->memberName->getText($node->getFileContents());

        $type = $this->typeResolver->methodType($log, $parent, MethodName::fromString($memberName));

        if (InferredType::unknown() != $type) {
            return $type;
        }

        $type = $this->typeResolver->propertyType($log, $parent, MethodName::fromString($memberName));

        return $type;
    }
}
