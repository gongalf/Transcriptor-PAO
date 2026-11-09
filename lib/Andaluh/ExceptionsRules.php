<?php

namespace Andaluh\Rules;

class ExceptionsRules extends BaseRule
{
    const PREPROCESSING_REPLACEMENTS = [
        'para el' => 'pal',
        'todo el' => 'tol',
        'se me' => 'me se',
        'se te' => 'te se',
    ];

    const ORIGINAL_EXCEPTIONS = [
        'andaluz' => 'andalú',
        'así' => 'asín',
        'mucho' => 'munxo',
        'luego' => 'aluego',
        'cuanto más' => 'contra mah',
        'ambos' => 'damboh',
        'enseguida' => 'deseguía',
        'a veces' => 'en bezeh',
        'de vez en cuando' => 'en beh en cuando',
        'pedazo' => 'peazo',
        'pedazos' => 'peazoh',
        'adonde' => 'aonde',
        'adónde' => 'aónde',

        // *** REVISAR ***
        'con respecto a' => 'a tento de',
        'seguidamente' => 'de seguío',
        'a continuación' => 'de seguío',
        'tal vez' => 'tarbéh',
        'este año' => 'ogaño',
        'simultánemante' => 'ar mihmo tiempo',
        'suma importancia' => 'gran importanzia',
        'nada más' => 'namáh que', 
        'sólo' => 'namáh que',
        'sólamente' => 'namáh que',
        'al azar' => 'a boleo',
        'por estos lares' => 'por ehtoh roaleh',
        'continuamente' => 'de contino',

        // *** CONTRACCIONES ***
        'todo lo' => 'tolo',
        'toda la' => 'tola',
        'todos los' => 'toloh',
        'todas las' => 'tolah',

        'con lo' => 'colo',
        'con la' => 'cola',
        'con los' => 'coloh',
        'con las' => 'colah',

        'por lo' => 'polo',
        'por la' => 'pola',
        'por los' => 'poloh',
        'por las' => 'polah',

        'de un' => 'dun',
        'de una' => 'duna',
        'de unos' => 'dunoh',
        'de unas' => 'dunah',

        'voy a' => 'bia',
        // 'vas a' => 'ba',
        // 'va a' => 'ba',
        // 'vamos a' => 'bamoa',
        // 'vais a' => 'baia',

        // *** VERBOS ***
        'hayamos' => 'áigamoh',

        'salís' => 'saléih',

        'saldría' => 'sadría',
        'saldrías' => 'sadríah',
        'saldríamos' => 'sadríamoh',
        'saldríais' => 'sadríaih',
        'saldrían' => 'sadrían',

        'querría' => 'quedría',
        'querrías' => 'quedríah',
        'querríamos' => 'quedríamoh',
        'querríais' => 'quedríaih',
        'querrían' => 'quedrían',

        // *** LÉXICO ***
        'cabaña' => 'xoza',
        'cabañas' => 'xozah',
        'iglesia' => 'ilesia',
        'iglesias' => 'ilesiah',
        'garaje' => 'coxera',
        'garajes' => 'coxerah',
        'hernia' => 'quebranzía',
        'hernias' => 'quebranzíah',
        'herniado' => 'quebrao',
        'herniada' => 'quebrá',
        'herniadoh' => 'quebraoh',
        'herniadah' => 'quebráh',
        'cadera' => 'caera',
        'caderas' => 'caerah',
        'nudo' => 'núo',
        'nudos' => 'núoh',
        'cadena' => 'caena',
        'cadenas' => 'caenah',
        'hucha' => 'arcanzía',
        'huchas' => 'arcanzíah',
        'almanaque' => 'armenaque',
        'almanaques' => 'armenaqueh',
        'hospital' => 'ehpità',
        'hospitales' => 'ehpitaleh',
        'administrador' => 'armenihtraò',
        'administradores' => 'armenihtraoreh',
        'apestoso' => 'pehtoso',
        'manifiestamente' => 'claramente',
        'moneda' => 'monea',
        'monedas' => 'moneah',
        'línea' => 'linia',
        'líneas' => 'liniah',
        'vídeo' => 'bidio',
        'vídeos' => 'bidioh',
        'tembleque' => 'temblique',
        'tembleques' => 'tembliqueh',
        'entrañas' => 'sentrañah',
        'píldora' => 'pahtiya',
        'píldoras' => 'pahtiyah',
        'tobillo' => 'tubiyo',
        'tobillos' => 'tubiyoh',
        'trueno' => 'tronío',
        'truenos' => 'troníoh',
        'cuello' => 'pehcuezo',
        'cuellos' => 'pehcuezoh',
        'ombligo' => 'ombrigo',
        'ombligos' => 'ombrigoh',
        'párpado' => 'párpago',
        'párpados' => 'párpagoh',
        'colina' => 'loma',
        'colinas' => 'lomah',
        'ministro' => 'menihtro',
        'ministros' => 'menihtroh',
        'ministerio' => 'menihterio',
        'ministerios' => 'menihterioh',
        'interlocuzión' => 'plática',
        'interlocuziones' => 'pláticah',
        'esternón' => 'güeso\'er pexo',
        'esternones' => 'güesoh\'er pexo',
        'cerebro' => 'zelebro',
        'cerebros' => 'zelebroh',
        'sofás' => 'sofaleh',
        'rodilla' => 'roíya',
        'rodillas' => 'roíyah',
        'codo' => 'co',
        'codos' => 'coh',
        'persona' => 'presona',
        'personas' => 'presonah',
        'personal' => 'presonà',
        'personales' => 'presonaleh',

        // *** No las puedo usar porque hay cambio de género ***
        // 'mejilla' => 'caxete',
        // 'mejillas' => 'caxeteh',
        // 'omóplato' => 'paletiya',
        // 'omóplatos' => 'paletiyah',
        // 'columna vertebral' => 'ehpinazo',
        // 'columnas vertebrales' => 'ehpinazoh',

        // *** UNIDADES DE MEDIDA ***
        'km' => 'km',
        'kg' => 'kg',
        'kl' => 'kl',
        'kw' => 'kw',
        'kWh' => 'kWh',
        'kb' => 'kb',
        'kB' => 'kB',
        'kHz' => 'kHz',
        
        // *** NOMBRES PROPIOS Y APELLIDOS ***
        'Arnold' => 'Arnold',
        'Schwarzenegger' => 'Schwarzenegger',
        'Hasselhoff' => 'Hasselhoff',
        'Steven' => 'Steven',
        'Seagal' => 'Seagal',
        'Swarovski' => 'Swarovski',
    ];

    const EXCEPTIONS = [
        'todo' => 'to',
        'todoh' => 'toh',
        'toda' => 'toa',
        'todah' => 'toah',
        'es' => 'eh',
        'ex' => 'eh',
        'ir' => 'ì',
        'os' => 'oh',
        'para' => 'pa',
        'todabía' => 'toabía',
        'ademáh' => 'amáh',
        'máh' => 'mah',

        'arrededò' => 'arreò',
        'arrededoreh' => 'arreoreh',
        
        'problema' => 'poblema',
        'problemah' => 'poblemah',
        
        // *** NUEVAS EXCEPCIONES ***
        'ehtoi' => 'ehtoy',
        'soi' => 'soy',
        'doi' => 'doy',
        'boi' => 'boy',
        'rei' => 'rey',
        'lei' => 'ley',
        'oi' => 'oy',
        'ai' => 'ay',
        'ui' => 'uy',
        'ei' => 'ey',
        'mui' => 'mu',
        
        // *** VERBOS ***
        'emoh' => 'abemoh',
        'aya' => 'aiga',
        'ayah' => 'aigah',
        'ayáih' => 'aigaih',
        'ayan' => 'aigan',
        
        // *** CAMBIO DE 'IE' POR 'YE' ***
        'ielo' => 'yelo',
        'ierba' => 'yerba',
        'ierbagüena' => 'yerbagüena',
        'ierbagüenah' => 'yerbagüenah',
    ];

    public static function apply(string $text): string
    {
        return preg_replace_callback(
            '/\b(' .  implode('|', array_keys(self::EXCEPTIONS)) . ')\b/iu',
            function ($match) {
                $word = $match[1];
                $replacement = self::EXCEPTIONS[$word]
                    ?? self::EXCEPTIONS[self::toLowerCase($word)];
                return self::keepOriginalExceptionCase($word, $replacement);
            },
            $text
        );
    }

    public static function protectOriginalWords(string $text, array &$replacements): string
    {
        return preg_replace_callback(
            '/\b(' . implode('|', array_keys(self::ORIGINAL_EXCEPTIONS)) . ')\b/iu',
            function ($match) use (&$replacements, $text) {
                $word = $match[1];
                $replacement = self::ORIGINAL_EXCEPTIONS[$word]
                    ?? self::ORIGINAL_EXCEPTIONS[self::toLowerCase($word)];
                $placeholder = "\x01" . str_repeat("\x02", count($replacements) + 1) . "\x03";

                while (strpos($text, $placeholder) !== false || isset($replacements[$placeholder])) {
                    $placeholder .= "\x02";
                }

                $replacements[$placeholder] = self::keepOriginalExceptionCase($word, $replacement);
                return $placeholder;
            },
            $text
        );
    }

    public static function restoreOriginalWords(
        string $text,
        array $replacements,
        array &$protectedRanges
    ): string {
        if (!$replacements) {
            return $text;
        }

        $placeholders = array_keys($replacements);
        $pattern = '/(' . implode('|', array_map(
            function ($placeholder) {
                return preg_quote($placeholder, '/');
            },
            $placeholders
        )) . ')/';
        $offsetAdjustment = 0;

        return preg_replace_callback(
            $pattern,
            function ($match) use ($replacements, &$protectedRanges, &$offsetAdjustment) {
                $placeholder = $match[1][0];
                $replacement = $replacements[$placeholder];
                $start = $match[1][1] + $offsetAdjustment;
                $protectedRanges[] = [$start, $start + strlen($replacement)];
                $offsetAdjustment += strlen($replacement) - strlen($placeholder);

                return $replacement;
            },
            $text,
            -1,
            $count,
            PREG_OFFSET_CAPTURE
        );
    }

    public static function applyPreprocessingReplacements(string $text): string
    {
        $phrases = array_keys(self::PREPROCESSING_REPLACEMENTS);
        usort($phrases, function ($a, $b) {
            return strlen($b) <=> strlen($a);
        });

        $pattern = '/\b(' . implode('|', array_map(
            function ($phrase) {
                return preg_quote($phrase, '/');
            },
            $phrases
        )) . ')\b/iu';

        return preg_replace_callback(
            $pattern,
            function ($match) {
                $phrase = $match[1];
                $replacement = self::PREPROCESSING_REPLACEMENTS[$phrase]
                    ?? self::PREPROCESSING_REPLACEMENTS[self::toLowerCase($phrase)];
                return self::keepOriginalExceptionCase($phrase, $replacement);
            },
            $text
        );
    }

    private static function keepOriginalExceptionCase(string $word, string $replacement): string
    {
        if (self::isLowerCase($word) || self::isUpperCase($word)) {
            return self::keepCase($word, $replacement);
        }

        if (self::isTitleCase($word)) {
            $replacement = self::toLowerCase($replacement);
            $firstChar = mb_substr($replacement, 0, 1, 'UTF-8');
            return self::toUpperCase($firstChar)
                . mb_substr($replacement, 1, null, 'UTF-8');
        }

        $firstChar = mb_substr($word, 0, 1, 'UTF-8');
        $rest = mb_substr($word, 1, null, 'UTF-8');
        if (
            self::toUpperCase($firstChar) === $firstChar
            && self::toLowerCase($firstChar) !== $firstChar
        ) {
            $replacement = self::toLowerCase($replacement);
            $firstReplacementChar = mb_substr($replacement, 0, 1, 'UTF-8');
            return self::toUpperCase($firstReplacementChar)
                . mb_substr($replacement, 1, null, 'UTF-8');
        }

        return $replacement;
    }
}
