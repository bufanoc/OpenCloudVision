<?php
require_once __DIR__ . '/../includes/AristaApi.php';
require_once __DIR__ . '/../models/NetworkSwitch.php';

header('Content-Type: application/json');

function respondWithJson($data) {
    echo json_encode($data);
    exit;
}

$switchModel = new NetworkSwitch();

try {
    // Handle GET requests
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id'])) {
            // Get specific switch details
            $switch = $switchModel->getSwitchById($_GET['id']);
            respondWithJson($switch);
        } else {
            // Get all switches
            $switches = $switchModel->getAllSwitches();
            respondWithJson($switches);
        }
    }
    
    // Handle POST requests (adding new switch)
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['ip_address']) || !isset($_POST['username']) || !isset($_POST['password'])) {
            throw new Exception('Missing required fields');
        }

        $api = new AristaApi($_POST['ip_address'], $_POST['username'], $_POST['password']);
        
        // Test connection and get switch details
        $result = $api->getSwitchInfo();
        
        if (!isset($result['result'])) {
            throw new Exception('Failed to retrieve switch information');
        }
        
        // Add switch to database
        $switchId = $switchModel->addSwitch(
            $_POST['ip_address'],
            $_POST['username'],
            $_POST['password'],
            $result['result'][0]  // Version information
        );
        
        // Add interfaces to database
        $switchModel->updateInterfaces(
            $switchId,
            $result['result'][1],  // Interface status
            $result['result'][2]   // IP interfaces
        );
        
        respondWithJson(['success' => true, 'message' => 'Switch added successfully']);
    }
    
    // Handle DELETE requests (offboarding switch)
    else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        if (!isset($_GET['id'])) {
            throw new Exception('Missing switch ID');
        }
        
        $switchModel->deleteSwitch($_GET['id']);
        respondWithJson(['success' => true, 'message' => 'Switch offboarded successfully']);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    respondWithJson(['success' => false, 'message' => $e->getMessage()]);
}
