<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ApiModel;
use CodeIgniter\HTTP\ResponseInterface;

class Main extends BaseController
{
    /**
     * Handles the initial setup and display of the new order page.
     * 
     * This function resets any previous order by calling `init_order`, updates session 
     * information about the restaurant if it's not already set, and then displays the 
     * new order page.
     * 
     * @return void
     */
    public function index()
    {
        // reset any previous order and set a new one
        init_order();

        // update session information about the restaurant
        if (empty(session()->getFlashdata('system_initiated'))) {
            $this->_init_system();
        }

        // display new order page
        echo view('main');
    }

    /**
     * Initializes the system by loading restaurant details and setting a session flag.
     * 
     * This function calls `_init_system` to load restaurant details, sets a flag in the 
     * session indicating that the system was initiated, and then displays a success page 
     * if everything is correct.
     * 
     * @return void
     */
    public function init()
    {
        // get restaurant details
        $this->_init_system();

        // sets a flag in session, indicating that the system was initiated
        session()->setFlashdata('system_initiated', true);

        // if everything is ok, show success page
        $this->_init_success();
    }

    /**
     * Displays an error message in the view and terminates the application.
     * 
     * This function is responsible for displaying error messages in the view and
     * terminating the application's execution. If a specific error message is provided
     * as an argument, it will be displayed. Otherwise, it attempts to retrieve an error
     * message from the session flash data. If no error message is found, a generic
     * "System error" message is displayed, prompting the user to contact support.
     * 
     * @param string|null $message An optional error message to be displayed. If not provided,
     *                              the function attempts to retrieve an error message from the
     *                              session flash data.
     * 
     * @return View The view containing the error message.
     */
    public function init_error($message = null)
    {
        if (empty($message)) {
            $message = session()->getFlashdata('error');
        }

        if (empty($message)) {
            die('System error: Please contact the support');
        }

        echo view('errors/init_error', [
            'error' => $message
        ]);
        die;
    }

    /**
     * Stops the application by destroying the session and displaying an error message.
     * 
     * This function terminates the application's execution by destroying the session,
     * effectively resetting the application's configuration. It then displays an error
     * message indicating that the application configuration has been reset.
     * 
     * @return View The view containing the error message.
     */
    public function stop()
    {
        session()->destroy();
        $this->init_error('Application configuration has been reseted');
    }

    // -----------------------------------------------------------------------------------------------------------------
    // PRIVATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Initialize the application by loading configuration settings, validating them,
     * and preparing the application for use.
     * 
     * This function loads configuration settings from a JSON file, validates
     * those settings to ensure they are complete and in the correct format, and then
     * prepares the application for use based on the configuration. If any errors
     * occur during the initialization process, an exception is thrown.
     * 
     * @throws Exception If the config file does not exist, has invalid/missing variables,
     *                   or if there are other errors during initialization.
     * 
     * @return void
     * 
     */
    private function _init_system()
    {
        try {
            // check if config file exists
            if (!file_exists(ROOTPATH . 'config.json')) {
                $this->init_error('Config file not found');
            }

            // load config file
            $config = json_decode(file_get_contents(ROOTPATH . 'config.json'), true);

            if (empty($config)) {
                $this->init_error('There was an error loading the config file');
            }

            // check if config file is valid
            if (!key_exists('api_url', $config)) {
                $this->init_error('Config file is not valid: api_url is missing');
            }

            if (!key_exists('project_id', $config)) {
                $this->init_error('Config file is not valid: project_id is missing');
            }

            if (!key_exists('api_key', $config)) {
                $this->init_error('Config file is not valid: api_key is missing');
            }

            // check if api url is valid
            if (!filter_var($config['api_url'], FILTER_VALIDATE_URL)) {
                $this->init_error('Config file is not valid: api_url is not a valid url');
            }

            // check if project id is valid
            if (!is_numeric($config['project_id'])) {
                $this->init_error('Config file is not valid: project_id is not a valid number');
            }

            // check if api key is valid
            if (!preg_match('/^[a-zA-Z0-9]{32}$/', $config['api_key'])) {
                $this->init_error('Config file is not valid: api_key is not a valid key');
            }
        } catch (\Exception $e) {
            $this->init_error('The was an error loading the config file');
        }

        // if everything is ok, set config variables in session
        session()->set($config);

        // get restaurant details
        $this->_get_restaurant_details();
    }

    /**
     * Retrieves restaurant details from the CigBurger API and sets them in the session.
     * 
     * This function loads initial data by fetching restaurant details from theCigBurger
     * API using an instance of the ApiModel class. It then checks the retrieved data
     * to ensure it is valid. If the data is invalid, it displays an error message
     * and terminates the application. Otherwise, it sets the retrieved data in the
     * session for later use.
     * 
     * @return void
     */
    private function _get_restaurant_details()
    {
        // load initial data
        $api = new ApiModel();
        $data = $api->get_restaurant_details();

        if (!$this->_check_data($data)) {
            $this->init_error('System error: Please contact the support');
        }

        // set initial data in session
        $restaurant_data = [
            'restaurant_details' => $data['data']['restaurant_details'],
            'products_categories' => $data['data']['products_categories'],
            'products' => $data['data']['products'],
        ];
        session()->set($restaurant_data);
    }

    /**
     * Checks the validity of retrieved data from an API.
     * 
     * This function verifies whether the retrieved data from an API is valid. It checks
     * if the data is not empty, is an array, contains required keys ('status' and 'message'),
     * and if the 'status' is 200 and the 'message' is 'success'. If any of these conditions
     * are not met, the function returns false indicating invalid data; otherwise, it returns true.
     * 
     * @param array $data The data retrieved from the API.
     * 
     * @return bool True if the data is valid; otherwise, false.
     */
    private function _check_data($data)
    {
        if (
            empty($data)
            || !is_array($data)
            || !key_exists('status', $data)
            || !key_exists('message', $data)
            || $data['status'] !== 200
            || $data['message'] !== 'success'
        ) {
            return false;
        }
        return true;
    }

    /**
     * Initializes the touch screen interface with product information.
     * 
     * This method retrieves product information, including categories and available stock, 
     * to initialize the touch screen interface for users to make purchases at the restaurant.
     * It prepares the necessary data and renders the view for the touch screen interface.
     * 
     * @return View The view for the touch screen interface with product information.
     */
    private function _init_success()
    {
        // prepare data
        $data = [
            'initiated_at' => date('Y-m-d H:i:s'),
            'restaurant_id' => session()->get('restaurant_details')['project_id'],
            'restaurant_name' => session()->get('restaurant_details')['name'],
            'categories' => $this->_get_restaurant_categories(),
            'products' => $this->_get_restaurant_products_and_stock()
        ];

        echo view('init_success', $data);
        die;
    }

    /**
     * Retrieves restaurant categories from session data.
     * 
     * This method extracts restaurant categories from session data and returns them as an array.
     * 
     * @return array The array of restaurant categories.
     */
    private function _get_restaurant_categories()
    {
        $categories = [];
        foreach (session()->get('products_categories') as $category) {
            $categories[] = $category['category'];
        }

        return $categories;
    }

    /**
     * Retrieves product information with available stock from session data.
     * 
     * This method extracts product information including product image, name, and available stock 
     * from session data and returns it as an array.
     * 
     * @return array The array of product information with available stock.
     */
    private function _get_restaurant_products_and_stock()
    {
        $products = [];
        foreach (session()->get('products') as $product) {
            $products[] = [
                'image' => $product['image'],
                'product' => $product['name'],
                'stock' => $product['stock']
            ];
        }

        return $products;
    }
}
