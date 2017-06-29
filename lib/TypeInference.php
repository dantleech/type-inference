<?php

namespace DTL\TypeInference;

use DTL\TypeInference\Domain\TypeInferer;
use DTL\TypeInference\Domain\SourceCode;
use DTL\TypeInference\Domain\Offset;
use DTL\TypeInference\Adapter\TolerantParser\TolerantTypeInferer;

final class TypeInference
{
    private $inferer;

    public function __construct(TypeInferer $inferer = null)
    {
        $this->inferer = $inferer ?: new TolerantTypeInferer();
    }

    public function inferTypeAtOffset(string $source, int $offset)
    {
        return $this->inferer->inferTypeAtOffset(
            SourceCode::fromString($source),
            Offset::fromInt($offset)
        );
    }
}
