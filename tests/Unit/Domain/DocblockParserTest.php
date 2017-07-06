<?php

namespace Phpactor\TypeInference\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use Phpactor\TypeInference\Domain\Docblock\DocblockParser;
use Phpactor\TypeInference\Domain\Docblock\Docblock;
use Phpactor\TypeInference\Domain\Docblock\DocblockTag;

class DocblockParserTest extends TestCase
{
    /**
     * @dataProvider provideParser
     */
    public function testParser($docblock, $expected)
    {
        $parser = new DocblockParser();
        $result = $parser->parse($docblock);
        $this->assertInstanceOf(Docblock::class, $result);
        $this->assertEquals($expected, $result->tagsNamed('var'));
    }

    public function provideParser()
    {
        return [
            [
                '/** @var $foobar Foobar */',
                [
                    DocblockTag::fromNameTargetAndValue('var', '$foobar', 'Foobar')
                ]
            ],
            [
                '/** @var Foobar */',
                [
                    DocblockTag::fromNameAndValue('var', 'Foobar')
                ]
            ],
            [
                '/** @var Barfoo\Foobar */',
                [ DocblockTag::fromNameAndValue('var', 'Barfoo\Foobar') ]
            ],
            [
                <<<'EOT'
/**
 * This is something.
 *
 * @var Barfoo
 */
EOT
                ,
                [ DocblockTag::fromNameAndValue('var', 'Barfoo') ]
            ],
            [
                <<<'EOT'
/**
 * This is something.
 *
 * @var Barfoo
 */
EOT
                ,
                [ DocblockTag::fromNameAndValue('var', 'Barfoo') ]
            ],
        ];
    }
}
