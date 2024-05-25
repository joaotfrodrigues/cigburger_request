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

if (!function_exists('define_order_number_from_id')) {
    /**
     * Defines the order number and series from the order ID.
     * 
     * This function calculates the order number and series based on the given order ID. If the order ID is less than 100,
     * the order number is the same as the order ID and the order series is set to 0. If the order ID is 100 or greater,
     * the order number is calculated as the order ID modulo 100, and the order series is calculated as the integer division
     * of the order ID by 100.
     * 
     * @param int $order_id The ID of the order.
     * 
     * @return array An associative array containing the 'order_number' and 'order_series'.
     */
    function define_order_number_from_id($order_id)
    {
        // defines the order number and series from the order id
        if ($order_id < 100) {
            $order_number = $order_id;
            $order_series = 0;
        } else {
            $order_number = $order_id % 100;
            $order_series = floor($order_id / 100);
        }

        return [
            'order_number' => $order_number,
            'order_series' => $order_series
        ];
    }
}
