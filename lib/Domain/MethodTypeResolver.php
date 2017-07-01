<?php

namespace DTL\TypeInference\Domain;

interface MethodTypeResolver
{
    public function methodType(InferredType $type, MethodName $name): InferredType;
}
