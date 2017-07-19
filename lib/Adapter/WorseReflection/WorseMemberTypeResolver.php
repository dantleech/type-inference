<?php

namespace Phpactor\TypeInference\Adapter\WorseReflection;

use Phpactor\WorseReflection\ClassName;
use Phpactor\TypeInference\Domain\MemberTypeResolver;
use Phpactor\TypeInference\Domain\InferredType;
use Phpactor\TypeInference\Domain\MethodName;
use Phpactor\WorseReflection\Reflector;
use Phpactor\TypeInference\Domain\SourceCodeNotFound;
use Phpactor\WorseReflection\Exception\ClassNotFound;
use Phpactor\WorseReflection\Reflection\ReflectionClass;
use Phpactor\TypeInference\Domain\MessageLog;
use Phpactor\WorseReflection\Exception\SourceNotFound;

final class WorseMemberTypeResolver implements MemberTypeResolver
{
    private $reflector;

    public function __construct(
        Reflector $reflector = null
    ) {
        $this->reflector = $reflector;
    }

    public function methodType(MessageLog $log, InferredType $type, MethodName $name): InferredType
    {
        try {
            $class = $this->reflector->reflectClass(ClassName::fromString((string) $type));
        } catch (SourceNotFound $e) {
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

        $type = $method->inferredReturnType()->className() ?: (string) $method->inferredReturnType();

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
