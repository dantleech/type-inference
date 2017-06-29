<?php

namespace DTL\TypeInference\Domain;

interface MemberTypeResolver
{
    public function methodType(InferredType $type, MethodName $name): InferredType;

    public function propertyType(InferredType $type, PropertyName $name): InferredType;
}
