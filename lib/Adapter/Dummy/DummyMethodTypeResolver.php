<?php

namespace DTL\TypeInference\Adapter\Dummy;

use DTL\TypeInference\Domain\MethodTypeResolver;
use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\SourceCode;
use DTL\TypeInference\Domain\SourceCodeNotFound;
use DTL\TypeInference\Domain\MethodName;

class DummyMethodTypeResolver implements MethodTypeResolver
{
    public function methodType(InferredType $type, MethodName $name): InferredType
    {
        return InferredType::unknown();
    }
}
