<?php

namespace Phpactor\TypeInference\Tests\Adapter\TolerantParser;

use PHPUnit\Framework\TestCase;
use Microsoft\PhpParser\Node;
use Phpactor\TypeInference\Adapter\TolerantParser\Frame;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\SourceFileNode;
use Microsoft\PhpParser\Parser;
use Phpactor\TypeInference\Adapter\TolerantParser\FrameBuilder;

class FrameBuilderTest extends TestCase
{
    /**
     * @dataProvider provideFrameBuilder
     */
    public function testFrameBuilder(int $pos, string $source, array $expectedKeys)
    {
        $parser = new Parser();
        $node = $parser->parseSourceFile($source);
        $node = $node->getDescendantNodeAtPosition($pos);
        $frame = (new FrameBuilder())->buildUntil($node);

        $map = $frame->asDebugMap();
        $this->assertEquals($expectedKeys, array_keys($map));
    }

    public function provideFrameBuilder()
    {
        return [
            [
                58,
                '<?php $foobar = new \Foobar(); $barfoo = $foobar; $hello = "string"; $end = true',
                ['$foobar', '$barfoo', '$hello']
            ],
            [
                72,
                <<<'EOT'
<?php
$hello = 'one';
class { public function hello() { $rabbit = 2; $dog = 5; } }
$lemming = 'three';
EOT
                , ['$this', 'self', '$rabbit', '$dog']
            ],
            [
                47,
                <<<'EOT'
<?php
$hello = 'one';
function hello() { $rabbit = 2; $dog = 5; }
$lemming = 'three';
EOT
                , ['$rabbit']
            ],
        ];
    }
}
