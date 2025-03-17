<?php

use App\Controllers\AppController;
use App\Controllers\MasterController;
use App\Controllers\UserController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Autentikasi login
// $routes->get('/login', [UserController::class, 'login']);
// $routes->post('/login-proses', [UserController::class, 'loginProcess']); // Pakai POST

// // Autentikasi register
// $routes->get('/register', 'UserController::register');
// $routes->post('/register-proses', 'UserController::registerProcess'); // Pakai POST

// // Logout di luar auth biar bisa logout kapan saja
// $routes->get('/logout', 'UserController::logout');

// Proteksi rute dengan auth filter
// $routes->group('', ['filter' => 'auth'], function ($routes) {
//     $routes->get('/', 'AppController::dashboard');
//     $routes->get('/checksheet', 'AppController::checksheet');
//     $routes->get('/checksheet/tambah', 'AppController::checksheetCreate');
// });

$routes->get('/', [AppController::class, 'dashboard']);

// list checksheet
$routes->get('/table-checksheet/(:num)/(:num)', 'AppController::detail/$1/$2');
$routes->get('/list-checksheet', 'AppController::checksheet');
$routes->get('/checksheet/tambah', 'AppController::checksheetCreate');
$routes->post('/checksheet-store', 'AppController::store');
$routes->delete('/checksheet/delete/(:num)', 'AppController::destroy/$1');
$routes->get('/checksheet/edit/(:num)', 'AppController::edit/$1');
$routes->post('/checksheet/update/(:num)', 'AppController::update/$1');

// master checksheet
$routes->get('/master-checksheet/tambah', [MasterController::class, 'create']);
$routes->get('/master-checksheet/index', [MasterController::class, 'index']);
$routes->post('/master-checksheet/store', [MasterController::class, 'store']);
$routes->get('/master/edit/(:num)', 'MasterController::edit/$1');
$routes->post('/master/update/(:num)', 'MasterController::update/$1');
$routes->get('/master/delete/(:num)', 'MasterController::delete/$1');