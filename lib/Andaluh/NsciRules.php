<?php

namespace Andaluh\Rules;

class NsciRules extends BaseRule
{
    public static function apply(string $text): string
    {
        return preg_replace_callback(
            '/nsci/iu',
            function ($match) {
                return self::keepCase($match[0], 'zzi');
            },
            $text
        );
    }
}
