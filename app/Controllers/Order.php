<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Order extends BaseController
{
    /**
     * Displays the main order page with product categories and products.
     * 
     * This function prepares the data required to display the main order page, 
     * including product categories and products filtered by the selected category. 
     * It retrieves product categories from the session, adds an "All Products" category, 
     * gets the products for the selected category, calculates additional product 
     * information (such as discounts), and then renders the order page with this data.
     * 
     * @return View The rendered view of the main order page.
     */
    public function index()
    {
        // prepare data
        $data = [];

        // categories
        $data['categories'] = session()->get('products_categories');

        // add "All Products" category
        array_unshift($data['categories'], ['category' => 'Todos']);

        // gets the products by category
        $selected_category = session()->get('selected_category');
        if (empty($selected_category)) {
            $selected_category = 'Todos';
        }

        $data['selected_category'] = $selected_category;

        $products = $this->_get_products_by_category($selected_category);

        // calculate product discount, state, etc...
        $data['products'] = $this->_set_products_info($products);

        return view('order/main_page', $data);
    }

    /**
     * Sets the selected product category filter and redirects to the order page.
     * 
     * This function decrypts the provided category parameter and checks its validity. 
     * If the category is invalid or empty, it redirects to the order page. If valid, 
     * it sets the selected category in the session and then redirects to the order page.
     * 
     * @param string|null $category The encrypted category name.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirects to the order page.
     */
    public function set_filter($category = null)
    {
        // check if $category is encrypted correctly
        if (empty(Decrypt($category))) {
            return redirect()->to('/order');
        }

        // set selected category
        session()->set('selected_category', Decrypt($category));

        return redirect()->to('/order');
    }

    /**
     * Handles the cancellation process by checking the current order and displaying a confirmation view.
     * 
     * This function checks if there is an existing order with at least one item. If no items are found 
     * in the order, it redirects to the home page. Otherwise, it displays a cancellation confirmation page.
     * 
     * @return View The rendered view of the cancellation confirmation page.
     */
    public function cancel()
    {
        // check if there is an order with, at least, one item
        $order = get_order();
        if (empty($order['items'])) {
            return redirect()->to('/');
        }

        // show cancel confirmation page
        return view('order/cancel_confirmation', [
            'total_items' => get_total_order_items()
        ]);
    }

    public function checkout()
    {
        // show checkout page
        dd(get_order());
    }

    // -----------------------------------------------------------------------------------------------------------------
    // PRIVATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Retrieves products filtered by the specified category.
     * 
     * This function fetches all products from the session and filters them based on 
     * the provided category. If the category is 'Todos', it returns all products. 
     * Otherwise, it returns only the products that match the specified category.
     * 
     * @param string $category The category to filter products by.
     * 
     * @return array The list of products filtered by the specified category.
     */
    private function _get_products_by_category($category)
    {
        // get all products
        $products = session()->get('products');
        $products_by_category = [];

        // all products
        if ($category === 'Todos') {
            $products_by_category = $products;
        } else {
            // products by specific category
            foreach ($products as $product) {
                if ($product['category'] === $category) {
                    $products_by_category[] = $product;
                }
            }
        }

        return $products_by_category;
    }

    /**
     * Processes and enhances product information with additional details.
     * 
     * This function iterates over a list of products, checks their availability,
     * applies promotions if applicable, and determines stock availability. It
     * enriches each product with additional information such as promotion status,
     * old price, and stock status. Only valid and available products are included
     * in the final list.
     * 
     * @param array $products The list of products to process.
     * 
     * @return array The list of processed and valid products.
     */
    public function _set_products_info($products)
    {
        $valid_products = [];
        foreach ($products as $index => $product) {
            // is product available
            if ($product['availability'] === 0 || !empty($product['deleted_at'])) {
                continue;
            }

            // promotion
            if ($product['promotion'] > 0) {
                $product['has_promotion'] = true;
                $product['old_price'] = $product['price'];
                $product['price'] = $product['price'] - ($product['price'] * $product['promotion'] / 100);
            } else {
                $product['has_promotion'] = false;
                $product['old_price'] = 0.0;
            }

            // stock availability
            if ($product['stock'] <= $product['stock_min_limit']) {
                $product['out_of_stock'] = true;
            } else {
                $product['out_of_stock'] = false;
            }

            $valid_products[] = $product;
        }

        return $valid_products;
    }
}
