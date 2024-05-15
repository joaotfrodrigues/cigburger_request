<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Order extends BaseController
{
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

    public function cancel()
    {
        // clear order
        delete_order();

        echo 'confirmar cancelamento do pedido';
    }

    public function checkout()
    {
        // show checkout page
        dd(get_order());
    }

    // -----------------------------------------------------------------------------------------------------------------
    // PRIVATE METHODS
    // -----------------------------------------------------------------------------------------------------------------
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
