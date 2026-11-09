<?php

namespace Andaluh\Rules;

class KRules extends BaseRule
{
    public static function apply(string $text): string
    {
        return preg_replace_callback(
            '/\b\w+\b/iu',
            function ($match) {
                $word = $match[0];
                if (preg_match('/^kil[oó]/iu', $word)) {
                    return $word;
                }

                return preg_replace_callback(
                    '/k([aeiou])/iu',
                    function ($match) {
                        $replacement = in_array(
                            self::toLowerCase($match[1]),
                            ['e', 'i'],
                            true
                        ) ? 'qu' : 'c';

                        return self::keepCase($match[0][0], $replacement) . $match[1];
                    },
                    $word
                );
            },
            $text
        );
    }
}
