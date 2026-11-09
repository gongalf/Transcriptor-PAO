<?php

namespace Andaluh\Rules;

class ZzRules extends BaseRule
{
    public static function apply(string $text): string
    {
        return preg_replace_callback(
            '/zz(?=ión|ioneh)/iu',
            function ($match) {
                return mb_substr($match[0], 0, 1, 'UTF-8');
            },
            $text
        );
    }
}
