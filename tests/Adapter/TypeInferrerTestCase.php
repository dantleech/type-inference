<?php

namespace DTL\TypeInference\Tests\Adapter;

use DTL\TypeInference\Adapter\TolerantParser\TolerantTypeInferer;
use DTL\TypeInference\Domain\Offset;
use DTL\TypeInference\Domain\InferredType;
use PHPUnit\Framework\TestCase;
use DTL\TypeInference\Domain\SourceCode;
use DTL\TypeInference\Domain\TypeInferer;

abstract class TypeInferrerTestCase extends TestCase
{
    abstract protected function inferrer(): TypeInferer;

    /**
     * @dataProvider provideTests
     */
    public function testAdapter(string $source, int $offset, InferredType $expectedType)
    {
        $type = $this->inferrer()->inferTypeAtOffset(SourceCode::fromString($source), Offset::fromInt($offset));
        $this->assertEquals($expectedType, $type);
    }

    public function provideTests()
    {
        return [
            'It should return unknown type for whitespace' => [
                '    ',
                1,
                InferredType::unknown()
            ],
            'It should return the name of a class' => [
                <<<'EOT'
<?php

$foo = new ClassName();

EOT
                , 23, InferredType::fromString('ClassName')
            ],
            'It should return the fully qualified name of a class' => [
                <<<'EOT'
<?php

namespace Foobar\Barfoo;

$foo = new ClassName();

EOT
                , 47, InferredType::fromString('Foobar\Barfoo\ClassName')
            ],
            'It should return the fully qualified name of a with an imported name.' => [
                <<<'EOT'
<?php

namespace Foobar\Barfoo;

use BarBar\ClassName();

$foo = new ClassName();

EOT
                , 70, InferredType::fromString('BarBar\ClassName')
            ],
            'It should return the fully qualified name of a use definition' => [
                <<<'EOT'
<?php

namespace Foobar\Barfoo;

use BarBar\ClassName();

$foo = new ClassName();

EOT
                , 46, InferredType::fromString('BarBar\ClassName')
            ],
            'It returns the FQN of a method parameter' => [
                <<<'EOT'
<?php

namespace Foobar\Barfoo;

class Foobar
{
    public function foobar(Barfoo $barfoo)
    {
    }
}

EOT
                , 77, InferredType::fromString('Foobar\Barfoo\Barfoo')
            ],
            'It returns the FQN of a static call' => [
                <<<'EOT'
<?php

namespace Foobar\Barfoo;

use Acme\Factory;

$foo = Factory::create();

EOT
                , 63, InferredType::fromString('Acme\Factory')
            ],
            'It returns the FQN of a method parameter' => [
                <<<'EOT'
<?php

namespace Foobar\Barfoo;

use Acme\Factory;

class Foobar
{
    public function hello(World $world)
    {
    }
}

EOT
                , 102, InferredType::fromString('Foobar\Barfoo\World')
            ],
            'It returns the FQN of variable assigned in the method declaration' => [
                <<<'EOT'
<?php

namespace Foobar\Barfoo;

use Acme\Factory;

class Foobar
{
    public function hello(World $world)
    {
        echo $world;
    }
}

EOT
                , 127, InferredType::fromString('Foobar\Barfoo\World')
            ],
        ];

    }
}
