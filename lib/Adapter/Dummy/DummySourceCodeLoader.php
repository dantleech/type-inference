<?php

namespace Phpactor\TypeInference\Adapter\Dummy;

use Phpactor\TypeInference\Domain\MemberTypeResolver;
use Phpactor\TypeInference\Domain\InferredType;
use Phpactor\TypeInference\Domain\SourceCode;
use Phpactor\TypeInference\Domain\SourceCodeNotFound;
use Phpactor\TypeInference\Domain\MethodName;
use Phpactor\TypeInference\Domain\SourceCodeLoader;

class DummySourceCodeLoader implements SourceCodeLoader
{
    public function loadSourceFor(InferredType $type): SourceCode
    {
        throw new SourceNotFound($type);
    }
}
