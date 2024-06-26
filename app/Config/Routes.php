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
$routes->get('/order/remove_product/(:alphanum)',              'Order::remove_product/$1');

$routes->get('/order/cancel',                                  'Order::cancel');
$routes->get('/order/checkout',                                'Order::checkout');
$routes->get('/order/checkout_payment',                        'Order::checkout_payment');
$routes->get('/order/checkout_payment_process',                'Order::checkout_payment_process');
$routes->post('/order/checkout_payment_confirm',                'Order::checkout_payment_confirm');