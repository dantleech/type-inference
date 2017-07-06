<?php

namespace Phpactor\TypeInference\Domain;

class SourceCodeNotFound extends \DomainException
{
    public function __construct(InferredType $type)
    {
        parent::__construct(sprintf(
            'Source code for type "%s" not found',
            (string) $type
        ));
    }
}
