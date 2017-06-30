<?php

namespace DTL\TypeInference\Tests\Adapter\TolerantParser;

use DTL\TypeInference\Domain\TypeInferer;
use DTL\TypeInference\Adapter\TolerantParser\TolerantTypeInferer;
use DTL\TypeInference\Tests\Adapter\TypeInferrerTestCase;
use PHPUnit\Framework\TestCase;
use DTL\TypeInference\Adapter\TolerantParser\TolerantMemberTypeResolver;
use DTL\TypeInference\Domain\SourceCodeLoader;
use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\SourceCode;
use DTL\TypeInference\Domain\MethodName;

class TolerantMemberTypeInfererTest extends TestCase
{
    private $loader;
    private $resolver;

    public function setUp()
    {
        $this->loader = $this->prophesize(SourceCodeLoader::class);
        $this->resolver = new TolerantMemberTypeResolver($this->loader->reveal());
    }

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

        $this->resolver->methodType($type, MethodName::fromString('type2'));
    }
}
