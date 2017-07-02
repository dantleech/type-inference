<?php

namespace DTL\TypeInference\Adapter\WorseReflection;

use DTL\WorseReflection\SourceCodeLocator;
use DTL\WorseReflection\SourceCode;
use DTL\TypeInference\Domain\InferredType;
use DTL\WorseReflection\ClassName;
use DTL\TypeInference\Domain\SourceCodeLoader;

class WorseSourceCodeLocator implements SourceCodeLocator
{
    private $loader;

    public function __construct(SourceCodeLoader $loader)
    {
        $this->loader = $loader;
    }


    public function locate(ClassName $className): SourceCode
    {
        return SourceCode::fromString($this->loader->loadSourceFor(InferredType::fromString((string) $className)));
    }
}
