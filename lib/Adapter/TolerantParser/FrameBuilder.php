<?php

namespace DTL\TypeInference\Adapter\TolerantParser;

use Microsoft\PhpParser\Node;
use Microsoft\PhpParser\Node\MethodDeclaration;
use Microsoft\PhpParser\Node\Statement\FunctionDeclaration;
use DTL\TypeInference\Domain\Variable;
use DTL\TypeInference\Domain\InferredType;
use Microsoft\PhpParser\Node\Parameter;
use Microsoft\PhpParser\Node\Expression\AssignmentExpression;
use Microsoft\PhpParser\Node\Expression\Variable as ExprVariable;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Token;
use Microsoft\PhpParser\Node\SourceFileNode;
use Microsoft\PhpParser\Node\Statement\ForeachStatement;
use DTL\TypeInference\Domain\DocblockParser;

final class FrameBuilder
{
    /**
     * @var DocblockParser
     */
    private $docblockParser;

    public function __construct()
    {
        $this->docblockParser = new DocblockParser();
    }

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
            FunctionDeclaration::class,
            SourceFileNode::class
        );
    }

    private function walk(Frame $frame, Node $node)
    {
        if ($node instanceof MethodDeclaration) {
            $this->processMethodDeclaration($frame, $node);
        }

        if ($node instanceof AssignmentExpression) {
            $frame->set(
                $node->leftOperand,
                $node->rightOperand
            );
        }

        $comment = $node->getLeadingCommentAndWhitespaceText();

        if (preg_match('{@var}', $comment)) {
            $docblock = $this->docblockParser->parse($comment);
            $tags = $docblock->tagsNamed('var');

            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    if (null === $tag->target()) {
                        continue;
                    }
                    $frame->setTag($tag);
                }
            }
        }

        foreach ($node->getChildNodes() as $childNode) {
            $this->walk($frame, $childNode);
        }
    }

    private function processMethodDeclaration(Frame $frame, MethodDeclaration $node)
    {
        $namespace = $node->getNamespaceDefinition();
        $class = $node->getFirstAncestor(ClassDeclaration::class);
        $frame->set('$this', $class);

        foreach ($node->parameters->children as $parameter) {
            if (false === $parameter instanceof Parameter) {
                continue;
            }

            $frame->set(
                $parameter->variableName->getText($node->getFileContents()),
                $parameter
            );
        }
    }
}
