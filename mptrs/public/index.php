<?php
require dirname(__DIR__) . '/config/config.php';

// Autoload core + app classes
spl_autoload_register(function ($class) {
    $paths = [
        dirname(__DIR__) . '/app/core/',
        dirname(__DIR__) . '/app/models/',
        dirname(__DIR__) . '/app/controllers/',
    ];
    foreach ($paths as $p) {
        if (file_exists($p . $class . '.php')) { require $p . $class . '.php'; return; }
    }
});

// Force PDO global availability
require dirname(__DIR__) . '/app/core/Database.php';
require dirname(__DIR__) . '/app/core/Model.php';
require dirname(__DIR__) . '/app/core/Session.php';
require dirname(__DIR__) . '/app/core/Auth.php';
require dirname(__DIR__) . '/app/core/Audit.php';
require dirname(__DIR__) . '/app/core/Uploader.php';
require dirname(__DIR__) . '/app/core/Validator.php';
require dirname(__DIR__) . '/app/core/Controller.php';

$route = $_GET['route'] ?? 'dashboard';

// Basic session timeout (30 min inactivity)
if (Auth::check()) {
    $last = Session::get('last_activity', time());
    if (time() - $last > 1800) {
        Auth::logout();
        header('Location: ' . BASE_URL . '/index.php?route=login');
        exit;
    }
    Session::set('last_activity', time());
}

// Route map
$routes = [
    // Auth
    'login'             => ['AuthController','loginForm', false],
    'login-post'        => ['AuthController','login', false],
    'logout'            => ['AuthController','logout', false],
    'forgot-password'   => ['AuthController','forgotForm', false],
    'reset-password'    => ['AuthController','resetForm', false],
    // Dashboard
    'dashboard'         => ['DashboardController','index', true],
    // Facilities
    'facilities'        => ['FacilityController','index', true],
    'facilities-create' => ['FacilityController','createForm', true],
    'facilities-edit'   => ['FacilityController','editForm', true],
    'facilities-save'   => ['FacilityController','save', true],
    'facilities-delete' => ['FacilityController','delete', true],
    // Buildings
    'buildings'         => ['BuildingController','index', true],
    'buildings-form'    => ['BuildingController','form', true],
    'buildings-save'    => ['BuildingController','save', true],
    'buildings-delete'  => ['BuildingController','delete', true],
    // Assets
    'assets'            => ['AssetController','index', true],
    'assets-form'       => ['AssetController','form', true],
    'assets-save'       => ['AssetController','save', true],
    'assets-delete'     => ['AssetController','delete', true],
    // Requests
    'requests'          => ['RequestController','index', true],
    'requests-create'   => ['RequestController','createForm', true],
    'requests-store'    => ['RequestController','store', true],
    'requests-view'     => ['RequestController','viewRequest', true],
    'requests-approve'  => ['RequestController','approve', true],
    'requests-reject'   => ['RequestController','reject', true],
    'requests-create-wo'=> ['RequestController','createWorkOrder', true],
    // Work Orders
    'workorders'        => ['WorkOrderController','index', true],
    'workorders-view'   => ['WorkOrderController','viewWO', true],
    'workorders-assign' => ['WorkOrderController','assign', true],
    'workorders-status' => ['WorkOrderController','updateStatus', true],
    'workorders-upload' => ['WorkOrderController','uploadPhoto', true],
    // Technicians
    'technicians'       => ['TechnicianController','index', true],
    // Costs
    'costs'             => ['CostController','index', true],
    'costs-store'       => ['CostController','store', true],
    // Reports
    'reports'           => ['ReportController','index', true],
    // Users
    'users'             => ['UserController','index', true],
    'users-form'        => ['UserController','form', true],
    'users-save'        => ['UserController','save', true],
    'users-delete'      => ['UserController','delete', true],
        // Notifications
    'notifications'          => ['NotificationController','index', true],
    'notifications-read'     => ['NotificationController','markRead', true],
    'notifications-read-all' => ['NotificationController','markAllRead', true],
    
    // Reports Export
    'reports-export-csv'     => ['ReportController','exportCsv', true],
    'reports-print-pdf'      => ['ReportController','printPdf', true],
];

if (!isset($routes[$route])) { http_response_code(404); die('404 Not Found'); }

[$controller, $method, $requiresAuth] = $routes[$route];
if ($requiresAuth) Auth::requireLogin();

// Handle POST for login manually (map login route to POST)
if ($route === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new AuthController())->login();
    exit;
}
if ($route === 'forgot-password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new AuthController())->forgot();
    exit;
}
if ($route === 'reset-password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new AuthController())->reset();
    exit;
}

(new $controller())->$method();