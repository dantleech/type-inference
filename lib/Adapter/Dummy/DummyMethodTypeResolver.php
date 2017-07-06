<?php

namespace Phpactor\TypeInference\Adapter\Dummy;

use Phpactor\TypeInference\Domain\MemberTypeResolver;
use Phpactor\TypeInference\Domain\InferredType;
use Phpactor\TypeInference\Domain\SourceCode;
use Phpactor\TypeInference\Domain\SourceCodeNotFound;
use Phpactor\TypeInference\Domain\MethodName;
use Phpactor\TypeInference\Domain\MessageLog;

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
