<?php
/**
 * Settings API
 */

require_once __DIR__ . '/../src/Database.php';

header('Content-Type: application/json');

$sede = $_GET['sede'] ?? 'talca';

try {
    $db = Database::getInstance();
    
    $sql = "SELECT * FROM global_settings WHERE sede = :sede AND id = 1";
    $settings = $db->fetchOne($sql, ['sede' => $sede]);
    
    echo json_encode($settings);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}