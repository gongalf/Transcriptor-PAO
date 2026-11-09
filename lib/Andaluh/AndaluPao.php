<?php

namespace Andaluh;
require_once('BaseRule.php');
require_once('ChRules.php');
require_once('DigraphRules.php');
require_once('ExceptionsRules.php');
require_once('GJRules.php');
require_once('HRules.php');
require_once('KRules.php');
require_once('LlRules.php');
require_once('LRules.php');
require_once('NsciRules.php');
require_once('PsicoPseudoRules.php');
require_once('VAFRules.php');
require_once('VRules.php');
require_once('WordEndingRules.php');
require_once('WordInteractionRules.php');
require_once('XRules.php');
require_once('ZzRules.php');

use Andaluh\Rules\BaseRule;
use Andaluh\Rules\ChRules;
use Andaluh\Rules\DigraphRules;
use Andaluh\Rules\ExceptionsRules;
use Andaluh\Rules\GJRules;
use Andaluh\Rules\HRules;
use Andaluh\Rules\KRules;
use Andaluh\Rules\LlRules;
use Andaluh\Rules\LRules;
use Andaluh\Rules\NsciRules;
use Andaluh\Rules\PsicoPseudoRules;
use Andaluh\Rules\VAFRules;
use Andaluh\Rules\VRules;
use Andaluh\Rules\WordEndingRules;
use Andaluh\Rules\WordInteractionRules;
use Andaluh\Rules\XRules;
use Andaluh\Rules\ZzRules;

class AndaluPao
{
    private $rules = [
        HRules::class,
        XRules::class,
        ChRules::class,
        KRules::class,
        GJRules::class,
        VRules::class,
        LlRules::class,
        LRules::class,
        NsciRules::class,
        PsicoPseudoRules::class,
        VAFRules::class,
        WordEndingRules::class,
        DigraphRules::class,
        ExceptionsRules::class,
        ZzRules::class
    ];

    /**
     * Transliterate español (spanish) spelling to andaluz proposals
     *
     * @param string $text
     * @param string $vaf
     * @param string $vvf
     * @param boolean $escape_links
     * @param boolean $debug
     * @return string trnasliterated string 
     */
    public function transliterate(
        string $text,
        string $vaf_s = BaseRule::VAF_S,
        string $vaf_z = BaseRule::VAF_Z,
        string $vvf = BaseRule::VVF,
        bool $escape_links = false, //TODO
        bool $debug = false //TODO
    ): string {

        $text = ExceptionsRules::applyPreprocessingReplacements($text);

        $originalExceptions = [];
        $text = ExceptionsRules::protectOriginalWords($text, $originalExceptions);

        $text = array_reduce(
            $this->rules,
            function ($text, $ruleClass) use ($vaf_s, $vaf_z, $vvf) {
                if ($ruleClass === XRules::class || $ruleClass == VAFRules::class) {
                    return $ruleClass::apply($text, $vaf_s, $vaf_z);
                }
                if ($ruleClass === GjRules::class) {
                    return $ruleClass::apply($text, $vvf);
                }
                return $ruleClass::apply($text);
            },
            $text
        );

        $protectedRanges = [];
        $text = ExceptionsRules::restoreOriginalWords(
            $text,
            $originalExceptions,
            $protectedRanges
        );

        return WordInteractionRules::apply($text, $protectedRanges);
    }
}
