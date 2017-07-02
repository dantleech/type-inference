<?php

namespace DTL\TypeInference\Tests\Adapter\TolerantParser;

use DTL\TypeInference\Adapter\TolerantParser\TolerantMemberTypeResolver;
use DTL\TypeInference\Domain\MemberTypeResolver;
use DTL\TypeInference\Tests\Adapter\MemberTypeResolverTestCase;

class TolerantMemberTypeResolverTest extends MemberTypeResolverTestCase
{
    protected function resolver(): MemberTypeResolver
    {
        return new TolerantMemberTypeResolver($this->loader->reveal());
    }
}
