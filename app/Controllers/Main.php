<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ApiModel;
use CodeIgniter\HTTP\ResponseInterface;

class Main extends BaseController
{
    // run when the machine boots
    public function init()
    {
        try {
            // check if config file exists
            if (!file_exists(ROOTPATH . 'config.json')) {
                $this->_init_error('Config file not found');
            }

            // load config file
            $config = json_decode(file_get_contents(ROOTPATH . 'config.json'), true);

            if (empty($config)) {
                $this->_init_error('There was an error loading the config file');
            }

            // check if config file is valid
            if (!key_exists('api_url', $config)) {
                $this->_init_error('Config file is not valid: api_url is missing');
            }

            if (!key_exists('project_id', $config)) {
                $this->_init_error('Config file is not valid: project_id is missing');
            }

            if (!key_exists('api_key', $config)) {
                $this->_init_error('Config file is not valid: api_key is missing');
            }

            // check if api url is valid
            if (!filter_var($config['api_url'], FILTER_VALIDATE_URL)) {
                $this->_init_error('Config file is not valid: api_url is not a valid url');
            }

            // check if project id is valid
            if (!is_numeric($config['project_id'])) {
                $this->_init_error('Config file is not valid: project_id is not a valid number');
            }

            // check if api key is valid
            if (!preg_match('/^[a-zA-Z0-9]{32}$/', $config['api_key'])) {
                $this->_init_error('Config file is not valid: api_key is not a valid key');
            }
        } catch (\Exception $e) {
            $this->_init_error('The was an error loading the config file');
        }

        // if everything is ok, set config variables in session
        session()->set($config);
    }

    private function _init_error($message)
    {
        die($message);
    }

    public function index()
    {
        // teste api
        $api = new ApiModel();

        echo '<pre>';
        print_r($api->get_status());
    }
}
