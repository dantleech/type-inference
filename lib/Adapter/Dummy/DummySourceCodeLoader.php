<?php

namespace DTL\TypeInference\Adapter\Dummy;

use DTL\TypeInference\Domain\MethodTypeResolver;
use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\SourceCode;
use DTL\TypeInference\Domain\SourceCodeNotFound;
use DTL\TypeInference\Domain\MethodName;
use DTL\TypeInference\Domain\SourceCodeLoader;

class DummySourceCodeLoader implements SourceCodeLoader
{
    public function loadSourceFor(InferredType $type): SourceCode
    {
        throw new SourceNotFound($type);
    }
}
