<?php

namespace DTL\TypeInference\Adapter\WorseReflection;

use DTL\WorseReflection\ClassName;
use DTL\TypeInference\Domain\MemberTypeResolver;
use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\MethodName;
use DTL\WorseReflection\Reflector;
use DTL\TypeInference\Domain\SourceCodeNotFound;
use DTL\WorseReflection\Exception\ClassNotFound;
use DTL\WorseReflection\Reflection\ReflectionClass;
use DTL\TypeInference\Domain\MessageLog;

final class WorseMemberTypeResolver implements MemberTypeResolver
{
    private $reflector;

    public function __construct(
        Reflector $reflector = null
    )
    {
        $this->reflector = $reflector;
    }

    public function methodType(MessageLog $log, InferredType $type, MethodName $name): InferredType
    {
        try {
            $class = $this->reflector->reflectClass(ClassName::fromString((string) $type));
        } catch (SourceCodeNotFound $e) {
            $log->log($e->getMessage());
            return InferredType::unknown();
        } catch (ClassNotFound $e) {
            $log->log($e->getMessage());
            return InferredType::unknown();
        }

        try {
            $method = $class->methods()->get((string) $name);
        } catch (\InvalidArgumentException $e) {
            $log->log($e->getMessage());
            return InferredType::unknown();
        }

        $type = $method->type()->className() ?: (string) $method->type();

        return InferredType::fromString($type);
    }

    public function propertyType(MessageLog $log, InferredType $type, MethodName $name): InferredType
    {
        try {
            $class = $this->reflector->reflectClass(ClassName::fromString((string) $type));
        } catch (SourceCodeNotFound $e) {
            $log->log($e->getMessage());
            return InferredType::unknown();
        } catch (ClassNotFound $e) {
            $log->log($e->getMessage());
            return InferredType::unknown();
        }

        if (!$class instanceof ReflectionClass) {
            return InferredType::unknown();
        }

        try {
            $property = $class->properties()->get((string) $name);
        } catch (\InvalidArgumentException $e) {
            $log->log($e->getMessage());
            return InferredType::unknown();
        }

        $type = $property->type()->className() ?: (string) $property->type();

        return InferredType::fromString($type);
    }
}
