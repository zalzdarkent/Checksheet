<?php

use App\Controllers\ChecksheetController;
use App\Controllers\DashboardController;
use App\Controllers\DashboardV2Controller;
use App\Controllers\DetailChecksheetController;
use App\Controllers\MasterController;
use App\Controllers\DashboardV3Controller;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Dashboard
$routes->group('', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', [DashboardController::class, 'index']);
    $routes->get('/dashboard-v2', [DashboardV2Controller::class, 'index']);
    $routes->get('/dashboard-v3', [DashboardV3Controller::class, 'index']);
    $routes->get('/dashboard-v3/ng-details', [DashboardV3Controller::class, 'getNGDetails']);
});

// Checksheet Routes Group
$routes->group('checksheet', function ($routes) {
    $routes->get('/', [ChecksheetController::class, 'checksheet']);
    $routes->get('table/(:num)', [ChecksheetController::class, 'detail/$1']);
    $routes->get('create', [ChecksheetController::class, 'checksheetCreate']);
    $routes->post('store', [ChecksheetController::class, 'store']);
    $routes->delete('delete/(:num)', [ChecksheetController::class, 'destroy/$1']);
    $routes->get('edit/(:num)', [ChecksheetController::class, 'edit/$1']);
    $routes->post('update/(:num)', [ChecksheetController::class, 'update/$1']);
    $routes->post('save-status', [DetailChecksheetController::class, 'saveStatus']);
});

// Master Checksheet Routes Group
$routes->group('master', function ($routes) {
    $routes->get('/', [MasterController::class, 'index']);
    $routes->get('create', [MasterController::class, 'create']);
    $routes->post('store', [MasterController::class, 'store']);
    $routes->get('edit/(:num)', [MasterController::class, 'edit/$1']);
    $routes->post('update/(:num)', [MasterController::class, 'update/$1']);
    $routes->get('delete/(:num)', [MasterController::class, 'delete/$1']);
});

// Open Ticket Routes
$routes->group('open-ticket', function ($routes) {
    $routes->get('/', [DetailChecksheetController::class, 'ngList']);
    $routes->get('change-status/(:num)', [DetailChecksheetController::class, 'changeStatusForm/$1']);
    $routes->get('change-log/(:num)', [DetailChecksheetController::class, 'detailChangeLog/$1']);
    $routes->post('update-status/(:num)', [DetailChecksheetController::class, 'updateStatus/$1']);
});