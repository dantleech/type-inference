<?php

namespace Phpactor\TypeInference\Tests\Adapter\TolerantParser;

use Phpactor\TypeInference\Adapter\TolerantParser\TolerantMemberTypeResolver;
use Phpactor\TypeInference\Domain\MemberTypeResolver;
use Phpactor\TypeInference\Tests\Adapter\MemberTypeResolverTestCase;
use Phpactor\TypeInference\Adapter\WorseReflection\WorseMemberTypeResolver;
use Phpactor\TypeInference\Adapter\WorseReflection\WorseSourceCodeLocator;
use Phpactor\WorseReflection\Reflector;

class WorseMemberTypeResolverTest extends MemberTypeResolverTestCase
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
