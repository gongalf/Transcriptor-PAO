<?php

namespace Andaluh\Rules;

class WordEndingRules extends BaseRule
{
    const WORDEND_D_RULES_EXCEPT = [
        // 'sé' => 'seh'
    ];

    const WORDEND_S_RULES_EXCEPT = [
        /* 'bies' => 'biêh', 'bis' => 'bîh', 'blues' => 'blû', 'bus' => 'bûh',
        'dios' => 'diôh', 'dos' => 'dôh',
        'gas' => 'gâh', 'gres' => 'grêh', 'gris' => 'grîh',
        'luis' => 'luîh',
        'mies' => 'miêh', 'mus' => 'mûh',
        'os' => 'ô',
        'pis' => 'pîh', 'plus' => 'plûh', 'pus' => 'pûh',
        'ras' => 'râh', 'res' => 'rêh',
        'tos' => 'tôh', 'tres' => 'trêh', 'tris' => 'trîh' */
    ];

    const WORDEND_CONST_RULES_EXCEPT = [
        'al' => 'al',
        // 'cual' => 'cuà',
        'del' => 'del',
        'dél' => 'dél',
        'el' => 'el',
        'él' => 'èl',
        'pal' => 'pal',
        'tol' => 'tol',
        'tal' => 'tal',
        'aquel' => 'aquel',
        // 'bil' => 'bîl',
        # TODO: uir = huir. Maybe better to add the exceptions on h_rules?
        'por' => 'por',
        'primer' => 'primer',
        'cuarquier' => 'cuarquier', // ¿cualihquier?
        # sic, tac
        // 'çic' => 'çic', 'tac' => 'tac',
        // 'yak' => 'yak',
        // 'stop' => 'êttôh',
        // 'bip' => 'bip'
    ];

    const WORDEND_D_INTERVOWEL_RULES_EXCEPT = [
        # Ending with -ado
        //"fado" => "fado", "cado" => "cado", "nado" => "nado", "priado" => "priado",
        # Ending with -ada
        "fabada" => "fabada",
        'fabadas' => 'fabadas',
        "fada" => "fada",
        "ada" => "ada",
        "lada" => "lada",
        "rada" => "rada",
        # Ending with -adas
        "adas" => "adas", // "radas" => "radas", "nadas" => "nadas",
        # Ending with -ido
        // "aikido" => "aikido",
        // "bûççido" => "bûççido",
        // "çido" => "çido",
        "cuido" => "cuido",
        // "cupido" => "cupido",
        "descuido" => "descuido",
        // "despido" => "despido",
        "eido" => "eido",
        // "embido" => "embido",
        "fido" => "fido",
        "hido" => "hido",
        // "ido" => "ido",
        "infido" => "infido",
        "laido" => "laido",
        // "libido" => "libido",
        // "nido" => "nido",
        "nucleido" => "nucleido",
        // "çonido" => "çonido",
        // "çuido" => "çuido"
    ];

    const  UNSTRESSED_RULES = [
        'a' => 'à', 'A' => 'À', 'á' => 'à', 'Á' => 'À',
        'e' => 'è',  'E' => 'È', 'é' => 'è', 'É' => 'È',
        'i' => 'ì', 'I' => 'Ì', 'í' => 'ì', 'Í' => 'Ì',
        'o' => 'ò', 'O' => 'Ò', 'ó' => 'ò', 'Ó' => 'Ò',
        'u' => 'ù', 'U' => 'Ù', 'ú' => 'ù', 'Ú' => 'Ù'
    ]; 

    const STRESSED_RULES = [
        'a' => 'á', 'A' => 'Á', 'á' => 'á', 'Á' => 'Á',
        'e' => 'é', 'E' => 'É', 'é' => 'é', 'É' => 'É',
        'i' => 'î', 'I' => 'Î', 'í' => 'î', 'Í' => 'Î',
        'o' => 'ô', 'O' => 'Ô', 'ó' => 'ô', 'Ó' => 'Ô',
        'u' => 'û', 'U' => 'Û', 'ú' => 'û', 'Ú' => 'Û'
    ];

    public static function apply(string $text): string
    {
        return preg_replace_callback_array(
            [
                '/\b(\w*?)(i?d?)(ad)(e?s?)\b/iu' => [self::class, 'replaceIntervowelDADEndWithCase'],
                '/\b(\w*?)(a|i|í|Í|u|ú|U|Ú)(d)(o|a)(?P<s>s?)\b/iu' => [self::class, 'replaceIntervowelDEndWithCase'],
                '/\b(\w+?)(e)(ps)\b/iu' => [self::class, 'replaceEpsEndWithCase'],
                '/\b(\w+?)(a|e|i|o|u|á|é|í|ó|ú)(d)\b/iu' => [self::class, 'replaceDEndWithCase'],
                '/\b(\w+?)(a|e|i|o|u|á|é|í|ó|ú)(s|z|t)\b/iu' => [self::class, 'replaceSEndWithCase'],
                '/\b(\w+?)(a|e|i|o|u|á|é|í|ó|ú)(b|c|f|g|j|k|l|p|r|x)\b/iu' => [self::class, 'replaceConstEndWithCase'],
                '/\b(\w+?)(y)\b/iu' => [self::class, 'replaceYEndWithCase'],
            ],
            $text
        );
    }

    private static function replaceIntervowelDEndWithCase(array $match): string
    {
        $wordLower = self::toLowerCase($match[0]);
        if (array_key_exists($wordLower, self::WORDEND_D_INTERVOWEL_RULES_EXCEPT)) {
            return self::keepCase(
                $wordLower,
                self::WORDEND_D_INTERVOWEL_RULES_EXCEPT[$wordLower]
            );
        }

        $prefix = $match[1];
        if (self::containsTildeVowel($prefix)) {
            return $match[0];
        }

        $suffixVowelA = $match[2];
        $suffixDChar = $match[3];
        $suffixVowelB =  $match[4];
        $endingS = $match['s'];

        $suffix = "{$suffixVowelA}{$suffixDChar}{$suffixVowelB}{$endingS}";

        switch (self::toLowerCase($suffix)) {
            case 'ada':
                return $prefix . self::keepCase($suffixVowelB, 'á');
            case 'adas':
                return $prefix . self::keepCase($suffix, 'áh');
            case 'udo':
                return $prefix . self::keepCase($suffixVowelB, 'úo');
            case 'udos':
                return $prefix . self::keepCase($suffix, 'úoh');
            case 'ado':
                return $prefix . $suffixVowelA . $suffixVowelB;
            case 'ados':
                return $prefix . $suffixVowelA . $suffixVowelB . 'h';
            case 'idos':
            case 'ídos':
            case 'idas':
            case 'ídas':
                return $prefix . self::getVowelTilde($suffixVowelA) . $suffixVowelB . 'h';
            case 'ido':
            case 'ído':
            case 'ida':
            case 'ída':
                return $prefix . self::keepCase($suffixVowelA, 'í') . $suffixVowelB;
            default:
                return $match[0];
        }
    }

    private static function replaceIntervowelDADEndWithCase(array $match): string
    {
            $wordLower = self::toLowerCase($match[0]);
            /* if (array_key_exists($wordLower, self::WORDEND_D_INTERVOWEL_RULES_EXCEPT)) {
                return self::keepCase(
                    $wordLower,
                    self::WORDEND_D_INTERVOWEL_RULES_EXCEPT[$wordLower]
                );
            } */

            $prefix = $match[1];
            /* if (self::containsTildeVowel($prefix)) {
                return $match[0];
            } */

            $suffix = $match[2] . $match[3] . $match[4];

            switch (self::toLowerCase($suffix)) {
                case "idad":
                    if (self::endsWithVowel($prefix)) {
                        return $prefix . self::keepCase($suffix, 'idá');
                    }
                    return $prefix . self::keepCase($suffix, 'iá');
                case "idades":
                    if (self::endsWithVowel($prefix)) {
                        return $prefix . self::keepCase($suffix, 'idadeh');
                    }
                    return $prefix . self::keepCase($suffix, 'iaeh');
                case "dad":
                    if ($match[2] !== '' && self::endsWithConsonant($prefix)) {
                        return $prefix
                            . self::keepCase($match[2], 'd')
                            . self::keepCase($suffix, 'á');
                    }
                    return $prefix . self::keepCase($suffix, 'á');
                case "dades":
                    if ($match[2] !== '' && self::endsWithConsonant($prefix)) {
                        return $prefix
                            . self::keepCase($match[2], 'd')
                            . self::keepCase($suffix, 'aeh');
                    }
                    return $prefix . self::keepCase($suffix, 'aeh');
                case "ades":
                    return $prefix . self::keepCase($suffix, 'aeh');
                default:
                    return $match[0];
            }
    }

    private static function endsWithConsonant(string $text): bool
    {
        return preg_match('/[b-df-hj-np-tv-zçñ]$/iu', $text) === 1;
    }

    private static function endsWithVowel(string $text): bool
    {
        return preg_match('/[aeiouáéíóú]$/iu', $text) === 1;
    }

    // Como "bíceps"
    private static function replaceEpsEndWithCase(array $match): string
    {
        [
            //$word, 
            ,
            $prefix,
            $suffix_vowel,
            $suffix_const
        ] = $match;

        return self::isLowerCase($match[1])
            ? $prefix . 'eh'
            : $prefix . 'EH';

        // return $prefix . 'eh';

        /* //  Leave as it is. There shouldn't be any word with -eps ending withough accent.
        if (!self::containsTildeVowel($prefix)) {
            return "{$prefix}{$suffix_vowel}{$suffix_const}";
        }
        return $prefix . self::keepCase($suffix_vowel, 'ê'); */
    }

    private static function replaceDEndWithCase(array $match): string
    {
        [$word, $prefix, $suffix_vowel, $suffix_const] = $match;
        $wordLower = self::toLowerCase($word);
        if (array_key_exists($wordLower, self::WORDEND_D_RULES_EXCEPT)) {
            return self::keepCase(
                $wordLower,
                self::WORDEND_D_RULES_EXCEPT[$wordLower]
            );
        }

        if (in_array(self::toLowerCase($suffix_vowel), ['i', 'í', 'o', 'ó', 'u', 'ú'], true)) {
            return $prefix . self::getVowelTilde($suffix_vowel);
        }

        if (self::containsTildeVowel($prefix)) {
            $unstressed = self::UNSTRESSED_RULES[$suffix_vowel];
            return "{$prefix}{$unstressed}";
        }

        $stressed = self::STRESSED_RULES[$suffix_vowel];
        if (in_array($suffix_vowel, ['a', 'e', 'A', 'E', 'á', 'é', 'Á', 'É'])) {
            return "{$prefix}{$stressed}";
        }

        return "{$prefix}{$stressed}" . self::keepCase($suffix_const, 'h');
    }

    private static function replaceSEndWithCase(array $match): string
    {
        [$word, $prefix, $suffix_vowel, $suffix_const] = $match;
        $wordLower = self::toLowerCase($word);
        if (array_key_exists($wordLower, self::WORDEND_S_RULES_EXCEPT)) {
            return self::keepCase(
                $wordLower,
                self::WORDEND_S_RULES_EXCEPT[$wordLower]
            );
        }

        return "{$prefix}{$suffix_vowel}" . self::keepCase($suffix_const, 'h');

        /* $unstressed = self::UNSTRESSED_RULES[$suffix_vowel];
        if (!self::containsTildeVowel($suffix_vowel)) {
            return "{$prefix}{$unstressed}";
        }

        return "{$prefix}{$unstressed}" . self::keepCase($suffix_const, 'h'); */
    }

    private static function replaceConstEndWithCase(array $match): string
    {
        [$word, $prefix, $suffix_vowel, $suffix_const] = $match;
        $wordLower = self::toLowerCase($word);
        if (array_key_exists($wordLower, self::WORDEND_CONST_RULES_EXCEPT)) {
            return self::keepCase(
                $word,
                self::WORDEND_CONST_RULES_EXCEPT[$wordLower]
            );
        }

        return $prefix . self::UNSTRESSED_RULES[$suffix_vowel];

        // $unstressed = self::UNSTRESSED_RULES[$suffix_vowel];
        // return "{$prefix}{$unstressed}";

        /* $unstressed = self::UNSTRESSED_RULES[$suffix_vowel];
        if (self::containsTildeVowel($prefix)) {
            return "{$prefix}{$unstressed}";
        }

        return "{$prefix}{$unstressed}" . self::keepCase($suffix_const, 'h'); */
    }

    // Como "bíceps"
    private static function replaceYEndWithCase(array $match): string
        {
            if (self::toLowerCase($match[0]) === 'ay') {
                return $match[0];
            }

            [
                //$word,
                ,
                $prefix,
                $suffix
            ] = $match;

            return self::isLowerCase($match[1])
                ? $prefix . 'i'
                : $prefix . 'I';

            /* //  Leave as it is. There shouldn't be any word with -eps ending withough accent.
            if (!self::containsTildeVowel($prefix)) {
                return "{$prefix}{$suffix_vowel}{$suffix_const}";
            }
            return $prefix . self::keepCase($suffix_vowel, 'ê'); */
        }
}
