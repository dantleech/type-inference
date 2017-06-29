<?php

namespace DTL\TypeInference\Tests\Adapter\ClassToFile;

use PHPUnit\Framework\TestCase;
use DTL\TypeInference\Adapter\ClassToFile\ClassToFileSourcePathResolver;
use DTL\ClassFileConverter\Adapter\Composer\ComposerClassToFile;
use DTL\ClassFileConverter\ClassToFileConverter;
use DTL\TypeInference\Domain\InferredType;

class ClassToFileSourcePathResolverTest extends TestCase
{
    /**
     * @runTestInSeparateProcess
     */
    public function testClassToFile()
    {
        $autoloader = require(__DIR__ . '/../../../vendor/autoload.php');
        $converter = ClassToFileConverter::fromComposerAutoloader($autoloader);
        $resolver = new ClassToFileSourcePathResolver($converter);

        $this->assertEquals(
            __FILE__,
            (string) $resolver->resolvePathFor(
                InferredType::fromString(__CLASS__)
            )
        );
    }
}
