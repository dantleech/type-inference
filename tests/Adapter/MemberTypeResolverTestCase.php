<?php

namespace Phpactor\TypeInference\Tests\Adapter;

use Phpactor\TypeInference\Domain\TypeInferer;
use Phpactor\TypeInference\Tests\Adapter\TypeInferrerTestCase;
use PHPUnit\Framework\TestCase;
use Phpactor\TypeInference\Domain\SourceCodeLoader;
use Phpactor\TypeInference\Domain\InferredType;
use Phpactor\TypeInference\Domain\SourceCode;
use Phpactor\TypeInference\Domain\MethodName;
use Phpactor\TypeInference\Domain\SourceCodeNotFound;
use Phpactor\TypeInference\Domain\MemberTypeResolver;
use Phpactor\TypeInference\Domain\MessageLog;

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
        $log = new MessageLog();

        $resolvedType = $this->resolver()->methodType($log, $type, MethodName::fromString('type2'));

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
        $log = new MessageLog();

        $resolvedType = $this->resolver()->methodType($log, $type, MethodName::fromString('type2'));

        $this->assertEquals('Type2', (string) $resolvedType);
    }

    /**
     * It returns unknown if the source is not found.
     */
    public function testSourceNotFound()
    {
        $type = InferredType::fromString('Type1');
        $this->loader->loadSourceFor($type)->willThrow(new SourceCodeNotFound($type));
        $log = new MessageLog();

        $type = $this->resolver()->methodType($log, $type, MethodName::fromString('type2'));
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
        $log = new MessageLog();

        $type = $this->resolver()->methodType($log, $type, MethodName::fromString('type2'));
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
        $log = new MessageLog();

        $type = $this->resolver()->methodType($log, $type, MethodName::fromString('typeZ'));
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
        $log = new MessageLog();

        $resolvedType = $this->resolver()->propertyType($log, $type, MethodName::fromString('foobar'));

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
        $log = new MessageLog();

        $resolvedType = $this->resolver()->propertyType($log, $type, MethodName::fromString('foobar'));


        $this->assertEquals('Acme\Bumble\PropertyType', (string) $resolvedType);
    }
}
