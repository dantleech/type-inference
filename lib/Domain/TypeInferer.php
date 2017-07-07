<?php

namespace Phpactor\TypeInference\Domain;

interface TypeInferer
{
    public function inferTypeAtOffset(SourceCode $source, Offset $offset): InferredTypeResult;
}
