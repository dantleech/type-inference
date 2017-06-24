<?php

namespace DTL\TypeInference\Adapter;

use Microsoft\PhpParser\Parser;
use DTL\TypeInference\Domain\TypeInferer;
use DTL\TypeInference\Domain\SourceCode;
use DTL\TypeInference\Domain\Offset;
use DTL\TypeInference\Domain\InferredType;
use Microsoft\PhpParser\Node\QualifiedName;

class TolerantTypeInferer implements TypeInferer
{
    private $parser;

    public function __construct(Parser $parser = null)
    {
        $this->parser = $parser ?: new Parser();
    }

    public function inferTypeAtOffset(SourceCode $code, Offset $offset): InferredType
    {
        $node = $this->parser->parseSourceFile((string) $code);
        $node = $node->getDescendantNodeAtPosition($offset->asInt());

        if ($node instanceof QualifiedName) {
            $imports = $node->getImportTablesForCurrentScope();
            $classImports = $imports[0];

            if (isset($classImports[$node->getText()])) {
                return InferredType::fromString((string) $classImports[$node->getText()]);
            }

            if ($namespaceDefinition = $node->getNamespaceDefinition()) {
                return InferredType::fromParts([$namespaceDefinition->name->getText(), $node->getText()]);
            }

            return InferredType::fromString($node->getText());
        }

        return InferredType::unknown();
    }
}
