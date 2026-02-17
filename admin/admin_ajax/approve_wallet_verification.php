<?php
/**
 * Approve Wallet Verification - Admin Endpoint
 * Admin approves a wallet after verifying the satoshi test transaction
 */

require_once '../../config.php';
require_once '../admin_session.php';

header('Content-Type: application/json');

try {
    $admin_id = $_SESSION['admin_id'];
    
    // Validate input
    if (!isset($_POST['wallet_id']) || !is_numeric($_POST['wallet_id'])) {
        throw new Exception('Invalid wallet ID');
    }
    
    $wallet_id = intval($_POST['wallet_id']);
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    
    // Get wallet details
    $stmt = $conn->prepare("SELECT id, user_id, cryptocurrency, verification_status, verification_txid
                           FROM user_payment_methods 
                           WHERE id = ? AND type = 'crypto'");
    $stmt->bind_param("i", $wallet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Wallet not found');
    }
    
    $wallet = $result->fetch_assoc();
    
    // Check if wallet is in correct status
    if ($wallet['verification_status'] !== 'verifying') {
        throw new Exception('Wallet must be in verifying status to approve');
    }
    
    if (empty($wallet['verification_txid'])) {
        throw new Exception('No transaction hash submitted for this wallet');
    }
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Update wallet status to verified
        $update_stmt = $conn->prepare("UPDATE user_payment_methods 
                                       SET verification_status = 'verified',
                                           verified_by = ?,
                                           verified_at = CURRENT_TIMESTAMP,
                                           verification_notes = ?,
                                           updated_at = CURRENT_TIMESTAMP
                                       WHERE id = ?");
        $update_stmt->bind_param("isi", $admin_id, $notes, $wallet_id);
        $update_stmt->execute();
        
        // Log admin action
        $action = "Approved wallet verification";
        $details = "Wallet ID: {$wallet_id}, Cryptocurrency: {$wallet['cryptocurrency']}, TxID: {$wallet['verification_txid']}";
        $log_stmt = $conn->prepare("INSERT INTO audit_logs (admin_id, action, entity_type, entity_id, details, ip_address) 
                                   VALUES (?, ?, 'payment_method', ?, ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->bind_param("isiss", $admin_id, $action, $wallet_id, $details, $ip);
        $log_stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Wallet verification approved successfully',
            'wallet_id' => $wallet_id,
            'status' => 'verified'
        ]);
        
        // TODO: Send notification to user (email/SMS)
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
