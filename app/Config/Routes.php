<?php

use App\Controllers\DashboardController;
use App\Controllers\AppController;
use App\Controllers\DashboardV2Controller;
use App\Controllers\DetailChecksheetController;
use App\Controllers\MasterController;
use App\Controllers\UserController;
use App\Controllers\ApiController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Dashboard
$routes->group('', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->get('/dashboard-v2', 'DashboardV2Controller::index');
    $routes->get('/dashboard-v3', 'DashboardV3Controller::index');
});
// $routes->get('/dashboard/ng-details', [DashboardController::class, 'getNGDetails']);

// Checksheet Routes Group
$routes->group('checksheet', function ($routes) {
    $routes->get('/', [AppController::class, 'checksheet']);
    $routes->get('table/(:num)', 'AppController::detail/$1');
    $routes->get('create', [AppController::class, 'checksheetCreate']);
    $routes->post('store', [AppController::class, 'store']);
    $routes->delete('delete/(:num)', 'AppController::destroy/$1');
    $routes->get('edit/(:num)', 'AppController::edit/$1');
    $routes->post('update/(:num)', 'AppController::update/$1');
    $routes->post('save-status', [DetailChecksheetController::class, 'saveStatus']);
    $routes->post('detail-checksheet/update-ng-to-ok', [DetailChecksheetController::class, 'updateNGtoOK']);
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

// Open Ticket Routes
$routes->group('open-ticket', function ($routes) {
    $routes->get('/', [DetailChecksheetController::class, 'ngList']);
    $routes->get('change-status/(:num)', [DetailChecksheetController::class, 'changeStatusForm/$1']);
    $routes->get('change-log/(:num)', [DetailChecksheetController::class, 'detailChangeLog/$1']);
    $routes->post('update-status/(:num)', [DetailChecksheetController::class, 'updateStatus/$1']);
});