<?php

namespace DTL\TypeInference\Tests\Adapter;

use DTL\TypeInference\Domain\TypeInferer;
use DTL\TypeInference\Tests\Adapter\TypeInferrerTestCase;
use PHPUnit\Framework\TestCase;
use DTL\TypeInference\Domain\SourceCodeLoader;
use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\SourceCode;
use DTL\TypeInference\Domain\MethodName;
use DTL\TypeInference\Domain\SourceCodeNotFound;
use DTL\TypeInference\Domain\MemberTypeResolver;

abstract class MemberTypeResolverTestCase extends TestCase
{
    protected $loader;
    private $resolver;

    public function setUp()
    {
        $this->loader = $this->prophesize(SourceCodeLoader::class);
    }

    abstract protected function resolver(): MemberTypeResolver;

    /**
     * It resolves the type of a method
     */
    public function testResolveMethodType()
    {
        $source = SourceCode::fromString(<<<'EOT'
<?php

class Type1
{
    public function type2(): Type2
    {
    }
}
EOT
        );
        $type = InferredType::fromString('Type1');
        $this->loader->loadSourceFor($type)->willReturn($source);

        $resolvedType = $this->resolver()->methodType($type, MethodName::fromString('type2'));

        $this->assertEquals('Type2', (string) $resolvedType);
    }

    /**
     * It resolves the type of a method with a documented return type
     */
    public function testResolveMethodTypeDocumented()
    {
        $source = SourceCode::fromString(<<<'EOT'
<?php

class Type1
{
    /**
     * @return Type2
     */
    public function type2()
    {
    }
}
EOT
        );
        $type = InferredType::fromString('Type1');
        $this->loader->loadSourceFor($type)->willReturn($source);

        $resolvedType = $this->resolver()->methodType($type, MethodName::fromString('type2'));

        $this->assertEquals('Type2', (string) $resolvedType);
    }

    /**
     * It returns unknown if the source is not found.
     */
    public function testSourceNotFound()
    {
        $type = InferredType::fromString('Type1');
        $this->loader->loadSourceFor($type)->willThrow(new SourceCodeNotFound($type));

        $type = $this->resolver()->methodType($type, MethodName::fromString('type2'));
        $this->assertEquals(InferredType::unknown(), $type);
    }

    /**
     * It returns unknown if the class was not found in the source code.
     */
    public function testClassNotFoundInSource()
    {
        $source = SourceCode::fromString(<<<'EOT'
<?php

class Type1
{
    public function type2(): Type2
    {
    }
}
EOT
        );
        $type = InferredType::fromString('TypeZ');
        $this->loader->loadSourceFor($type)->willReturn($source);

        $type = $this->resolver()->methodType($type, MethodName::fromString('type2'));
        $this->assertEquals(InferredType::unknown(), $type);
    }

    /**
     * It returns unknown if the class method was not found in the source code.
     */
    public function testClassMethodNotFoundInSource()
    {
        $source = SourceCode::fromString(<<<'EOT'
<?php

class Type1
{
    public function type2(): Type2
    {
    }
}
EOT
        );
        $type = InferredType::fromString('TypeZ');
        $this->loader->loadSourceFor($type)->willReturn($source);

        $type = $this->resolver()->methodType($type, MethodName::fromString('typeZ'));
        $this->assertEquals(InferredType::unknown(), $type);
    }

    /**
     * It resolves the type of a property
     */
    public function testResolvePropertyType()
    {
        $source = SourceCode::fromString(<<<'EOT'
<?php

class Type1
{
    /**
     * @var PropertyType
     */
    private $foobar;
}
EOT
        );
        $type = InferredType::fromString('Type1');
        $this->loader->loadSourceFor($type)->willReturn($source);

        $resolvedType = $this->resolver()->propertyType($type, MethodName::fromString('foobar'));

        $this->assertEquals('PropertyType', (string) $resolvedType);
    }

    /**
     * It resolves the type of a property with a use statement
     */
    public function testResolvePropertyTypeUsed()
    {
        $source = SourceCode::fromString(<<<'EOT'
<?php

use Acme\Bumble\PropertyType;

class Type1
{
    /**
     * @var PropertyType
     */
    private $foobar;
}
EOT
        );
        $type = InferredType::fromString('Type1');
        $this->loader->loadSourceFor($type)->willReturn($source);

        $resolvedType = $this->resolver()->propertyType($type, MethodName::fromString('foobar'));

        $this->assertEquals('Acme\Bumble\PropertyType', (string) $resolvedType);
    }
}
