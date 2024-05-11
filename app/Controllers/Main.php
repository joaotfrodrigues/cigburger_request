<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Main extends BaseController
{
    // run when the machine boots
    public function init()
    {
        echo 'iniciação do sistema';
    }

    public function index()
    {
        echo 'index';
    }
}
