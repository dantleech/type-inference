<?php

namespace Phpactor\TypeInference\Adapter\ClassToFile;

use Phpactor\TypeInference\Domain\SourceCodeLoader;
use Phpactor\TypeInference\Domain\InferredType;
use Phpactor\ClassFileConverter\Domain\ClassToFile;
use Phpactor\TypeInference\Domain\SourceCode;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\TypeInference\Domain\SourceCodeNotFound;

class ClassToFileSourceCodeLoader implements SourceCodeLoader
{
    private $converter;

    public function __construct(ClassToFile $converter)
    {
        $this->converter = $converter;
    }

    public function loadSourceFor(InferredType $type): SourceCode
    {
        $candidates = $this->converter->classToFileCandidates(ClassName::fromString((string) $type));

        if ($candidates->noneFound()) {
            throw new SourceCodeNotFound($type);
        }

        foreach ($candidates as $candidate) {
            if (file_exists((string) $candidate)) {
                return SourceCode::fromString(file_get_contents((string) $candidate));
            }
        }

        throw new SourceCodeNotFound($type);
    }
}
