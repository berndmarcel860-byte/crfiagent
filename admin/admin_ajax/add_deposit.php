<?php
require_once '../admin_session.php';

header('Content-Type: application/json');

try {
    // Get current admin role and ID
    $currentAdminRole = $_SESSION['admin_role'] ?? 'admin';
    $currentAdminId = $_SESSION['admin_id'];
    
    // Validate required fields
    if (empty($_POST['user_id']) || empty($_POST['amount']) || empty($_POST['method_code'])) {
        echo json_encode([
            'success' => false,
            'message' => 'User, amount, and payment method are required'
        ]);
        exit;
    }
    
    $userId = intval($_POST['user_id']);
    $amount = floatval($_POST['amount']);
    $methodCode = $_POST['method_code'];
    $transactionId = $_POST['transaction_id'] ?? '';
    $status = $_POST['status'] ?? 'completed';
    $adminNotes = $_POST['admin_notes'] ?? '';
    
    // Validate amount
    if ($amount <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Amount must be greater than 0'
        ]);
        exit;
    }
    
    // Verify user belongs to this admin (unless superadmin)
    if ($currentAdminRole !== 'superadmin') {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND (admin_id = ? OR admin_id IS NULL)");
        $stmt->execute([$userId, $currentAdminId]);
        
        if (!$stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'User not found or you do not have permission to add deposit for this user'
            ]);
            exit;
        }
    }
    
    // Generate unique reference
    $reference = 'DEP' . time() . rand(1000, 9999);
    
    // Insert deposit
    $stmt = $pdo->prepare("
        INSERT INTO deposits (user_id, amount, method_code, reference, status, admin_notes, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([$userId, $amount, $methodCode, $reference, $status, $adminNotes]);
    
    // If status is completed, update user balance
    if ($status === 'completed') {
        $updateStmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $updateStmt->execute([$amount, $userId]);
    }
    
    // Log admin action
    $logStmt = $pdo->prepare("
        INSERT INTO admin_logs (admin_id, action, details, created_at)
        VALUES (?, 'add_deposit', ?, NOW())
    ");
    $logDetails = json_encode([
        'user_id' => $userId,
        'amount' => $amount,
        'reference' => $reference,
        'method_code' => $methodCode,
        'status' => $status
    ]);
    $logStmt->execute([$currentAdminId, $logDetails]);
    
    // Send email notification to user
    try {
        require_once '../mail_functions.php';
        
        $userStmt = $pdo->prepare("SELECT email, first_name, last_name FROM users WHERE id = ?");
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $statusText = ucfirst($status);
            $emailContent = "
                <h2>Deposit {$statusText}</h2>
                <p>Dear {$user['first_name']} {$user['last_name']},</p>
                <p>A deposit has been recorded for your account:</p>
                <ul>
                    <li><strong>Amount:</strong> €" . number_format($amount, 2) . "</li>
                    <li><strong>Reference:</strong> {$reference}</li>
                    <li><strong>Status:</strong> {$statusText}</li>
                </ul>
            ";
            
            if ($status === 'completed') {
                $emailContent .= "<p>Your account balance has been updated.</p>";
            }
            
            sendEmail($user['email'], "Deposit {$statusText}", $emailContent);
        }
    } catch (Exception $e) {
        // Log email error but don't fail the deposit creation
        error_log('Email notification failed: ' . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Deposit created successfully',
        'reference' => $reference
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}