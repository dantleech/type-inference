<?php

namespace Phpactor\TypeInference\Domain;

interface MemberTypeResolver
{
    public function methodType(MessageLog $log, InferredType $class, MethodName $name): InferredType;

    public function propertyType(MessageLog $log, InferredType $class, MethodName $name): InferredType;
}
