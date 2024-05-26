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

if (!function_exists('define_order_number_from_last_order_number')) {
    /**
     * Defines the order number and series from the given order number.
     * 
     * This function calculates the order number and series based on the given order number. If the order number is less than 100,
     * the order number remains the same and the order series is set to 0. If the order number is 100 or greater,
     * the order number is calculated as the order number modulo 100, and the order series is calculated as the integer division
     * of the order number by 100.
     * 
     * @param int $new_order_number The order number.
     * 
     * @return array An associative array containing the 'order_number' and 'order_series'.
     */
    function define_order_number_from_last_order_number($new_order_number)
    {
        // defines the order number and series from the order id
        if ($new_order_number < 100) {
            $order_number = $new_order_number;
            $order_series = 0;
        } else {
            $order_number = $new_order_number % 100;
            $order_series = floor($new_order_number / 100);
        }

        return [
            'order_number' => $order_number,
            'order_series' => $order_series
        ];
    }
}
