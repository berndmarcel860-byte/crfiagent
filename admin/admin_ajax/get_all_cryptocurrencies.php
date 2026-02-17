<?php
/**
 * Get All Cryptocurrencies with Networks (Admin)
 */

session_start();
require_once '../../config.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    // Fetch all cryptocurrencies (including inactive)
    $sql = "SELECT * FROM cryptocurrencies ORDER BY sort_order ASC, name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $cryptos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch networks for each cryptocurrency
    foreach ($cryptos as &$crypto) {
        $networkSql = "SELECT * FROM crypto_networks WHERE crypto_id = ? ORDER BY sort_order ASC, network_name ASC";
        $networkStmt = $pdo->prepare($networkSql);
        $networkStmt->execute([$crypto['id']]);
        $crypto['networks'] = $networkStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode([
        'success' => true,
        'cryptocurrencies' => $cryptos
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
