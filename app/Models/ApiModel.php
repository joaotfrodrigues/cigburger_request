<?php

namespace App\Models;

use CodeIgniter\Model;

class ApiModel extends Model
{
    private $api_url;
    private $project_id;
    private $api_key;

    public function __construct()
    {
        // load config data from session
        $this->api_url = session()->get('api_url');
        $this->project_id = session()->get('project_id');
        $this->api_key = session()->get('api_key');
    }

    private function api($endpoint, $method = 'GET', $data = [])
    {
        $curl = curl_init();

        // set the complete url for the api request
        $endpoint = $this->api_url . $endpoint;

        curl_setopt_array($curl, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-API-CREDENTIALS: " . $this->_set_credentials()
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }

    private function _set_credentials()
    {
        $data = json_encode([
            'project_id' => $this->project_id,
            'api_key' => $this->api_key
        ]);

        $encrypter = \Config\Services::encrypter();

        return bin2hex($encrypter->encrypt($data));
    }

    public function get_status()
    {
        return $this->api('get_status');
    }
}