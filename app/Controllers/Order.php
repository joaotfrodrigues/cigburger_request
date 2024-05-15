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

        $data['products'] = $this->_get_products_by_category($selected_category);

        dd($data);

        return view('order/main_page', $data);
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
            foreach($products as $product) {
                if ($product['category'] === $category) {
                    $products_by_category[] = $product;
                }
            }
        }

        return $products_by_category;
    }
}
