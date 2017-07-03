<?php

namespace DTL\TypeInference;

use DTL\TypeInference\Domain\TypeInferer;
use DTL\TypeInference\Domain\SourceCode;
use DTL\TypeInference\Domain\Offset;
use DTL\TypeInference\Adapter\TolerantParser\TolerantTypeInferer;
use DTL\TypeInference\Adapter\TolerantParser\TolerantMemberTypeResolver;
use DTL\TypeInference\Domain\SourceCodeLoader;
use DTL\TypeInference\Domain\InferredType;

final class TypeInference
{
    /**
     * @var TypeInferer
     */
    private $inferer;

    public function __construct(TypeInferer $inferer = null)
    {
        $this->inferer = $inferer ?: new TolerantTypeInferer();
    }

    public static function withSourceCodeLoader(SourceCodeLoader $loader)
    {
        return new self(new TolerantTypeInferer(
            null,
            new TolerantMemberTypeResolver($loader)
        ));
    }

    public function inferTypeAtOffset(string $source, int $offset): InferredType
    {
        return $this->inferer->inferTypeAtOffset(
            SourceCode::fromString($source),
            Offset::fromInt($offset)
        );
    }
}
