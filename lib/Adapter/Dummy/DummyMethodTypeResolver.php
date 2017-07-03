<?php

namespace DTL\TypeInference\Adapter\Dummy;

use DTL\TypeInference\Domain\MemberTypeResolver;
use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\SourceCode;
use DTL\TypeInference\Domain\SourceCodeNotFound;
use DTL\TypeInference\Domain\MethodName;
use DTL\TypeInference\Domain\MessageLog;

class DummyMethodTypeResolver implements MemberTypeResolver
{
    public function methodType(MessageLog $log, InferredType $type, MethodName $name): InferredType
    {
        return InferredType::unknown();
    }

    public function propertyType(MessageLog $log, InferredType $type, MethodName $name): InferredType
    {
        return InferredType::unknown();
    }
}
