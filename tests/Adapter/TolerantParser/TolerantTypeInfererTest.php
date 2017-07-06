<?php

namespace Phpactor\TypeInference\Tests\Adapter\TolerantParser;

use Phpactor\TypeInference\Domain\TypeInferer;
use Phpactor\TypeInference\Adapter\TolerantParser\TolerantTypeInferer;
use Phpactor\TypeInference\Tests\Adapter\TypeInferrerTestCase;
use Phpactor\TypeInference\Adapter\TolerantParser\TolerantMemberTypeResolver;
use Phpactor\TypeInference\Domain\MessageLog;
use Phpactor\TypeInference\Domain\SourceCode;
use Phpactor\TypeInference\Domain\Offset;

class TolerantTypeInfererTest extends TypeInferrerTestCase
{
    protected function inferrer(): TypeInferer
    {
        return new TolerantTypeInferer(null, new TolerantMemberTypeResolver($this->sourceCodeLoader()));
    }

    public function testAsDebugMap()
    {
        $source = <<<'EOT'
<?php

namespace Acme;

use Far\Bar\Hello;

class Foobar
{
    public function foobar(Barfoo $barfoo, Hello $hello)
    {
    }
}
EOT
        ;


        $messageLog = new MessageLog();
        $result = $this->inferrer()->inferTypeAtOffset(
            SourceCode::fromString($source),
            Offset::fromInt(30)
        );
        $map = $result->frame()->asDebugMap();
        $this->assertCount(3, $map);
    }
}
