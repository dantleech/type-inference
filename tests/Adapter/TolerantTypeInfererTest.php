<?php

namespace DTL\TypeInference\Tests\Adapter;

use DTL\TypeInference\Adapter\TolerantTypeInferer;
use DTL\TypeInference\Domain\Offset;
use DTL\TypeInference\Domain\InferredType;
use PHPUnit\Framework\TestCase;
use DTL\TypeInference\Domain\SourceCode;

class TolerantTypeInfererTest extends TestCase
{
    private $inferrer;

    public function setUp()
    {
        $this->inferrer = new TolerantTypeInferer();
    }

    /**
     * It should return unknown type for whitespace.
     */
    public function testUnknownTypeForWhitespace()
    {
        $type = $this->inferrer->inferTypeAtOffset(SourceCode::fromString(<<<'EOT'

EOT
        ), Offset::fromInt(1));

        $this->assertEquals(InferredType::unknown(), $type);
    }

    /**
     * It should return the name of a class.
     */
    public function testClassNameForClass()
    {
        $type = $this->inferrer->inferTypeAtOffset(SourceCode::fromString(<<<'EOT'
<?php

$foo = new ClassName();

EOT
        ), Offset::fromInt(23));

        $this->assertEquals(InferredType::fromString('ClassName'), $type);
    }

    /**
     * It should return the fully qualified name of a class.
     */
    public function testClassNameForNamespacedClass()
    {
        $type = $this->inferrer->inferTypeAtOffset(SourceCode::fromString(<<<'EOT'
<?php

namespace Foobar\Barfoo;

$foo = new ClassName();

EOT
        ), Offset::fromInt(47));

        $this->assertEquals(InferredType::fromString('Foobar\Barfoo\ClassName'), $type);
    }

    /**
     * It should return the fully qualified name of a with an imported name.
     */
    public function testClassNameForImportedClass()
    {
        $type = $this->inferrer->inferTypeAtOffset(SourceCode::fromString(<<<'EOT'
<?php

namespace Foobar\Barfoo;

use BarBar\ClassName();

$foo = new ClassName();

EOT
        ), Offset::fromInt(70));

        $this->assertEquals(InferredType::fromString('BarBar\ClassName'), $type);
    }

    /**
     * It should return the fully qualified name of a use definition
     *
     * NOTE: Not sure if we should really do this as the use statement is just an imported namespace and
     *       does not mean that the class exists.
     */
    public function testClassNameForUsed()
    {
        $type = $this->inferrer->inferTypeAtOffset(SourceCode::fromString(<<<'EOT'
<?php

namespace Foobar\Barfoo;

use BarBar\ClassName();

$foo = new ClassName();

EOT
        ), Offset::fromInt(46));

        $this->assertEquals(InferredType::fromString('BarBar\ClassName'), $type);
    }
}
