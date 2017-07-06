<?php

namespace Phpactor\TypeInference\Tests\Adapter\ClassToFile;

use PHPUnit\Framework\TestCase;
use Phpactor\TypeInference\Adapter\ClassToFile\ClassToFileSourceCodeLoader;
use Phpactor\ClassFileConverter\Adapter\Composer\ComposerClassToFile;
use Phpactor\ClassFileConverter\ClassToFileConverter;
use Phpactor\TypeInference\Domain\InferredType;

class ClassToFileSourceCodeLoaderTest extends TestCase
{
    /**
     * @runTestInSeparateProcess
     */
    public function testLoadSource()
    {
        $autoloader = require(__DIR__ . '/../../../vendor/autoload.php');
        $converter = ClassToFileConverter::fromComposerAutoloader($autoloader);
        $resolver = new ClassToFileSourceCodeLoader($converter);

        $this->assertEquals(
            file_get_contents(__FILE__),
            (string) $resolver->loadSourceFor(
                InferredType::fromString(__CLASS__)
            )
        );
    }
}
