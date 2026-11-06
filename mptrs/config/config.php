<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('BASE_URL', 'http://localhost/mptrs/public');
define('APP_NAME', 'MPTRS - Hospital Maintenance');
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/');
define('TIMEZONE', 'Africa/Nairobi');
date_default_timezone_set(TIMEZONE);
session_start();