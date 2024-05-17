<?php

if (!function_exists('init_order')) {
    /**
     * Initializes a new order by clearing any existing order data and setting up a new order structure.
     * 
     * This function clears any previous order using `delete_order`, and then initializes a new order
     * in the session with an empty items list and a status of 'new'.
     * 
     * @return void
     */
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
    /**
     * Deletes the current order and selected category from the session.
     * 
     * This function removes the 'order' and 'selected_category' session variables, effectively
     * clearing any existing order data and resetting the selected category to null.
     * 
     * @return void
     */
    function delete_order()
    {
        // clear order from session
        session()->remove('order');
        session()->remove('selected_category');
    }
}

if (!function_exists('get_order')) {
    /**
     * Retrieves the current order from the session.
     * 
     * This function fetches the 'order' session variable, which contains information about 
     * the current order, and returns it.
     * 
     * @return mixed The current order data stored in the session.
     */
    function get_order()
    {
        // get order from session
        return session()->get('order');
    }
}
