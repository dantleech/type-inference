<?php

namespace DTL\TypeInference\Tests\Unit\Adapter\TolerantParser;

use PHPUnit\Framework\TestCase;
use Microsoft\PhpParser\Node;
use DTL\TypeInference\Adapter\TolerantParser\Frame;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\SourceFileNode;
use Microsoft\PhpParser\Parser;
use DTL\TypeInference\Adapter\TolerantParser\FrameBuilder;

class FrameTest extends TestCase
{
    /**
     * Test debug map
     */
    public function testDebugMap()
    {
        $parser = new Parser();
        $node = $parser->parseSourceFile('<?php $foobar = new \Foobar(); $barfoo = $foobarl $hello = "string";');
        $node = $node->getDescendantNodeAtPosition(58);
        $frame = (new FrameBuilder())->buildUntil($node);

        $map = $frame->asDebugMap();
        $this->assertCount(3, $map);
    }
}
