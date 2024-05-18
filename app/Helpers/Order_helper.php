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

if (!function_exists('get_total_order_items')) {
    /**
     * Calculates the total number of items in the current order.
     * 
     * This function retrieves the current order from the session and sums the quantities 
     * of all items in the order. It returns the total number of items.
     * 
     * @return int The total quantity of items in the current order.
     */
    function get_total_order_items()
    {
        // get order from session
        $order = get_order();

        $total = 0;
        foreach ($order['items'] as $item) {
            $total += $item['quantity'];
        }

        return $total;
    }
}

if (!function_exists('get_order_product_quantity')) {
    /**
     * Retrieves the quantity of a product in the current order.
     * 
     * This function fetches the current order from the session and checks if the product with the specified ID
     * exists in the order items. If the product exists, it returns the quantity of that product in the order.
     * If the product does not exist in the order or if the order is empty, it returns 0.
     * 
     * @param int $id The ID of the product to retrieve the quantity for.
     * 
     * @return int The quantity of the specified product in the current order, or 0 if not found.
     */
    function get_order_product_quantity($id)
    {
        $order = get_order();

        if (empty($order['items'])) {
            return 0;
        }

        if (key_exists($id, $order['items'])) {
            return $order['items'][$id]['quantity'];
        }

        return 0;
    }
}

if (!function_exists('update_order')) {
    /**
     * Updates the order with the specified product ID, quantity, and price.
     * 
     * This function retrieves the current order from the session and updates it based on the provided
     * product ID, quantity, and price. If the product already exists in the order, it updates the quantity
     * of the product. If the quantity is 0, it removes the product from the order. If the product is not
     * already in the order and the quantity is greater than 0, it adds the new product with the specified 
     * quantity and price. Finally, it updates the order in the session with the modified order data.
     * 
     * @param int $product_id The ID of the product to update in the order.
     * @param int $quantity The new quantity of the product.
     * @param float $price The price of the product.
     * 
     * @return void
     */
    function update_order($product_id, $quantity, $price)
    {
        // get order from session
        $order = get_order();

        // check if product already exists in the order
        if (key_exists($product_id, $order['items'])) {
            if ($quantity == 0) {
                // remove product from order
                unset($order['items'][$id]);
            } else {
                // update product quantity
                $order['items'][$product_id]['quantity'] = $quantity;
            }
        } else {
            // check if quantity is 0
            if (quantity === 0) return;

            // add new product to the order
            $order['items'][$product_id] = [
                'quantity' => $quantity,
                'price' => $price
            ];
        }

        // update order in session
        session()->set('order', $order);
    }
}
