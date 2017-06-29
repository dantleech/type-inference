<?php

namespace DTL\TypeInference\Adapter;

use Microsoft\PhpParser\Node;
use DTL\TypeInference\Domain\Frame;
use Microsoft\PhpParser\Node\MethodDeclaration;
use Microsoft\PhpParser\Node\Statement\FunctionDeclaration;
use DTL\TypeInference\Domain\Variable;
use DTL\TypeInference\Domain\InferredType;
use Microsoft\PhpParser\Node\Parameter;

final class FrameBuilder
{
    public function buildUntil(Node $node): Frame
    {
        $frame = new Frame();
        if (null === $start = $this->getFrameStart($node)) {
            return $frame;
        }

        $this->walk($frame, $start);

        return $frame;
    }

    private function getFrameStart(Node $node)
    {
        return $node->getFirstAncestor(
            MethodDeclaration::class,
            FunctionDeclaration::class
        );
    }

    private function walk(Frame $frame, Node $node)
    {
        if ($node instanceof MethodDeclaration) {
            $this->processMethodDeclaration($frame, $node);
        }
    }

    private function processMethodDeclaration(Frame $frame, MethodDeclaration $node)
    {
        foreach ($node->parameters->children as $parameter) {
            if (false === $parameter instanceof Parameter) {
                continue;
            }

            $frame->set(Variable::fromNameAndType(
                (string) $parameter->variableName->getText($node->getFileContents()),
                $this->resolveName($parameter->typeDeclaration)
            ));
        }
    }

    /**
     * TODO: Copied from TolerantTypeInferer
     */
    private function resolveName(Node $node)
    {
        $imports = $node->getImportTablesForCurrentScope();
        $classImports = $imports[0];

        if (isset($classImports[$node->getText()])) {
            return InferredType::fromString((string) $classImports[$node->getText()]);
        }

        if ($namespaceDefinition = $node->getNamespaceDefinition()) {
            return InferredType::fromParts([$namespaceDefinition->name->getText(), $node->getText()]);
        }

        return InferredType::fromString($node->getText());
    }
}
