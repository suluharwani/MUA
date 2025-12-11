<?php

namespace App\Helpers;

if (!function_exists('character_limiter')) {
    /**
     * Character Limiter
     * 
     * @param string $str
     * @param int $n
     * @param string $end_char
     * @return string
     */
    function character_limiter(string $str, int $n = 500, string $end_char = '&#8230;'): string
    {
        // Jika string kosong atau lebih pendek dari limit
        if (empty($str) || mb_strlen($str) <= $n) {
            return $str;
        }
        
        // Replace multiple spaces/newlines
        $str = preg_replace('/\s+/', ' ', $str);
        
        // Potong teks
        $out = mb_substr($str, 0, $n);
        
        // Cari posisi spasi terakhir agar tidak memotong kata
        $last_space = mb_strrpos($out, ' ');
        
        if ($last_space !== false && $last_space > ($n * 0.8)) {
            $out = mb_substr($out, 0, $last_space);
        }
        
        return trim($out) . $end_char;
    }
}

if (!function_exists('word_limiter')) {
    /**
     * Word Limiter
     * 
     * @param string $str
     * @param int $limit
     * @param string $end_char
     * @return string
     */
    function word_limiter(string $str, int $limit = 100, string $end_char = '&#8230;'): string
    {
        if (trim($str) === '') {
            return $str;
        }
        
        preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $str, $matches);
        
        if (!isset($matches[0]) || mb_strlen($matches[0]) === mb_strlen($str)) {
            $end_char = '';
            return $str;
        }
        
        return rtrim($matches[0]) . $end_char;
    }
}