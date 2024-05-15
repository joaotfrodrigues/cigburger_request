<?php

if (!function_exists('format_currency')) {
    function format_currency($value)
    {
        return number_format($value, 2, ',') . '€';
    }
}
