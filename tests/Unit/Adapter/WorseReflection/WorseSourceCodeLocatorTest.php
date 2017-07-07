<?php

namespace Phpactor\TypeInference\Tests\Unit\Adapter\WorseReflection;

use PHPUnit\Framework\TestCase;
use Phpactor\TypeInference\Domain\SourceCodeLoader;
use Phpactor\TypeInference\Domain\SourceCodeNotFound;
use Phpactor\TypeInference\Domain\InferredType;
use Phpactor\TypeInference\Adapter\WorseReflection\WorseSourceCodeLocator;
use Phpactor\WorseReflection\ClassName;

class WorseSourceCodeLocatorTest extends TestCase
{
    /**
     * @testdox It throws a (WorseReflection) SourceNotFound exception if the loader did not find source.
     * @expectedException Phpactor\WorseReflection\Exception\SourceNotFound
     */
    public function testThrowsWorseReflection()
    {
        $type = InferredType::fromString('Foo');
        $typeLoader = $this->prophesize(SourceCodeLoader::class);
        $typeLoader->loadSourceFor($type)->willThrow(new SourceCodeNotFound($type));

        $loader = new WorseSourceCodeLocator($typeLoader->reveal());
        $loader->locate(ClassName::fromString('Foo'));
    }
}
