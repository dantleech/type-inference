<?php

namespace DTL\TypeInference\Adapter\ClassToFile;

use DTL\TypeInference\Domain\SourceCodeLoader;
use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\MethodName;
use DTL\ClassFileConverter\Domain\ClassToFile;
use DTL\ClassFileConverter\ClassToFileConverter;
use DTL\TypeInference\Domain\SourcePath;
use DTL\TypeInference\Domain\SourceCode;
use DTL\ClassFileConverter\Domain\ClassName;
use DTL\TypeInference\Domain\SourceCodeNotFound;

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
