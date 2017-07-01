<?php

namespace DTL\TypeInference\Tests;

use PHPUnit\Framework\TestCase;
use DTL\TypeInference\TypeInference;
use DTL\TypeInference\Adapter\Dummy\DummySourceCodeLoader;

class TypeInferenceTest extends TestCase
{
    public function testFacade()
    {
        $source = <<<'EOT'
<?php

$foobar = 'Hello';
EOT
        ;
        $inference = new TypeInference();
        $inference->inferTypeAtOffset($source, 12);
    }

    public function testWithLoader()
    {
        $source = <<<'EOT'
<?php

$foobar = 'Hello';
EOT
        ;
        $inference = TypeInference::withSourceCodeLoader(new DummySourceCodeLoader());
        $inference->inferTypeAtOffset($source, 12);
    }
}
