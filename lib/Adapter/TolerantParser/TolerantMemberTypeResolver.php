<?php

namespace DTL\TypeInference\Adapter\TolerantParser;

use Microsoft\PhpParser\Parser;
use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\SourceCodeLoader;

final class TolerantMemberTypeResolver
{
    private $parser;
    private $sourceLoader;

    public function __construct(Parser $parser, SourceCodeLoader $sourceLoader)
    {
        $this->parser = $parser;
        $this->sourceLoader = $sourceLoader;
    }

    public function methodType(InferredType $type, MethodName $name): InferredType
    {
        $sourceCode = $this->sourceLoader->loadSourceFor($type);

        try {
            $node = $this->parser->parseSourceFile((string) $sourceCode);
            var_dump($node);die();;
        } catch (SourceNotFound $e) {
            return InferredType::unknown();
        }
    }

    public function propertyType(InferredType $type, PropertyName $name): InferredType
    {
    }

    private function getSourceNode(InferredType $type)
    {
    }
}
