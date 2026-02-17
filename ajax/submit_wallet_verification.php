<?php
/**
 * Submit Wallet Verification - User Endpoint
 * User submits transaction hash after making satoshi test deposit
 */

session_start();
require_once '../config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $user_id = $_SESSION['user_id'];
    
    // Validate input
    if (!isset($_POST['wallet_id']) || !is_numeric($_POST['wallet_id'])) {
        throw new Exception('Invalid wallet ID');
    }
    
    if (!isset($_POST['verification_txid']) || empty(trim($_POST['verification_txid']))) {
        throw new Exception('Transaction hash is required');
    }
    
    $wallet_id = intval($_POST['wallet_id']);
    $verification_txid = trim($_POST['verification_txid']);
    
    // Validate transaction hash format (typically 64 hexadecimal characters)
    if (!preg_match('/^[a-fA-F0-9]{64}$/', $verification_txid)) {
        throw new Exception('Invalid transaction hash format. Must be 64 hexadecimal characters.');
    }
    
    // Verify wallet ownership
    $stmt = $conn->prepare("SELECT id, cryptocurrency, verification_status, verification_amount, verification_address 
                           FROM user_payment_methods 
                           WHERE id = ? AND user_id = ? AND type = 'crypto'");
    $stmt->bind_param("ii", $wallet_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Wallet not found or access denied');
    }
    
    $wallet = $result->fetch_assoc();
    
    // Check if verification details are set
    if (empty($wallet['verification_amount']) || empty($wallet['verification_address'])) {
        throw new Exception('Verification details not set by admin yet. Please wait.');
    }
    
    // Check current status
    if ($wallet['verification_status'] === 'verified') {
        throw new Exception('This wallet is already verified');
    }
    
    if ($wallet['verification_status'] === 'verifying') {
        throw new Exception('Verification already submitted. Awaiting admin approval.');
    }
    
    // Update wallet with transaction hash and set status to verifying
    $update_stmt = $conn->prepare("UPDATE user_payment_methods 
                                   SET verification_txid = ?,
                                       verification_status = 'verifying',
                                       verification_requested_at = CURRENT_TIMESTAMP,
                                       updated_at = CURRENT_TIMESTAMP
                                   WHERE id = ? AND user_id = ?");
    $update_stmt->bind_param("sii", $verification_txid, $wallet_id, $user_id);
    
    if ($update_stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Transaction hash submitted successfully! Your wallet is now awaiting verification.',
            'status' => 'verifying'
        ]);
    } else {
        throw new Exception('Failed to update wallet verification status');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
