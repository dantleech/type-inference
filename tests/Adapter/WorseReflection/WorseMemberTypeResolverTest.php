<?php

namespace DTL\TypeInference\Tests\Adapter\TolerantParser;

use DTL\TypeInference\Adapter\TolerantParser\TolerantMemberTypeResolver;
use DTL\TypeInference\Domain\MemberTypeResolver;
use DTL\TypeInference\Tests\Adapter\MemberTypeResolverTestCase;
use DTL\TypeInference\Adapter\WorseReflection\WorseMemberTypeResolver;
use DTL\TypeInference\Adapter\WorseReflection\WorseSourceCodeLocator;
use DTL\WorseReflection\Reflector;

class TolerantMemberTypeResolverTest extends MemberTypeResolverTestCase
{
    protected function resolver(): MemberTypeResolver
    {
        return new WorseMemberTypeResolver(
            new Reflector(
                new WorseSourceCodeLocator(
                    $this->loader->reveal()
                )
            )
        );
    }
}
