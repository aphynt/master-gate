<?php

if (!function_exists('normalizeTime')) {
    function normalizeTime($time) {
        $time = str_replace(['.', ';', '-'], ':', $time);
        if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
            return $time;
        }
        return null;
    }
}
