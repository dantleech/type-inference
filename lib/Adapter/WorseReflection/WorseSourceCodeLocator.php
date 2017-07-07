<?php

namespace Phpactor\TypeInference\Adapter\WorseReflection;

use Phpactor\WorseReflection\SourceCodeLocator;
use Phpactor\WorseReflection\SourceCode;
use Phpactor\TypeInference\Domain\InferredType;
use Phpactor\WorseReflection\ClassName;
use Phpactor\TypeInference\Domain\SourceCodeLoader;
use Phpactor\TypeInference\Domain\SourceCodeNotFound;
use Phpactor\WorseReflection\Exception\SourceNotFound;

class WorseSourceCodeLocator implements SourceCodeLocator
{
    private $loader;

    public function __construct(SourceCodeLoader $loader)
    {
        $this->loader = $loader;
    }

    public function locate(ClassName $className): SourceCode
    {
        try {
            return SourceCode::fromString(
                $this->loader->loadSourceFor(InferredType::fromString((string) $className))
            );
        } catch (SourceCodeNotFound $e) {
            throw new SourceNotFound($e->getMessage(), null, $e);
        }
    }
}
