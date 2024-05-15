<?php

if (!function_exists('init_order')) {
    function init_order()
    {
        // clear the previous order, if exists
        delete_order();

        // set new order
        session()->set('order', [
            'items' => [],
            'status' => 'new'
        ]);
    }
}

if (!function_exists('delete_order')) {
    function delete_order()
    {
        // clear order from session
        session()->remove('order');
        session()->remove('selected_category');
    }
}

if (!function_exists('get_order')) {
    function get_order()
    {
        // get order from session
        return session()->get('order');
    }
}