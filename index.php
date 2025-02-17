<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', __DIR__);

// Load required files
require_once BASE_PATH . '/includes/Database.php';
require_once BASE_PATH . '/includes/AristaApi.php';
require_once BASE_PATH . '/models/NetworkSwitch.php';

// Start output buffering
ob_start();

// Include header
require 'templates/header.php';

// Include sidebar
require 'templates/sidebar.php';

// Routing logic based on URL parameters
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$page_file = BASE_PATH . '/pages/' . $page . '.php';

if (file_exists($page_file)) {
    require $page_file;
} else {
    require BASE_PATH . '/pages/dashboard.php';
}

// Include footer
require 'templates/footer.php';

// End output buffering and flush
ob_end_flush();
?>
