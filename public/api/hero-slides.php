<?php
/**
 * Hero Slides API
 */

require_once __DIR__ . '/../src/Database.php';

header('Content-Type: application/json');

// Get sede from query param
$sede = $_GET['sede'] ?? 'talca';

try {
    $db = Database::getInstance();
    
    $sql = "SELECT * FROM hero_slides WHERE sede = :sede ORDER BY order_index ASC, id ASC";
    $slides = $db->fetchAll($sql, ['sede' => $sede]);
    
    echo json_encode($slides);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}