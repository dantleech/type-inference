<?php

namespace Phpactor\TypeInference\Tests\Unit\Adapter\TolerantParser;

use PHPUnit\Framework\TestCase;
use Microsoft\PhpParser\Node;
use Phpactor\TypeInference\Adapter\TolerantParser\Frame;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\SourceFileNode;
use Microsoft\PhpParser\Parser;
use Phpactor\TypeInference\Adapter\TolerantParser\FrameBuilder;

class FrameTest extends TestCase
{
    /**
     * Test debug map
     */
    public function testDebugMap()
    {
        $parser = new Parser();
        $node = $parser->parseSourceFile('<?php $foobar = new \Foobar(); $barfoo = $foobar; $hello = "string"; $end = true');
        $node = $node->getDescendantNodeAtPosition(58);
        $frame = (new FrameBuilder())->buildUntil($node);

        $map = $frame->asDebugMap();
        $this->assertArrayHasKey('$foobar', $map);
        $this->assertArrayHasKey('$barfoo', $map);
        $this->assertArrayNotHasKey('$end', $map);
    }
}
