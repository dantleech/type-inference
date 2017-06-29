<?php

namespace DTL\TypeInference\Adapter\ClassToFile;

use DTL\TypeInference\Domain\SourcePathResolver;
use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\MethodName;
use DTL\ClassFileConverter\Domain\ClassToFile;
use DTL\ClassFileConverter\ClassToFileConverter;
use DTL\TypeInference\Domain\SourcePath;

class ClassToFileSourcePathResolver implements SourcePathResolver
{
    private $converter;

    public function __construct(ClassToFileConverter $converter)
    {
        $this->converter = $converter;
    }

    public function resolvePathFor(InferredType $type): SourcePath
    {
        $candidates = $this->converter->classToFileCandidates((string) $type);

        if ($candidates->noneFound()) {
            return SourcePath::none();
        }

        foreach ($candidates as $candidate) {
            if (file_exists((string) $candidate)) {
                return $candidate;
            }
        }

        return SourcePath::none();
    }
}
