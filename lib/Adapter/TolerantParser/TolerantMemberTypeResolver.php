<?php

namespace Phpactor\TypeInference\Adapter\TolerantParser;

use Microsoft\PhpParser\Parser;
use Phpactor\TypeInference\Domain\InferredType;
use Phpactor\TypeInference\Domain\SourceCodeLoader;
use Phpactor\TypeInference\Domain\MethodName;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\MethodDeclaration;
use Phpactor\TypeInference\Domain\SourceCodeNotFound;
use Phpactor\TypeInference\Domain\MemberTypeResolver;
use Microsoft\PhpParser\Node\PropertyDeclaration;
use Phpactor\TypeInference\Domain\Docblock\DocblockParser;
use Microsoft\PhpParser\Node\Statement\InterfaceDeclaration;
use Microsoft\PhpParser\NamespacedNameInterface;
use Phpactor\TypeInference\Domain\MessageLog;

final class TolerantMemberTypeResolver implements MemberTypeResolver
{
    private $parser;
    private $sourceLoader;
    private $docblockParser;
    private $fqnResolver;

    public function __construct(
        SourceCodeLoader $sourceLoader,
        Parser $parser = null,
        DocblockParser $docblockParser = null,
        FullyQualifiedNameResolver $resolver = null
    ) {
        $this->parser = $parser ?: new Parser();
        $this->docblockParser = $docblockParser ?: new DocblockParser();
        $this->fqnResolver = $resolver ?: new FullyQualifiedNameResolver();
        $this->sourceLoader = $sourceLoader;
    }

    public function methodType(MessageLog $log, InferredType $type, MethodName $name): InferredType
    {
        return $this->memberType($log, $type, (string) $name, [
            'node_class' => MethodDeclaration::class,
            'resolver' => function (MethodDeclaration $node) {
                if (null === $node->returnType) {
                    $docblock = $this->docblockParser->parse($node->getLeadingCommentAndWhitespaceText());

                    foreach ($docblock->tagsNamed('return') as $return) {
                        return InferredType::fromString($this->fqnResolver->resolveQualifiedName($node, $return->value()));
                    }

                    $log->log(sprintf(
                        'Could not determine return type for "%s" from method declaration or docblock',
                        $node->getName()
                    ));

                    return InferredType::unknown();
                }

                return InferredType::fromString($node->returnType->getResolvedName());
            },
            'get_name' => function ($node) {
                return $node->getName();
            },
        ]);
    }

    public function propertyType(MessageLog $log, InferredType $type, MethodName $name): InferredType
    {
        return $this->memberType($log, $type, (string) $name, [
            'node_class' => PropertyDeclaration::class,
            'resolver' => function ($node) use ($log) {
                $docblock = $this->docblockParser->parse($node->getLeadingCommentAndWhitespaceText());
                foreach ($docblock->tagsNamed('var') as $var) {
                    return InferredType::fromString($this->fqnResolver->resolveQualifiedName($node, $var->value()));
                }

                $log->log(sprintf(
                    'Could not determine type for "%s" from property declaration or docblock',
                    $node->getName()
                ));

                return InferredType::unknown();
            },
            'get_name' => function ($node) {
                foreach ($node->propertyElements as $propertyElement) {
                    foreach ($propertyElement as $variable) {
                        return $variable->getName();
                    }
                }
            },
        ]);
    }

    private function memberType(MessageLog $log, InferredType $type, string $name, $strategy)
    {
        try {
            $sourceCode = $this->sourceLoader->loadSourceFor($type);
        } catch (SourceCodeNotFound $e) {
            $log->log($e->getMessage());

            return InferredType::unknown();
        }

        $node = $this->parser->parseSourceFile((string) $sourceCode);

        foreach ($node->getDescendantNodes() as $descendant) {
            if (
                $descendant instanceof ClassDeclaration ||
                $descendant instanceof InterfaceDeclaration
            ) {
                if ((string) $descendant->getNamespacedName() == (string) $type) {
                    return $this->memberTypeFromClassDeclaration($name, $descendant, $strategy);
                }
            }
        }

        $log->log(sprintf('Could not find class "%s" in source.', (string) $type));

        return InferredType::unknown();
    }

    private function memberTypeFromClassDeclaration(string $name, NamespacedNameInterface $node, array $strategy)
    {
        foreach ($node->getDescendantNodes() as $descendant) {
            if (get_class($descendant) == $strategy['node_class']) {
                if ($strategy['get_name']($descendant) == (string) $name) {
                    return $strategy['resolver']($descendant);
                }
            }
        }

        return InferredType::unknown();
    }
}
