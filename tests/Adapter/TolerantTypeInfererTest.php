<?php

namespace DTL\TypeInference\Tests\Adapter;

use DTL\TypeInference\Domain\TypeInferer;
use DTL\TypeInference\Adapter\TolerantTypeInferer;

class TolerantTypeInfererTest extends AdapterTestCase
{
    protected function inferrer(): TypeInferer
    {
        return new TolerantTypeInferer();
    }
}
