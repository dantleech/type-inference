<?php

namespace DTL\TypeInference\Tests\Adapter\TolerantParser;

use DTL\TypeInference\Domain\TypeInferer;
use DTL\TypeInference\Adapter\TolerantParser\TolerantTypeInferer;
use DTL\TypeInference\Tests\Adapter\TypeInferrerTestCase;
use DTL\TypeInference\Adapter\TolerantParser\TolerantMemberTypeResolver;
use DTL\TypeInference\Domain\MessageLog;
use DTL\TypeInference\Domain\SourceCode;
use DTL\TypeInference\Domain\Offset;

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
