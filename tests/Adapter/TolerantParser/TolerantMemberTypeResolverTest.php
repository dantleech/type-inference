<?php

namespace Phpactor\TypeInference\Tests\Adapter\TolerantParser;

use Phpactor\TypeInference\Adapter\TolerantParser\TolerantMemberTypeResolver;
use Phpactor\TypeInference\Domain\MemberTypeResolver;
use Phpactor\TypeInference\Tests\Adapter\MemberTypeResolverTestCase;

class TolerantMemberTypeResolverTest extends MemberTypeResolverTestCase
{
    protected function resolver(): MemberTypeResolver
    {
        return new TolerantMemberTypeResolver($this->loader->reveal());
    }
}
