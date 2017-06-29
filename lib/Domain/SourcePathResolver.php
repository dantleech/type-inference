<?php

namespace DTL\TypeInference\Domain;

use DTL\TypeInference\Domain\InferredType;

interface SourcePathResolver
{
    public function resolvePathFor(InferredType $type): SourcePath;
}
