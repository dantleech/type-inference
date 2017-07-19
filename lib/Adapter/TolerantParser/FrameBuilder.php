<?php

namespace Phpactor\TypeInference\Adapter\TolerantParser;

use Microsoft\PhpParser\Node;
use Microsoft\PhpParser\Node\MethodDeclaration;
use Microsoft\PhpParser\Node\Statement\FunctionDeclaration;
use Microsoft\PhpParser\Node\Parameter;
use Microsoft\PhpParser\Node\Expression\AssignmentExpression;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\SourceFileNode;
use Phpactor\TypeInference\Domain\Docblock\DocblockParser;
use Microsoft\PhpParser\Node\Statement\InterfaceDeclaration;

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

        $this->walk($frame, $start, $node);

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

    private function walk(Frame $frame, Node $node, Node $until)
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

        // TODO: Remove this duplication
        if (preg_match('{@var}', $comment)) {
            $docblock = $this->docblockParser->parse($comment);
            $tags = $docblock->tagsNamed('var');

            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    if (null === $tag->target()) {
                        continue;
                    }

                    $frame->set($tag->target(), $node);
                }
            }
        }

        if ($node === $until) {
            return false;
        }

        foreach ($node->getChildNodes() as $childNode) {
            if (false === $this->walk($frame, $childNode, $until)) {
                return false;
            }
        }
    }

    private function processMethodDeclaration(Frame $frame, MethodDeclaration $node)
    {
        $namespace = $node->getNamespaceDefinition();
        $class = $node->getFirstAncestor(ClassDeclaration::class, InterfaceDeclaration::class);
        $frame->set('$this', $class);
        $frame->set('self', $class);

        if (null === $node->parameters) {
            return;
        }

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
