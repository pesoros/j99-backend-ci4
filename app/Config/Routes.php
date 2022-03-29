<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('document/ticket/thermal', 'Document::thermalTicket');
$routes->post('register', 'Register::index');
$routes->post('login', 'Login::index');
$routes->post('datakota', 'MasterData::dataKota');
$routes->post('datakelas', 'MasterData::datakelas');
$routes->post('dataunit', 'MasterData::dataUnit');
$routes->get('dataresto', 'MasterData::dataResto');
$routes->post('datarestomenu', 'MasterData::dataMenu');
$routes->post('listbus', 'Trip::tripList');
$routes->post('seatlist', 'Trip::seatList');
$routes->get('datapaymentmethod', 'XenditResource::paymentMethodList');
$routes->get('paymentmethodstatus', 'XenditResource::paymentMethodStatus');
$routes->get('xendit', 'XenditResource::index');
$routes->post('booking/add', 'Booking::storeBook');
$routes->post('callback/xendit/va/create', 'Callback::virtualAccountCreate');
$routes->post('callback/xendit/va', 'Callback::virtualAccountPay');
$routes->post('callback/xendit/retailoutlet', 'Callback::retailOutletPay');
$routes->post('callback/xendit/ewallet', 'Callback::ewalletPay');
$routes->post('paket/cek', 'Paket::cekPaket');
$routes->post('ticket/cek', 'Ticket::cekTicket');
$routes->post('account/profile', 'Account::getProfile');
$routes->post('account/profile/update', 'Account::updateProfile');
$routes->post('account/profile/historyticket', 'Account::historyTicket');
$routes->post('account/password/change', 'Account::changePassword');
$routes->post('contact/pariwisata', 'Contact::pariwisata');

$routes->group("", ["filter" => "authfilter"], function($routes){
    
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
