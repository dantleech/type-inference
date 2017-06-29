<?php

namespace DTL\TypeInference\Adapter\TolerantParser;

use Microsoft\PhpParser\Parser;
use DTL\TypeInference\Domain\InferredType;

final class TolerantMemberTypeResolver
{
    private $parser;
    private $pathResolver;

    public function __construct(Parser $parser, SourcePathResolver $pathResolver)
    {
        $this->parser = $parser;
        $this->pathResolver = $pathResolver;
    }

    public function methodType(InferredType $type, MethodName $name): InferredType
    {
        $sourceNode = $this->getSourceNode($type);

        if (null === $sourceNode) {
            return InferredType::unknown();
        }

        var_dump($sourceNode);die();;


    }

    public function propertyType(InferredType $type, PropertyName $name): InferredType
    {
    }

    private function getSourceNode(InferredType $type)
    {
        $path = $this->pathResolver->resolvePathFor($type);
        if ($path == SourcePath::unknown()) {
            return null;
        }

        return $this->parser->parseSourceFile(file_get_contents((string) $path));
    }
}
