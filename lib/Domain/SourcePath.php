<?php

namespace DTL\TypeInference\Domain;

final class SourcePath 
{
    private $path;

    private function __construct(string $absolutePath)
    {
        $this->path = $absolutePath;
    }

    public static function fromString(string $absolutePath)
    {
        if (null !== $absolutePath && substr($absolutePath, 0, 1) !== '/') {
            throw new \RuntimeException(sprintf(
                'Path must be absolute, got "%s"', $absolutePath
            ));
        }

        return new self($absolutePath);
    }

    public static function unknown()
    {
        return new self();
    }

    public function __toString()
    {
        return $this->path ?: '<unknown>';
    }
}
