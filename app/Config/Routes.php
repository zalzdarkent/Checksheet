<?php

use App\Controllers\AppController;
use App\Controllers\DetailChecksheetController;
use App\Controllers\MasterController;
use App\Controllers\UserController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Dashboard
$routes->get('/', [AppController::class, 'dashboard']);

// Checksheet Routes Group
$routes->group('checksheet', function ($routes) {
    $routes->get('/', 'AppController::checksheet');
    $routes->get('table/(:num)', 'AppController::detail/$1');
    $routes->get('create', 'AppController::checksheetCreate');
    $routes->post('store', 'AppController::store');
    $routes->delete('delete/(:num)', 'AppController::destroy/$1');
    $routes->get('edit/(:num)', 'AppController::edit/$1');
    $routes->post('update/(:num)', 'AppController::update/$1');
    $routes->post('save-status', [DetailChecksheetController::class, 'saveStatus']);
    $routes->post('detail-checksheet/update-ng-to-ok', 'DetailChecksheetController::updateNGtoOK');
});

// Master Checksheet Routes Group
$routes->group('master', function ($routes) {
    $routes->get('/', [MasterController::class, 'index']);
    $routes->get('create', [MasterController::class, 'create']);
    $routes->post('store', [MasterController::class, 'store']);
    $routes->get('edit/(:num)', 'MasterController::edit/$1');
    $routes->post('update/(:num)', 'MasterController::update/$1');
    $routes->get('delete/(:num)', 'MasterController::delete/$1');
});