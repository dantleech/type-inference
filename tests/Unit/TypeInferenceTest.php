<?php

namespace DTL\TypeInference\Tests\Unit;

use PHPUnit\Framework\TestCase;
use DTL\TypeInference\Domain\TypeInferer;
use DTL\TypeInference\TypeInference;
use DTL\TypeInference\Domain\SourceCode;
use DTL\TypeInference\Domain\Offset;
use DTL\TypeInference\Domain\InferredType;

class TypeInferenceTest extends TestCase
{
    private $typeInferer;
    private $typeInference;

    public function setUp()
    {
        $this->typeInferer = $this->prophesize(TypeInferer::class);
        $this->typeInference = new TypeInference($this->typeInferer->reveal());
    }

    /**
     * It delgates to the the type inferer.
     */
    public function testDelegatesToTypeInferer()
    {
        $source = SourceCode::fromString('<?php');
        $expectedType = InferredType::fromString('Foobar');
        $offset = Offset::fromInt(12);
        $this->typeInferer->inferTypeAtOffset($source, $offset)->willReturn($expectedType);

        $type = $this->typeInference->inferTypeAtOffset('<?php', 12);
        $this->assertSame($expectedType, $type);
    }
}
