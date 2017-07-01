<?php

namespace DTL\TypeInference\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use DTL\TypeInference\Domain\DocblockParser;
use DTL\TypeInference\Domain\Docblock;
use DTL\TypeInference\Domain\DocblockTag;

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
