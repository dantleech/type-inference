<?php

namespace DTL\TypeInference\Tests\Adapter\TolerantParser;

use DTL\TypeInference\Domain\TypeInferer;
use DTL\TypeInference\Adapter\TolerantParser\TolerantTypeInferer;
use DTL\TypeInference\Tests\Adapter\TypeInferrerTestCase;
use DTL\TypeInference\Adapter\TolerantParser\TolerantMethodTypeResolver;

class TolerantTypeInfererTest extends TypeInferrerTestCase
{
    protected function inferrer(): TypeInferer
    {
        return new TolerantTypeInferer(null, new TolerantMethodTypeResolver($this->sourceCodeLoader()));
    }
}
