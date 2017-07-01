<?php

namespace DTL\TypeInference\Adapter\TolerantParser;

use Microsoft\PhpParser\Parser;
use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\SourceCodeLoader;
use DTL\TypeInference\Domain\MethodName;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\MethodDeclaration;
use DTL\TypeInference\Domain\MemberTypeResolver;
use DTL\TypeInference\Domain\PropertyName;
use DTL\TypeInference\Domain\SourceCodeNotFound;
use DTL\TypeInference\Domain\MethodTypeResolver;

final class TolerantMethodTypeResolver implements MethodTypeResolver
{
    private $parser;
    private $sourceLoader;

    public function __construct(SourceCodeLoader $sourceLoader, Parser $parser = null)
    {
        $this->parser = $parser ?: new Parser();
        $this->sourceLoader = $sourceLoader;
    }

    public function methodType(InferredType $type, MethodName $name): InferredType
    {
        try {
            $sourceCode = $this->sourceLoader->loadSourceFor($type);
        } catch (SourceCodeNotFound $e) {
            return InferredType::unknown();
        }

        $node = $this->parser->parseSourceFile((string) $sourceCode);

        foreach ($node->getDescendantNodes() as $descendant) {
            if ($descendant instanceof ClassDeclaration) {
                if ((string) $descendant->getNamespacedName() == (string) $type) {
                    return $this->methodFromClassDeclaration($name, $descendant);
                }
            }
        }

        return InferredType::unknown();
    }

    private function methodFromClassDeclaration(MethodName $name, ClassDeclaration $node)
    {
        foreach ($node->getDescendantNodes() as $descendant) {
            if ($descendant instanceof MethodDeclaration) {
                if ($descendant->getName() == (string) $name) {
                    return InferredType::fromString($descendant->returnType->getResolvedName());
                }
            }

        }

        return InferredType::unknown();
    }

    public function propertyType(InferredType $type, PropertyName $name): InferredType
    {
    }
}
