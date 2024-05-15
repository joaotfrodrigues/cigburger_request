<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/',                                 'Main::index');
$routes->get('/init',                             'Main::init');
$routes->get('/init_error',                       'Main::init_error');
$routes->get('/stop',                             'Main::stop');

// order routes
$routes->get('/order',                            'Order::index');
$routes->get('/order/set_filter/(:alphanum)',     'Order::set_filter/$1');