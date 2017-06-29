<?php

namespace DTL\TypeInference\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use DTL\TypeInference\Domain\Frame;
use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\Variable;

class TypeFrameTest extends TestCase
{
    /**
     * @testdox It gets and sets variables.
     */
    public function testGetAndSet()
    {
        $variable = Variable::fromNameAndType('foobar', InferredType::fromString('string'));
        $frame = new Frame();
        $frame->set($variable);
        $this->assertSame($variable, $frame->get('foobar'));
    }

    /**
     * @testdox It returns null when no variable found
     */
    public function testVariableNotFound()
    {
        $frame = new Frame();
        $this->assertNull($frame->get('foobar'));
    }
}
