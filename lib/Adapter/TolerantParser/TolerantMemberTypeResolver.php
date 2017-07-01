<?php

namespace DTL\TypeInference\Adapter\TolerantParser;

use Microsoft\PhpParser\Parser;
use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\SourceCodeLoader;
use DTL\TypeInference\Domain\MethodName;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\MethodDeclaration;
use DTL\TypeInference\Domain\PropertyName;
use DTL\TypeInference\Domain\SourceCodeNotFound;
use DTL\TypeInference\Domain\MemberTypeResolver;
use Microsoft\PhpParser\Node\PropertyDeclaration;
use DTL\TypeInference\Domain\DocblockParser;
use Microsoft\PhpParser\Node\Statement\InterfaceDeclaration;
use Microsoft\PhpParser\NamespacedNameInterface;

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
    )
    {
        $this->parser = $parser ?: new Parser();
        $this->docblockParser = $docblockParser ?: new DocblockParser();
        $this->fqnResolver = $resolver ?: new FullyQualifiedNameResolver();
        $this->sourceLoader = $sourceLoader;
    }

    public function methodType(InferredType $type, MethodName $name): InferredType
    {
        return $this->memberType($type, (string) $name, [
            'node_class' => MethodDeclaration::class,
            'resolver' => function (MethodDeclaration $node) {
                if (null === $node->returnType) {
                    $docblock = $this->docblockParser->parse($node->getLeadingCommentAndWhitespaceText());

                    foreach ($docblock->tagsNamed('return') as $return) {
                        return InferredType::fromString($this->fqnResolver->resolveQualifiedName($node, $return->value()));
                    }

                    return InferredType::unknown();
                }
                return InferredType::fromString($node->returnType->getResolvedName());
            },
            'get_name' => function ($node) {
                return $node->getName();
            },
        ]);
    }

    public function propertyType(InferredType $type, MethodName $name): InferredType
    {
        return $this->memberType($type, (string) $name, [
            'node_class' => PropertyDeclaration::class,
            'resolver' => function ($node) {
                $docblock = $this->docblockParser->parse($node->getLeadingCommentAndWhitespaceText());
                foreach ($docblock->tagsNamed('var') as $var) {
                    return InferredType::fromString($this->fqnResolver->resolveQualifiedName($node, $var->value()));
                }

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

    private function memberType(InferredType $type, string $name, $strategy)
    {
        try {
            $sourceCode = $this->sourceLoader->loadSourceFor($type);
        } catch (SourceCodeNotFound $e) {
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
