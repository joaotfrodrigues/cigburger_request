<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ApiModel;
use CodeIgniter\HTTP\ResponseInterface;
use getID3;

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
     * It also retrieves the total number of items in the current order and the total 
     * price of the order to be displayed on the main order page.
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

        // get order total items and total price
        $data['total_items'] = get_total_order_items();
        $data['total_price'] = get_total_order_price();

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
     * and sets the quantity to 1 if the product does not already exist in the order. It then displays the 
     * add product page with the product details, allowing the client to choose the quantity and add it to the shopping cart.
     * 
     * @param string $enc_id The encrypted ID of the product to display.
     * 
     * @return View The rendered view of the add product page.
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

        // if the product does not exists in the order, set quantity to 1
        if (empty($quantity)) {
            $quantity = 1;
        }

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
     * Removes a product from the order.
     * 
     * This function decrypts the provided encrypted product ID and validates it. If the ID is invalid,
     * it redirects to the order page. It then removes the product from the order by setting its quantity
     * to zero and updates the order in the session. Finally, it redirects to the checkout page.
     * 
     * @param string $enc_id The encrypted ID of the product to remove from the order.
     * 
     * @return RedirectResponse Redirects to the checkout page after removing the product.
     */
    public function remove_product($enc_id)
    {
        // check if id is valid
        $id = Decrypt($enc_id);
        if (empty($id)) {
            return redirect()->to('/order');
        }

        // remove product from order
        update_order($id, 0, 0.0);

        return redirect()->to('/order/checkout');
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

    /**
     * Displays the checkout page with order summary and product details.
     * 
     * This function retrieves the current order from the session and prepares the data
     * needed to display the checkout page. It calculates the total number of items and the
     * total price of the order. It also retrieves detailed information about each product
     * in the order, including quantity and total price, and checks for any promotions.
     * Finally, it renders the checkout page with the prepared data.
     * 
     * @return View The rendered view of the checkout page.
     */
    public function checkout()
    {
        // get the order
        $order = get_order();

        // prepare data to display
        $data['total_products'] = get_total_order_items();
        $data['total_price'] = get_total_order_price();

        $order_products = [];
        foreach ($order['items'] as $id => $item) {
            // get product details
            $product = $this->_get_product_by_id($id);

            // addicional product details based on the order
            $product['quantity'] = $item['quantity'];
            // total price of the order ( check for promotion )
            $this->_check_product_promotion($product);
            $product['total_price'] = $item['quantity'] * $item['price'];

            // add product to the list
            $order_products[] = $product;
        }

        $data['order_products'] = $order_products;

        // display checkout page
        return view('order/order_checkout', $data);
    }

    /**
     * Displays the checkout payment page with order details.
     * 
     * This function retrieves the current order from the session and prepares the data needed to display
     * the checkout payment page. It calculates the total number of products and the total price of the order.
     * For each product in the order, it retrieves the product details, adds additional details such as quantity
     * and total price (considering any promotions), and compiles this information into a list of order products.
     * The function then renders the checkout payment page with this data.
     * 
     * @return View The rendered view of the checkout payment page.
     */

    public function checkout_payment()
    {
        // get the order
        $order = get_order();

        // prepare data to display
        $data['total_products'] = get_total_order_items();
        $data['total_price'] = get_total_order_price();

        $order_products = [];
        foreach ($order['items'] as $id => $item) {
            // get product details
            $product = $this->_get_product_by_id($id);

            // addicional product details based on the order
            $product['quantity'] = $item['quantity'];
            // total price of the order ( check for promotion )
            $this->_check_product_promotion($product);
            $product['total_price'] = $item['quantity'] * $item['price'];

            // add product to the list
            $order_products[] = $product;
        }

        $data['order_products'] = $order_products;

        // display checkout payment page
        return view('order/order_checkout_payment', $data);
    }

    /**
     * Displays the checkout payment process page with the total order price.
     * 
     * This function retrieves the total price of the current order from the session and prepares the data
     * needed to display the checkout payment process page. It simulates the payment process by displaying
     * a page with the total order price and a fake PIN number for demonstration purposes. Additionally,
     * it checks if there was any error message in the session and includes it in the data to be displayed.
     * 
     * @return View The rendered view of the checkout payment process page.
     */
    public function checkout_payment_process()
    {
        // get total order price
        $data['total_price'] = get_total_order_price();

        // fake pin number
        $data['pin_number'] = rand(1000, 9999);

        // check if there was an error
        $data['error'] = session()->getFlashdata('error');

        // display checkout payment page, checkout simulation
        return view('order/checkout_payment_process', $data);
    }

    /**
     * Confirms the payment during the checkout process.
     * 
     * This function handles the final confirmation of the payment during the checkout process. It validates the PIN entered by the user, 
     * prepares the necessary data for the API request, and processes the payment through the CigBurger API. If the payment is successful, 
     * it generates the order number and series, updates the order session, and displays the order receipt.
     * 
     * @return RedirectResponse The redirect response with error message if validation or API request fails.
     */
    public function checkout_payment_confirm()
    {
        // get pin value
        $pin_value = Decrypt($this->request->getPost('pin_value'));

        // validate pin value
        if (empty($pin_value)) {
            return redirect()->back()->with('error', 'Aconteceu um erro com o PIN. Tente novamente');
        }

        // validate pin number
        $pin_number = $this->request->getPost('pin_number');
        if (empty($pin_number) || !preg_match('/^\d{4}$/', $pin_number) || $pin_number != $pin_value) {
            return redirect()->back()->with('error', 'O PIN introduzido não é válido.');
        }

        // prepare data to send the request to the CigBurger API
        $data = [
            'restaurant_id' => session()->get('restaurant_details')['project_id'],
            'order' => get_order(),
            'machine_id' => session()->get('machine_id')
        ];

        // set order status as paid
        $data['order']['status'] = 'paid';

        // send request to the API
        $api = new ApiModel();
        $response = $api->request_checkout($data);

        // check if there was an error | products out of stock | products unavailable
        if ($response['status'] === 400) {
            return redirect()->back()->with('error', 'O seu pedido não pode ser processado. Por favor, digija-se ao balcão.');
        }

        // add additional data to the order
        $data['id_restaurant'] = session()->get('restaurant_details')['id'];

        // calculate total price
        $data['total_price'] = get_total_order_price();

        // everything is ok, send the order to the api
        $response = $api->request_final_confirmation($data);

        // check if there was an error
        if ($response['status'] === 400) {
            return redirect()->back()->with('error', 'O seu pedido não pode ser processado. Por favor, digija-se ao balcão.');
        }

        // get the order number and calculate the order number and series
        $new_order_number = $response['data']['order_number'];
        
        $id_order = $response['data']['id_order'];

        $order_id_and_series = define_order_number_from_last_order_number($new_order_number);

        $order_number = $order_id_and_series['order_number'];
        $order_series = $order_id_and_series['order_series'];

        // add order number and series to the order in session
        update_order_with_order_and_series_number($id_order, $order_number, $order_series);

        $this->_show_order_receipt();
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
            if ($product['id'] == $id) {
                return $product;
            }
        }

        return null;
    }

    /**
     * Displays the order receipt with detailed order information.
     * 
     * This function retrieves the current order from the session and prepares the data needed to display the order receipt.
     * It collects product details, calculates total prices, and includes additional order information such as order ID,
     * order number, and order series. It also includes restaurant details. Finally, it renders the order receipt view with
     * the prepared data.
     * 
     * @return void
     */
    private function _show_order_receipt()
    {
        // get the order
        $order = get_order();

        // prepare data to display
        $data['total_products'] = get_total_order_items();
        $data['total_price'] = get_total_order_price();

        $order_products = [];
        foreach ($order['items'] as $id => $item) {
            // get product details
            $product = $this->_get_product_by_id($id);

            // addicional product details based on the order
            $product['quantity'] = $item['quantity'];
            // total price of the order ( check for promotion )
            $this->_check_product_promotion($product);
            $product['total_price'] = $item['quantity'] * $item['price'];

            // add product to the list
            $order_products[] = $product;
        }

        $data['order_products'] = $order_products;

        // get order id, number and series
        $data['order_id'] = $order['order_id'];
        $data['order_number'] = $order['order_number'];
        $data['order_series'] = $order['order_series'];

        // add restaurant details
        $data['restaurant_details'] = session()->get('restaurant_details');

        // show order receipt
        echo view('order/order_receipt', $data);
    }
}
