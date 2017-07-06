<?php

namespace Phpactor\TypeInference\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use Phpactor\TypeInference\Domain\InferredType;

class InferredTypeTest extends TestCase
{
    /**
     * @testdox It strips leading namepspace delimiters when creating from string.
     */
    public function testClean()
    {
        $type = InferredType::fromString('\Foobar');
        $this->assertEquals('Foobar', (string) $type);
    }
}
