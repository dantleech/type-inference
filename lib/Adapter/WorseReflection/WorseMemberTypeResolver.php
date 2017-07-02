<?php

namespace DTL\TypeInference\Adapter\WorseReflection;

use DTL\WorseReflection\ClassName;
use DTL\TypeInference\Domain\MemberTypeResolver;
use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\MethodName;
use DTL\WorseReflection\Reflector;
use DTL\TypeInference\Domain\SourceCodeNotFound;
use DTL\WorseReflection\Exception\ClassNotFound;

final class WorseMemberTypeResolver implements MemberTypeResolver
{
    private $reflector;

    public function __construct(
        Reflector $reflector = null
    )
    {
        $this->reflector = $reflector;
    }

    public function methodType(InferredType $type, MethodName $name): InferredType
    {
        try {
            $class = $this->reflector->reflectClass(ClassName::fromString((string) $type));
        } catch (SourceCodeNotFound $e)
        {
            return InferredType::unknown();
        } catch (ClassNotFound $e) {
            return InferredType::unknown();
        }

        $method = $class->methods()->get((string) $name);

        $type = $method->type()->className() ?: (string) $method->type();

        return InferredType::fromString($type);
    }

    public function propertyType(InferredType $type, MethodName $name): InferredType
    {
        try {
            $class = $this->reflector->reflectClass(ClassName::fromString((string) $type));
        } catch (SourceCodeNotFound $e)
        {
            return InferredType::unknown();
        } catch (ClassNotFound $e) {
            return InferredType::unknown();
        }

        $property = $class->properties()->get((string) $name);

        $type = $property->type()->className() ?: (string) $property->type();

        return InferredType::fromString($type);
    }
}
