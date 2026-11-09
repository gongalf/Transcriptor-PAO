<?php

namespace Andaluh\Rules;

class WordInteractionRules extends BaseRule
{
    public static function apply(string $text, array $protectedRanges = []): string
    {
        // Rotating word ending /l/ with /r/ if first next word char is non-l or vowel.
        $text = preg_replace_callback(
            '/\b(\w*?)(l)(\s)(b|c|ç|d|f|g|h|j|k|m|n|ñ|p|q|r|s|t|v|w|x|y|z)/iu',
            function ($match) use ($protectedRanges) {
                if (self::overlapsProtectedRange($match[2][1], $match[2][1] + strlen($match[2][0]), $protectedRanges)) {
                    return $match[0][0];
                }

                $rChar = self::keepCase($match[2][0], 'r');
                return $match[1][0] . $rChar . $match[3][0] . $match[4][0];
            },
            $text,
            -1,
            $count,
            PREG_OFFSET_CAPTURE
        );

        $wordsEndingInR = array_values(array_filter(
            WordEndingRules::WORDEND_CONST_RULES_EXCEPT,
            function ($word) {
                return self::toLowerCase(mb_substr($word, -1, null, 'UTF-8')) === 'r';
            }
        ));

        if (!$wordsEndingInR) {
            return $text;
        }

        $pattern = '/\b(' . implode('|', array_map(
            function ($word) {
                return preg_quote($word, '/');
            },
            $wordsEndingInR
        )) . ')(\s)(l)/iu';

        return preg_replace_callback(
            $pattern,
            function ($match) use ($protectedRanges) {
                $word = $match[1][0];
                $wordStart = $match[1][1];
                if (self::overlapsProtectedRange($wordStart, $wordStart + strlen($word), $protectedRanges)) {
                    return $match[0][0];
                }

                $prefix = mb_substr($word, 0, -1, 'UTF-8');
                $finalR = mb_substr($word, -1, null, 'UTF-8');
                $finalL = self::keepCase($finalR, 'l');
                return "{$prefix}{$finalL}{$match[2][0]}{$match[3][0]}";
            },
            $text,
            -1,
            $count,
            PREG_OFFSET_CAPTURE
        );
    }

    private static function overlapsProtectedRange(int $start, int $end, array $protectedRanges): bool
    {
        foreach ($protectedRanges as [$protectedStart, $protectedEnd]) {
            if ($start < $protectedEnd && $end > $protectedStart) {
                return true;
            }
        }

        return false;
    }
}
