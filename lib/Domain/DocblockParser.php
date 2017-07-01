<?php

namespace DTL\TypeInference\Domain;

final class DocblockParser
{
    const TAGS = [
        'var',
    ];

    public function parse(string $text): Docblock
    {
        $tags = [];
        foreach (self::TAGS as $tag) {
            if (false === preg_match(sprintf('{@%s (?<value>[\w\\\]+)}', $tag), $text, $matches)) {
                continue;
            }

            $tags[] = DocblockTag::fromNameAndValue($tag, $matches['value']);
        }

        return Docblock::fromTags($tags);
    }
}
