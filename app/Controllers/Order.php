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
     * @return RedirectResponse Redirects to the order page.
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
     * Displays the add product view with product details for the client to choose quantity and see more information.
     * 
     * This function decrypts the provided encrypted product ID and validates it. If the ID is invalid,
     * it redirects to the order page. It then retrieves the product by its ID and checks if the product
     * exists. If the product does not exist, it redirects to the order page. If the product exists, it
     * checks for any promotions on the product, retrieves the quantity of the product in the current order,
     * and then displays the add product page with the product details, allowing the client to choose the quantity
     * and add it to the shopping cart.
     * 
     * @param string $enc_id The encrypted ID of the product to display.
     * 
     * @return View The rendered view of the add product
     */
    public function add_product($enc_id)
    {
        // check if product id is valid
        $id = Decrypt($enc_id);
        if (empty($id)) {
            return redirect()->to('/order');
        }

        // get product by id
        $product = $this->_get_product_by_id($id);

        if (empty($product)) {
            return redirect()->to('/order');
        }

        // check if product have a promotion
        $this->_check_product_promotion($product);

        // check if product is already in the order, and get the quantity
        $quantity = get_order_product_quantity($id);

        // display add product view
        return view('order/add_product', [
            'product' => $product,
            'quantity' => $quantity
        ]);
    }

    /**
     * Confirms and adds a product to the order with the specified quantity.
     * 
     * This function decrypts the provided encrypted product ID and validates it. If the ID is invalid,
     * it redirects to the order page. It also validates the quantity to ensure it falls within the allowed range.
     * It then retrieves the product details by its ID, checks for any promotions on the product, and updates
     * the order with the specified quantity and product price. Finally, it redirects back to the order page.
     * 
     * @param string $enc_id The encrypted ID of the product to add to the order.
     * @param int $quantity The quantity of the product to add to the order.
     * 
     * @return RedirectResponse Redirects to the order page after adding the product to the order.
     */
    public function add_product_confirm($enc_id, $quantity)
    {
        // check if id and quantity are valid
        $id = Decrypt($enc_id);
        if (empty($id)) {
            return redirect()->to('/order');
        }

        // check if quantity is valid
        if ($quantity < 0 || $quantity > MAX_QUANTITY_PER_PRODUCT) {
            return redirect()->to('/order');
        }

        // get the price of the product
        $product = $this->_get_product_by_id($id);
        $this->_check_product_promotion($product);

        // update the order
        update_order($id, $quantity, $product['price']);

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
    private function _set_products_info($products)
    {
        $valid_products = [];
        foreach ($products as $index => $product) {
            // is product available
            if ($product['availability'] === 0 || !empty($product['deleted_at'])) {
                continue;
            }

            // promotion ?
            $this->_check_product_promotion($product);

            // stock availability
            $this->_check_product_availability($product);

            $valid_products[] = $product;
        }

        return $valid_products;
    }

    /**
     * Checks and applies promotion details to a product.
     * 
     * This function checks if the given product has a promotion. If a promotion exists,
     * it sets the `has_promotion` flag to true, calculates and sets the discounted price,
     * and records the old price. If no promotion exists, it sets the `has_promotion` flag
     * to false and the `old_price` to 0.0.
     * 
     * @param array &$product The product array to check and modify for promotion details.
     * 
     * @return void
     */
    private function _check_product_promotion(&$product)
    {
        if ($product['promotion'] > 0) {
            $product['has_promotion'] = true;
            $product['old_price'] = $product['price'];
            $product['price'] = $product['price'] - ($product['price'] * $product['promotion'] / 100);
        } else {
            $product['has_promotion'] = false;
            $product['old_price'] = 0.0;
        }
    }

    /**
     * Checks and updates the availability status of a product based on its stock levels.
     * 
     * This function examines the stock level of the given product and updates its availability status.
     * If the stock is less than or equal to the minimum stock limit, it sets the `out_of_stock` flag to true.
     * Otherwise, it sets the `out_of_stock` flag to false.
     * 
     * @param array &$product The product array to check and modify for availability status.
     * 
     * @return void
     */
    private function _check_product_availability(&$product)
    {
        if ($product['stock'] <= $product['stock_min_limit']) {
            $product['out_of_stock'] = true;
        } else {
            $product['out_of_stock'] = false;
        }
    }

    /**
     * Retrieves a product by its ID.
     * 
     * This function fetches all products from the session and searches for a product with the specified ID.
     * If a product with the matching ID is found, it returns that product. If no matching product is found,
     * it returns null.
     * 
     * @param int $id The ID of the product to retrieve.
     * 
     * @return array|null The product with the specified ID, or null if not found.
     */
    private function _get_product_by_id($id)
    {
        // get all products
        $products = session()->get('products');

        // get product by id
        foreach ($products as $product) {
            if ($product['id'] === $id) {
                return $product;
            }
        }

        return null;
    }
}
