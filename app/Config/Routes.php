<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/',                                              'Main::index');
$routes->get('/init',                                          'Main::init');
$routes->get('/init_error',                                    'Main::init_error');
$routes->get('/stop',                                          'Main::stop');

// order routes
$routes->get('/order',                                         'Order::index');
$routes->get('/order/set_filter/(:alphanum)',                  'Order::set_filter/$1');
$routes->get('/order/add_product/(:alphanum)',                 'Order::add_product/$1');
$routes->get('/order/add_product_confirm/(:alphanum)/(:num)',  'Order::add_product_confirm/$1/$2');
$routes->get('/order/cancel',                                  'Order::cancel');
$routes->get('/order/checkout',                                'Order::checkout');