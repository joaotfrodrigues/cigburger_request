<?php

if (!function_exists('format_currency')) {
    /**
     * Formats a numeric value as currency with euro symbol.
     * 
     * This function takes a numeric value and formats it as currency with two decimal places
     * and the euro symbol. For example, it formats "10.5" as "10,50€".
     * 
     * @param float $value The numeric value to format as currency.
     * 
     * @return string The formatted currency string.
     */
    function format_currency($value)
    {
        return number_format($value, 2, ',') . '€';
    }
}
