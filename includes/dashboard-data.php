<?php
/**
 * Dashboard Data Fetching
 * Handles all database queries for dashboard data
 */

// Load current user if logged in
if (!empty($userId)) {
    try {
        $userStmt = $pdo->prepare("SELECT id, first_name, force_password_change, balance, last_login, is_verified FROM users WHERE id = ?");
        $userStmt->execute([$userId]);
        $currentUser = $userStmt->fetch(PDO::FETCH_ASSOC);
        if ($currentUser) {
            $passwordChangeRequired = ((int)$currentUser['force_password_change'] === 1);
            $currentUserLogin = $currentUser['first_name'] ?: 'Unknown User';
        }
    } catch (PDOException $e) {
        error_log("Database error (user fetch): " . $e->getMessage());
    }
}

if (empty($currentUserLogin)) {
    $currentUserLogin = 'Unknown User';
}

// Additional data for logged-in user
if (!empty($userId)) {
    try {
        // KYC
        $kyc = $pdo->prepare("SELECT status FROM kyc_verification_requests WHERE user_id=? ORDER BY id DESC LIMIT 1");
        $kyc->execute([$userId]);
        $kyc_status = ($row = $kyc->fetch(PDO::FETCH_ASSOC)) ? $row['status'] : 'pending';

        // Check for verified payment methods
        $stmt_verified = $pdo->prepare("SELECT COUNT(*) as count FROM user_payment_methods 
                                        WHERE user_id = ? AND type = 'crypto' 
                                        AND verification_status = 'verified'");
        $stmt_verified->execute([$userId]);
        $verified_count = $stmt_verified->fetch(PDO::FETCH_ASSOC);
        $hasVerifiedPaymentMethod = $verified_count['count'] > 0;

        // Login logs
        $loginLogsStmt = $pdo->prepare("SELECT ip_address, attempted_at, success FROM login_logs WHERE user_id=? ORDER BY attempted_at DESC LIMIT 3");
        $loginLogsStmt->execute([$userId]);
        $loginLogs = $loginLogsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Stats
        $statsStmt = $pdo->prepare("SELECT COUNT(*) as total_cases, COALESCE(SUM(reported_amount), 0) as total_reported, COALESCE(SUM(recovered_amount), 0) as total_recovered, MAX(created_at) as last_case_date FROM cases WHERE user_id = ?");
        $statsStmt->execute([$userId]);
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_cases' => 0,
            'total_reported' => 0.00,
            'total_recovered' => 0.00,
            'last_case_date' => null
        ];

        // Recent cases
        $casesStmt = $pdo->prepare("SELECT c.*, p.name as platform_name, p.logo as platform_logo FROM cases c JOIN scam_platforms p ON c.platform_id = p.id WHERE c.user_id = ? ORDER BY c.created_at DESC LIMIT 5");
        $casesStmt->execute([$userId]);
        $cases = $casesStmt->fetchAll(PDO::FETCH_ASSOC);

        // Ongoing recoveries
        $ongoingStmt = $pdo->prepare("SELECT c.*, p.name as platform_name FROM cases c JOIN scam_platforms p ON c.platform_id = p.id WHERE c.user_id = ? AND c.status NOT IN ('closed', 'refund_rejected') ORDER BY c.created_at DESC LIMIT 5");
        $ongoingStmt->execute([$userId]);
        $ongoingRecoveries = $ongoingStmt->fetchAll(PDO::FETCH_ASSOC);

        // Transactions
        $transactionsStmt = $pdo->prepare("SELECT t.*, CASE WHEN t.case_id IS NOT NULL THEN c.case_number ELSE 'System' END as reference_name FROM transactions t LEFT JOIN cases c ON t.case_id = c.id WHERE t.user_id = ? ORDER BY t.created_at DESC LIMIT 5");
        $transactionsStmt->execute([$userId]);
        $transactions = $transactionsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Status counts
        $statusStmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM cases WHERE user_id = ? GROUP BY status ORDER BY count DESC");
        $statusStmt->execute([$userId]);
        $statusCounts = $statusStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
        error_log("Database error (data fetch): " . $e->getMessage());
        $cases = $cases ?? [];
        $ongoingRecoveries = $ongoingRecoveries ?? [];
        $transactions = $transactions ?? [];
        $statusCounts = $statusCounts ?? [];
        $stats = $stats ?? [
            'total_cases' => 0,
            'total_reported' => 0.00,
            'total_recovered' => 0.00,
            'last_case_date' => null
        ];
    }
} else {
    // not logged in: safe defaults
    $stats = [
        'total_cases' => 0,
        'total_reported' => 0.00,
        'total_recovered' => 0.00,
        'last_case_date' => null
    ];
}

// Last AI scan
$lastAIScan = date('M d, Y H:i', strtotime($stats['last_case_date'] ?? 'now'));

// Recovery calculations
$reportedTotal = (float)($stats['total_reported'] ?? 0.0);
$recoveredTotal = (float)($stats['total_recovered'] ?? 0.0);
$recoveryPercentage = ($reportedTotal > 0) ? round(($recoveredTotal / $reportedTotal) * 100, 2) : 0;
$outstandingAmount = max(0, $reportedTotal - $recoveredTotal);
