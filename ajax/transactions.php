<?php
require_once '../config.php';
require_once '../session.php';

header('Content-Type: application/json');

try {
    // Only process POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method', 405);
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized access', 401);
    }

    // Get request data
    $input = json_decode(file_get_contents('php://input'), true);
    $start = $input['start'] ?? 0;
    $length = $input['length'] ?? 10;
    $search = $input['search']['value'] ?? '';
    $orderColumn = $input['order'][0]['column'] ?? 4;
    $orderDir = $input['order'][0]['dir'] ?? 'desc';

    // Column mapping for ordering
    $columns = [
        0 => 't.type',
        1 => 't.amount',
        2 => 'COALESCE(pm.method_name, t.payment_method_id)',
        3 => 't.status',
        4 => 't.created_at',
        5 => 't.reference'
    ];
    $orderBy = $columns[$orderColumn] ?? 't.created_at';

    // Prepare base query
    $query = "SELECT 
                t.id,
                t.type,
                t.amount,
                COALESCE(pm.method_name, t.payment_method_id) as method,
                t.status,
                t.reference,
                t.created_at,
                t.wallet_address,
                t.bank_details,
                t.proof_path,
                CASE 
                    WHEN t.type = 'withdrawal' THEN w.payment_details
                    WHEN t.type = 'deposit' THEN d.proof_path
                    WHEN t.type = 'refund' THEN crt.notes
                    ELSE NULL
                END as details
              FROM transactions t
              LEFT JOIN payment_methods pm ON t.payment_method_id = pm.id
              LEFT JOIN withdrawals w ON t.reference = w.reference AND t.type = 'withdrawal'
              LEFT JOIN deposits d ON t.reference = d.reference AND t.type = 'deposit'
              LEFT JOIN case_recovery_transactions crt ON t.case_id = crt.case_id AND t.type = 'refund'
              WHERE t.user_id = :user_id";

    // Add search filter if provided
    if (!empty($search)) {
        $query .= " AND (t.type LIKE :search OR t.status LIKE :search OR pm.method_name LIKE :search OR t.reference LIKE :search)";
    }

    // Add ordering and pagination
    $query .= " ORDER BY $orderBy $orderDir LIMIT :start, :length";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
    $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);

    if (!empty($search)) {
        $searchTerm = "%$search%";
        $stmt->bindValue(':search', $searchTerm);
    }

    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data for DataTables
    $formattedTransactions = array_map(function($transaction) {
        return [
            'id' => $transaction['id'],
            'type' => $transaction['type'],
            'amount' => $transaction['amount'],
            'method' => $transaction['method'],
            'status' => $transaction['status'],
            'reference' => $transaction['reference'],
            'created_at' => $transaction['created_at'],
            'details' => $transaction['details'],
            'proof_path' => $transaction['proof_path'],
            'wallet_address' => $transaction['wallet_address'],
            'bank_details' => $transaction['bank_details']
        ];
    }, $transactions);

    // Get total records count
    $totalQuery = "SELECT COUNT(*) FROM transactions WHERE user_id = :user_id";
    $totalStmt = $pdo->prepare($totalQuery);
    $totalStmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $totalStmt->execute();
    $totalRecords = $totalStmt->fetchColumn();

    // Get filtered count if searching
    $filteredRecords = $totalRecords;
    if (!empty($search)) {
        $filteredQuery = "SELECT COUNT(*) FROM transactions t
                         LEFT JOIN payment_methods pm ON t.payment_method_id = pm.id
                         WHERE t.user_id = :user_id
                         AND (t.type LIKE :search OR t.status LIKE :search OR pm.method_name LIKE :search OR t.reference LIKE :search)";
        $filteredStmt = $pdo->prepare($filteredQuery);
        $filteredStmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $filteredStmt->bindValue(':search', "%$search%");
        $filteredStmt->execute();
        $filteredRecords = $filteredStmt->fetchColumn();
    }

    // Return JSON response
    echo json_encode([
        'draw' => intval($input['draw'] ?? 1),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $formattedTransactions
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTrace()
    ]);
}