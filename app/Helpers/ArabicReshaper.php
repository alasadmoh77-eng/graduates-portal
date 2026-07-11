<?php

namespace App\Helpers;

class ArabicReshaper
{
    private static $map = [
        0x0621 => [0x0621, 0x0621, 0x0621, 0x0621], // Hamza
        0x0622 => [0x0622, 0x0622, 0xFE82, 0xFE82], // Alef Mad
        0x0623 => [0x0623, 0x0623, 0xFE84, 0xFE84], // Alef Hamza Above
        0x0624 => [0x0624, 0x0624, 0xFE86, 0xFE86], // Waw Hamza Above
        0x0625 => [0x0625, 0x0625, 0xFE88, 0xFE88], // Alef Hamza Below
        0x0626 => [0xFE89, 0xFE8B, 0xFE8C, 0xFE8A], // Yeh Hamza Above
        0x0627 => [0x0627, 0x0627, 0xFE8E, 0xFE8E], // Alef
        0x0628 => [0xFE8F, 0xFE91, 0xFE92, 0xFE90], // Beh
        0x0629 => [0xFE93, 0xFE93, 0xFE94, 0xFE94], // Teh Marbuta
        0x062A => [0xFE95, 0xFE97, 0xFE98, 0xFE96], // Teh
        0x062B => [0xFE99, 0xFE9B, 0xFE9C, 0xFE9A], // Theh
        0x062C => [0xFE9D, 0xFE9F, 0xFEA0, 0xFE9E], // Jeem
        0x062D => [0xFEA1, 0xFEA3, 0xFEA4, 0xFEA2], // Hah
        0x062E => [0xFEA5, 0xFEA7, 0xFEA8, 0xFEA6], // Khah
        0x062F => [0xFEA9, 0xFEA9, 0xFEAA, 0xFEAA], // Dal
        0x0630 => [0xFEAB, 0xFEAB, 0xFEAC, 0xFEAC], // Thal
        0x0631 => [0xFEAD, 0xFEAD, 0xFEAE, 0xFEAE], // Reh
        0x0632 => [0xFEAF, 0xFEAF, 0xFEB0, 0xFEB0], // Zain
        0x0633 => [0xFEB1, 0xFEB3, 0xFEB4, 0xFEB2], // Seen
        0x0634 => [0xFEB5, 0xFEB7, 0xFEB8, 0xFEB6], // Sheen
        0x0635 => [0xFEB9, 0xFEBB, 0xFEBC, 0xFEBA], // Sad
        0x0636 => [0xFEBD, 0xFEBF, 0xFEC0, 0xFEBE], // Dad
        0x0637 => [0xFEC1, 0xFEC3, 0xFEC4, 0xFEC2], // Tah
        0x0638 => [0xFEC5, 0xFEC7, 0xFEC8, 0xFEC6], // Zah
        0x0639 => [0xFEC9, 0xFECB, 0xFECC, 0xFECA], // Ain
        0x063A => [0xFECD, 0xFECF, 0xFED0, 0xFECE], // Ghain
        0x0641 => [0xFED1, 0xFED3, 0xFED4, 0xFED2], // Feh
        0x0642 => [0xFED5, 0xFED7, 0xFED8, 0xFED6], // Qaf
        0x0643 => [0xFED9, 0xFEDB, 0xFEDC, 0xFEDA], // Kaf
        0x0644 => [0xFEDD, 0xFEDF, 0xFEE0, 0xFEDE], // Lam
        0x0645 => [0xFEE1, 0xFEE3, 0xFEE4, 0xFEE2], // Meem
        0x0646 => [0xFEE5, 0xFEE7, 0xFEE8, 0xFEE6], // Noon
        0x0647 => [0xFEE9, 0xFEEB, 0xFEEC, 0xFEEA], // Heh
        0x0648 => [0xFEED, 0xFEED, 0xFEEE, 0xFEEE], // Waw
        0x0649 => [0xFEEF, 0xFEEF, 0xFEF0, 0xFEF0], // Alef Maksura
        0x064A => [0xFEF1, 0xFEF3, 0xFEF4, 0xFEF2], // Yeh
    ];

    private static $joins_right = [0x0622, 0x0623, 0x0624, 0x0625, 0x0627, 0x062F, 0x0630, 0x0631, 0x0632, 0x0648, 0x0649];

    public static function utf8Glyphs($text)
    {
        $text = (string) $text;
        $res = '';
        $chars = self::utf8_to_codes($text);
        $count = count($chars);

        for ($i = 0; $i < $count; $i++) {
            $current = $chars[$i];
            
            if (!isset(self::$map[$current])) {
                $res .= self::code_to_utf8($current);
                continue;
            }

            $prev = ($i > 0) ? $chars[$i - 1] : null;
            $next = ($i < $count - 1) ? $chars[$i + 1] : null;

            $join_prev = $prev !== null && isset(self::$map[$prev]) && !in_array($prev, self::$joins_right);
            $join_next = $next !== null && isset(self::$map[$next]);

            if ($join_prev && $join_next) {
                $form = 2; // Medial
            } elseif ($join_prev) {
                $form = 3; // Final
            } elseif ($join_next) {
                $form = 1; // Initial
            } else {
                $form = 0; // Isolated
            }

            $res .= self::code_to_utf8(self::$map[$current][$form]);
        }

        // PDF rendering engines often need the string to be reversed for RTL
        return self::utf8_rev($res);
    }

    private static function utf8_to_codes($text)
    {
        $codes = [];
        $len = strlen($text);
        for ($i = 0; $i < $len; $i++) {
            $ord = ord($text[$i]);
            if ($ord < 128) $codes[] = $ord;
            elseif ($ord < 224) { $codes[] = (($ord & 31) << 6) | (ord($text[++$i]) & 63); }
            elseif ($ord < 240) { $codes[] = (($ord & 15) << 12) | ((ord($text[++$i]) & 63) << 6) | (ord($text[++$i]) & 63); }
        }
        return $codes;
    }

    private static function code_to_utf8($code)
    {
        if ($code < 128) return chr($code);
        if ($code < 2048) return chr(192 | ($code >> 6)) . chr(128 | ($code & 63));
        if ($code < 65536) return chr(224 | ($code >> 12)) . chr(128 | (($code >> 6) & 63)) . chr(128 | ($code & 63));
        return '';
    }

    private static function utf8_rev($text)
    {
        preg_match_all('/./us', $text, $ar);
        $chars = array_reverse($ar[0]);
        $res = implode('', $chars);
        
        // Fix Latin sequences that got reversed (e.g., "demhA" -> "Ahmed")
        return preg_replace_callback('/[a-zA-Z0-9\s\-\.\:\/]{2,}/', function($matches) {
            return strrev($matches[0]);
        }, $res);
    }
}
