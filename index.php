<?php
// dashboard.php
// Completed dashboard file — safe includes, defensive checks, and full template + JS.
// Uploaded/edited: assistant
// NOTE: adjust paths (config.php, header.php, footer.php) to your app structure if needed.

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Debug marker (remove in production)
echo "<!-- DEBUG: dashboard.php loaded -->\n";

// Ensure config.php exists
if (!file_exists(__DIR__ . '/config.php')) {
    http_response_code(500);
    echo "<h1>Server configuration error</h1><p>Missing config.php</p>";
    exit;
}
require_once __DIR__ . '/config.php';

// Optionally include header.php if present
if (file_exists(__DIR__ . '/header.php')) {
    require_once __DIR__ . '/header.php';
} else {
    echo "<!-- header.php missing; continuing without it -->\n";
}

// Validate PDO instance
if (empty($pdo) || !($pdo instanceof PDO)) {
    http_response_code(500);
    echo "<h1>Database connection error</h1><p>Can't find valid PDO instance.</p>";
    exit;
}

// CSRF token init
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Current date/time UTC
$currentDateTime = new DateTime('now', new DateTimeZone('UTC'));
$currentDateTimeFormatted = $currentDateTime->format('Y-m-d H:i:s');

// Branding
$appName = "Fundtracer AI";
$appTagline = "Next-Generation Scam Recovery & Fund Tracing";
$brandColor = "#2950a8";
$brandGradient = "linear-gradient(90deg,#2950a8 0,#2da9e3 100%)";
$aiStatus = "Online";

// Safe defaults
$passwordChangeRequired = false;
$currentUser = null;
$currentUserLogin = null;
$cases = [];
$ongoingRecoveries = [];
$transactions = [];
$statusCounts = [];
$userId = $_SESSION['user_id'] ?? null;
$kyc_status = 'pending';
$loginLogs = [];

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
?>
<?php if ($passwordChangeRequired): ?>

<div class="modal fade show" id="passwordChangeModal" tabindex="-1" role="dialog"
     aria-labelledby="passwordChangeModalLabel" style="display:block; padding-right:15px;" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title mb-0" id="passwordChangeModalLabel">
                    <i class="anticon anticon-lock m-r-5"></i> Password Change Required
                </h5>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning mb-4" role="alert">
                    <i class="anticon anticon-info-circle"></i>
                    For your security, please update your password before continuing.
                </div>

                <form id="passwordChangeForm" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">

                    <!-- Current Password -->
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" required aria-required="true" autocomplete="current-password">
                    </div>

                    <!-- New Password -->
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" class="form-control" id="newPassword" required minlength="8" aria-describedby="passwordHelp" autocomplete="new-password">
                        <small id="passwordHelp" class="form-text text-muted">
                            Use a unique password. We enforce a minimum of 8 characters.
                        </small>

                        <!-- Strength Bar -->
                        <div class="progress mt-2" style="height:8px;">
                            <div id="passwordStrengthBar" class="progress-bar bg-danger" style="width:0%;" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small id="passwordStrengthText" class="text-muted small d-block mb-1" aria-live="polite">Strength: Weak</small>

                        <!-- Requirements Checklist -->
                        <ul class="list-unstyled small" id="passwordChecklist" aria-hidden="false">
                            <li id="req-length" class="text-danger"><i class="anticon anticon-close"></i> At least 8 characters</li>
                            <li id="req-upper" class="text-danger"><i class="anticon anticon-close"></i> At least one uppercase letter</li>
                            <li id="req-number" class="text-danger"><i class="anticon anticon-close"></i> At least one number</li>
                            <li id="req-special" class="text-danger"><i class="anticon anticon-close"></i> At least one special character</li>
                        </ul>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" required autocomplete="new-password">
                        <small id="passwordMatchText" class="small text-muted" aria-live="polite">Waiting for input...</small>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submitPasswordChange" aria-label="Change password">
                    <i class="anticon anticon-save"></i> Change Password
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>


<div class="modal fade" id="newDepositModal" tabindex="-1" role="dialog" aria-labelledby="newDepositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm">
            <div class="modal-header" style="background: <?= htmlspecialchars($brandGradient, ENT_QUOTES) ?>; color:#fff;">
                <h5 class="modal-title" id="newDepositModalLabel">Fund Your Account</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <form id="depositForm" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-start" role="alert">
                        <i class="anticon anticon-info-circle mr-2"></i>
                        <div>
                            <strong>Important:</strong> Please complete your deposit within 30 minutes to avoid processing delays.
                            <div class="small text-muted">Deposits help speed up recovery actions for your active cases.</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-semibold">Amount (USD)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" aria-hidden="true">$</span>
                            </div>
                            <input type="number" class="form-control" name="amount" min="10" step="0.01" required placeholder="Enter deposit amount" aria-label="Amount in US dollars">
                        </div>
                        <small class="form-text text-muted">Minimum deposit: $10.00 | Processing fee: 0%</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-semibold">Payment Method</label>
                        <select class="form-control select2" name="payment_method" id="paymentMethod" required aria-required="true">
                            <option value="">Select Payment Method</option>
                            <?php
                            try {
                                $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE is_active = 1 AND allows_deposit = 1");
                                $stmt->execute();
                                while ($method = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $details = [
                                        'bank_name' => $method['bank_name'] ?? '',
                                        'account_number' => $method['account_number'] ?? '',
                                        'routing_number' => $method['routing_number'] ?? '',
                                        'wallet_address' => $method['wallet_address'] ?? '',
                                        'instructions' => $method['instructions'] ?? '',
                                        'is_crypto' => $method['is_crypto'] ?? 0
                                    ];
                                    $detailsJson = htmlspecialchars(json_encode($details, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ENT_QUOTES);
                                    echo '<option value="'.htmlspecialchars($method['method_code'], ENT_QUOTES).'" data-details=\''.$detailsJson.'\'>'.htmlspecialchars($method['method_name'], ENT_QUOTES).'</option>';
                                }
                            } catch (Exception $e) {
                                error_log("Payment methods load error: " . $e->getMessage());
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="payment-details-container mt-4" id="paymentDetails" style="display: none;">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Payment Instructions</h6>
                            </div>
                            <div class="card-body">
                                <div id="bankDetails" style="display: none;">
                                    <div class="mb-3">
                                        <h6 class="text-primary"><i class="anticon anticon-bank"></i> Bank Transfer Details</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Bank Name:</strong></p>
                                                <p class="mb-1"><strong>Account Number:</strong></p>
                                                <p class="mb-1"><strong>Routing Number:</strong></p>
                                                <p class="mb-1"><strong>Account Type:</strong></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1" id="detail-bank-name">-</p>
                                                <p class="mb-1" id="detail-account-number">-</p>
                                                <p class="mb-1" id="detail-routing-number">-</p>
                                                <p class="mb-1">Business Checking</p>
                                            </div>
                                        </div>
                                        <div class="alert alert-warning mt-3">
                                            <i class="anticon anticon-exclamation-circle"></i>
                                            <strong>Note:</strong> Include your User ID as payment reference
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="cryptoDetails" style="display: none;">
                                    <div class="mb-3">
                                        <h6 class="text-primary"><i class="anticon anticon-block"></i> Crypto Wallet Details</h6>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="mb-1"><strong>Network:</strong> <span id="detail-crypto-network">Ethereum (ERC20)</span></p>
                                                <p class="mb-1"><strong>Wallet Address:</strong></p>
                                                <div class="input-group mb-2">
                                                    <input type="text" class="form-control" id="detail-wallet-address" readonly aria-label="Wallet address">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button" id="copyWalletAddress" aria-label="Copy wallet address">
                                                            <i class="anticon anticon-copy"></i> Copy
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="alert alert-danger">
                                                    <i class="anticon anticon-warning"></i>
                                                    <strong>Important:</strong> Send only the specified cryptocurrency to this address.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="generalInstructions" style="display: none;">
                                    <h6 class="text-primary"><i class="anticon anticon-info-circle"></i> Additional Instructions</h6>
                                    <div id="detail-instructions" class="mb-0"></div>
                                </div>
                                
                                <hr>
                                
                                <div class="form-group">
                                    <label class="font-weight-semibold" for="proofOfPayment">Proof of Payment</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="proofOfPayment" name="proof_of_payment" accept="image/*,.pdf" required>
                                        <label class="custom-file-label" for="proofOfPayment">Choose screenshot or PDF</label>
                                    </div>
                                    <small class="form-text text-muted">Accepted formats: JPG, PNG, PDF (Max 2MB)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary" aria-label="Confirm deposit">Confirm Deposit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- New Withdrawal Modal -->
<!-- 🔒 Withdrawal Modal -->
<div class="modal fade" id="newWithdrawalModal" tabindex="-1" role="dialog" aria-labelledby="newWithdrawalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="newWithdrawalModalLabel">
                    <i class="anticon anticon-wallet"></i> Withdrawal Request
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>

            <form id="withdrawalForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">

                <div class="modal-body">

                    <div class="alert alert-info" role="alert">
                        <i class="anticon anticon-info-circle"></i> 
                        <strong>Processing Time:</strong> Withdrawals are processed within 1–3 business days.
                    </div>

                    <!-- Hidden real balance for JS -->
                    <input type="hidden" id="availableBalance" value="<?= (float)($currentUser['balance'] ?? 0) ?>">

                    <!-- AMOUNT -->
                    <div class="form-group">
                        <label class="font-weight-semibold">Amount (USD)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input 
                                type="number"
                                class="form-control"
                                name="amount"
                                id="amount"
                                step="0.01"
                                required
                                placeholder="Enter withdrawal amount">
                        </div>
                        <small class="form-text text-muted">
                            Available balance: $<?= number_format($currentUser['balance'] ?? 0, 2) ?>
                        </small>
                    </div>

                    <!-- PAYMENT METHOD -->
                    <div class="form-group">
                        <label class="font-weight-semibold">Payment Method</label>
                        <select class="form-control select2" name="payment_method" id="withdrawalMethod" required>
                            <option value="">Select Withdrawal Method</option>
                            <?php
                            try {
                                $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE is_active = 1 AND allows_withdrawal = 1");
                                $stmt->execute();
                                while ($method = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . htmlspecialchars($method['method_code'], ENT_QUOTES) . '">' 
                                         . htmlspecialchars($method['method_name'], ENT_QUOTES) . '</option>';
                                }
                            } catch (Exception $e) {
                                error_log("Withdrawal methods load error: " . $e->getMessage());
                            }
                            ?>
                        </select>
                    </div>

                    <!-- BANK DETAILS (Auto-Fill) -->
                    <div id="bankDetailsContainer" class="mt-3" style="display:none;">
                        <h6 class="text-primary"><i class="anticon anticon-bank"></i> Your Bank Details</h6>
                        <p><strong>Bank:</strong> <span id="user-bank-name">-</span></p>
                        <p><strong>Account Holder:</strong> <span id="user-account-holder">-</span></p>
                        <p><strong>IBAN:</strong> <span id="user-iban">-</span></p>
                        <p><strong>BIC:</strong> <span id="user-bic">-</span></p>
                    </div>

                    <!-- PAYMENT DETAILS -->
                    <div class="form-group mt-3">
                        <label class="font-weight-semibold">Payment Details</label>
                        <textarea class="form-control" name="payment_details" id="paymentDetails" rows="3" required placeholder="Enter complete payment details"></textarea>
                    </div>

                    <!-- CONFIRM CHECKBOX -->
                    <div class="form-group mt-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="confirmDetails" required>
                            <label class="custom-control-label" for="confirmDetails">
                                I confirm that the provided payment details are accurate.
                            </label>
                        </div>
                    </div>

                    <!-- OTP SECTION -->
                    <hr>
                    <div id="otpSection" class="pt-2">
                        <h6 class="text-primary">
                            <i class="anticon anticon-safety"></i> Email Verification
                        </h6>
                        <p class="text-muted mb-2">
                            For security reasons, please verify your identity via the one-time code sent to your registered email.
                        </p>

                        <div class="input-group mb-2">
                            <input type="text" id="otpCode" maxlength="6" class="form-control" placeholder="Enter 6-digit OTP" disabled>
                            <div class="input-group-append">
                                <button type="button" id="sendOtpBtn" class="btn btn-outline-primary">
                                    <i class="anticon anticon-mail"></i> Send OTP
                                </button>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="button" id="verifyOtpBtn" class="btn btn-outline-success" disabled>
                                <i class="anticon anticon-check-circle"></i> Verify OTP
                            </button>
                        </div>
                        <small id="otpInfoText" class="form-text text-muted mt-1">
                            OTP is valid for 5 minutes.
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        <i class="anticon anticon-close"></i> Cancel
                    </button>
                    <button type="submit" id="withdrawalSubmitBtn" class="btn btn-success" disabled>
                        <i class="anticon anticon-send"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" role="dialog" aria-labelledby="transactionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="transactionDetailsModalLabel">Transaction Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-semibold">Transaction ID:</label>
                            <p id="txn-id" class="form-control-static">-</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-semibold">Date & Time:</label>
                            <p id="txn-date" class="form-control-static">-</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-semibold">Type:</label>
                            <p id="txn-type" class="form-control-static">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-semibold">Amount:</label>
                            <p id="txn-amount" class="form-control-static">-</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-semibold">Status:</label>
                            <p id="txn-status" class="form-control-static">-</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-semibold">Reference:</label>
                            <p id="txn-reference" class="form-control-static">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Payment Details</h6>
                    </div>
                    <div class="card-body">
                        <div id="txn-payment-details"></div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Transaction Timeline</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush" id="txn-timeline" role="list">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Request submitted</span>
                                <small class="text-muted">-</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" id="printReceiptBtn">Print Receipt</button>
            </div>
        </div>
    </div>
</div>

<!-- Current Date and Time Display -->
<div class="fixed-bottom text-right p-2" style="z-index: 1000;">
    <small class="bg-dark text-light px-2 py-1 rounded" role="status" aria-live="polite">
        Current Date and Time (UTC): <?= htmlspecialchars($currentDateTimeFormatted, ENT_QUOTES) ?> | Current User's Login: <?= htmlspecialchars($currentUserLogin, ENT_QUOTES) ?>
    </small>
</div>

<style>
:root{
    --brand:#2950a8;
    --brand-light:#2da9e3;
    --bg:#f7fafd;
    --muted:#6c757d;
    --card-radius:12px;
}
body{background:var(--bg);font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;}
.main-content .card{border-radius:var(--card-radius);transition:transform .12s ease, box-shadow .12s ease;}
.main-content .card:hover{transform:translateY(-4px);box-shadow:0 8px 20px rgba(41,80,168,0.12);}
.avatar-icon{display:inline-flex;align-items:center;justify-content:center;border-radius:8px;padding:12px;color:#fff}
.avatar-blue{background:linear-gradient(45deg,#2950a8,#2da9e3)}
.avatar-cyan{background:linear-gradient(45deg,#17a2b8,#5bd0e6)}
.avatar-gold{background:linear-gradient(45deg,#f39c12,#f6c36d)}
.avatar-purple{background:linear-gradient(45deg,#6f42c1,#b28bff)}
.lead{font-size:1.05rem;font-weight:600}
.algorithm-animation{padding:12px 0}
.algorithm-steps{display:flex;justify-content:space-between;align-items:center;gap:8px;padding:6px}
.step{flex:1;background:#fff;border-radius:8px;padding:10px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,0.04);transition:transform .18s ease,background .18s ease}
.step.active{background:linear-gradient(90deg,#e8f3ff,#f4fbff);transform:translateY(-6px)}
.step-icon{font-size:20px;margin-bottom:6px;color:var(--brand)}
.algorithm-progress{height:8px;background:#e9eef7;border-radius:10px;margin-top:12px;overflow:hidden}
.algorithm-progress .progress-bar{height:100%;background:linear-gradient(90deg,var(--brand),var(--brand-light));transition:width 900ms cubic-bezier(.2,.9,.3,1)}
.table-hover tbody tr:hover{background:rgba(41,80,168,0.03)}
.live-progress{transition:width 700ms cubic-bezier(.2,.9,.3,1)}
.scrollable{overflow:auto;padding-right:8px}
.kv-skeleton{background:linear-gradient(90deg,#f3f6fb,#eef6ff);border-radius:8px;height:18px;display:inline-block;width:100%;animation:skeleton 1.2s linear infinite}
@keyframes skeleton{0%{opacity:1}50%{opacity:.5}100%{opacity:1}}
.badge-pill{border-radius:999px}
.small-muted{color:var(--muted)}
.tooltip-inner{max-width:280px}
</style>


<div class="main-content">
    <div class="container-fluid">

        <!-- HEADER & BRAND -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm" style="background: <?= htmlspecialchars($brandGradient, ENT_QUOTES) ?>; color: #fff; border: none;">
                    <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <div class="h5 mb-1" style="font-weight:700;letter-spacing:.2px;"><?= htmlspecialchars($appName, ENT_QUOTES) ?></div>
                            <div class="lead"><?= htmlspecialchars($appTagline, ENT_QUOTES) ?></div>
                            <div class="mt-2">
                                <span class="badge badge-light" style="color:<?= htmlspecialchars($brandColor, ENT_QUOTES) ?>;">
                                    Your data is encrypted & AI monitored
                                </span>
                                <span class="ml-2 mt-2 badge badge-success" id="ai-status-badge" role="status" aria-live="polite">
                                    AI Status: <span id="aiStatusText"><?= htmlspecialchars($aiStatus, ENT_QUOTES) ?></span>
                                </span>
                                <span class="ml-2 mt-2 badge badge-info">
                                    Last scan: <span id="lastScanText"><?= htmlspecialchars($lastAIScan, ENT_QUOTES) ?></span>
                                </span>
                            </div>
                        </div>
                        <div class="text-right mt-2">
                            <div class="mb-2">
                                <span class="badge badge-pill badge-primary" style="font-size:1.05em;">
                                    Welcome, <?= htmlspecialchars($currentUser['first_name'] ?? $currentUserLogin, ENT_QUOTES) ?>!
                                </span>
                            </div>
                            <div class="mt-2">
                                <span class="font-weight-bold" style="font-size:0.95em;color:rgba(255,255,255,.9)">Balance:</span>
                                <span class="h3 font-weight-bold text-light" id="balanceCounter" data-value="<?= number_format($currentUser['balance'] ?? 0,2, '.', '') ?>">$<?= number_format($currentUser['balance'] ?? 0,2) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- AI INSIGHT CARD -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm border-0" aria-labelledby="aiInsightsHeading">
                    <div class="card-body">
                        <h5 id="aiInsightsHeading" class="mb-2">
                            <i class="anticon anticon-robot text-primary"></i> Fundtracer AI Insights
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <li><i class="anticon anticon-check-circle text-success"></i>
                                Continuous monitoring for suspicious activity.
                            </li>
                            <li class="mt-1"><i class="anticon anticon-info-circle text-info"></i>
                                Next scheduled scan: <b id="nextScan"><?= date('M d, Y H:i', strtotime('+1 hour')) ?></b>
                            </li>
                            <li class="mt-1"><i class="anticon anticon-exclamation-circle text-warning"></i>
                                <?php if ($passwordChangeRequired): ?>
                                    <span class="text-danger">Action required: Please change your password for enhanced security.</span>
                                <?php else: ?>
                                    <span class="text-muted">No immediate actions required.</span>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- KYC/AML -->
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm border-0" aria-labelledby="complianceHeading">
                    <div class="card-body">
                        <h5 id="complianceHeading" class="mb-2">
                            <i class="anticon anticon-safety-certificate text-success"></i> Compliance Status
                        </h5>
                        <p class="mb-1">
                            KYC Status:
                            <?php
                            $kycStatus = $kyc_status;
                            $kycBadge = "secondary";
                            if ($kycStatus === 'approved') {
                                $kycBadge = "success";
                                $kycStatus = "verified";
                            } elseif ($kycStatus === 'rejected') {
                                $kycBadge = "danger";
                            }
                            ?>
                            <span class="badge badge-<?= htmlspecialchars($kycBadge, ENT_QUOTES) ?>"><?= htmlspecialchars(ucfirst($kycStatus), ENT_QUOTES) ?></span>
                        </p>
                        <p class="mb-0">To ensure secure recovery procedures, withdrawals and advanced tools require KYC verification.</p>
                        <?php if ($kycStatus == "pending"): ?>
                            <a href="kyc.php" class="btn btn-outline-primary btn-sm mt-2" role="button" aria-pressed="false">Verify Now</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Security -->
            <div class="col-md-12 col-lg-4">
                <div class="card shadow-sm border-0" aria-labelledby="securityHeading">
                    <div class="card-body">
                        <h5 id="securityHeading" class="mb-2">
                            <i class="anticon anticon-lock text-primary"></i> Account Security
                        </h5>
                        <p class="mb-1">
                            Last login: <b><?= htmlspecialchars($currentUser['last_login'] ?? $currentDateTimeFormatted, ENT_QUOTES) ?></b>
                        </p>
                        <p class="mb-1">Location: <span id="user-location">Detecting...</span></p>
                        <p class="mb-0">If this wasn't you, <a href="support.php">contact support</a> immediately.</p>
                        <script>
                        (function(){
                            fetch('https://ipapi.co/json/').then(function(r){ return r.json() }).then(function(data){
                                var el = document.getElementById('user-location');
                                if (data && data.city && data.country_name) {
                                    el.textContent = data.city + ', ' + data.country_name + ' (' + data.ip + ')';
                                } else {
                                    el.textContent = 'Location not available';
                                }
                            }).catch(function(){ document.getElementById('user-location').innerText = 'Location not available'; });
                        })();
                        </script>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Quick Actions</h5>
                            <p class="card-text small text-muted mb-0">Perform common transactions quickly</p>
                        </div>
                        <div class="btn-group" role="group" aria-label="Quick actions">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#newDepositModal" data-toggle="tooltip" title="Add funds">
                                <i class="anticon anticon-plus"></i> New Deposit
                            </button>
                            <button class="btn btn-success ml-2" data-toggle="modal" data-target="#newWithdrawalModal" data-toggle="tooltip" title="Request withdrawal">
                                <i class="anticon anticon-minus"></i> New Withdrawal
                            </button>
                            <a href="transactions.php" class="btn btn-info ml-2" title="View transactions">
                                <i class="anticon anticon-history"></i> Transactions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Row -->
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="avatar avatar-icon avatar-lg avatar-blue" aria-hidden="true">
                                <i class="anticon anticon-file-text"></i>
                            </div>
                            <div class="m-l-15">
                                <h2 class="m-b-0 count" data-value="<?= htmlspecialchars($stats['total_cases'], ENT_QUOTES) ?>"><?= htmlspecialchars($stats['total_cases'], ENT_QUOTES) ?></h2>
                                <p class="m-b-0 text-muted">Total Cases</p>
                                <?php if ($stats['last_case_date']): ?>
                                <small class="text-muted">Last case: <?= htmlspecialchars(date('M d, Y', strtotime($stats['last_case_date'])), ENT_QUOTES) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="avatar avatar-icon avatar-lg avatar-cyan" aria-hidden="true">
                                <i class="anticon anticon-line-chart"></i>
                            </div>
                            <div class="m-l-15">
                                <h2 class="m-b-0 count" data-value="<?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>"><?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>%</h2>
                                <p class="m-b-0 text-muted">Recovery Rate</p>
                                <small class="text-<?= $recoveryPercentage >= 50 ? 'success' : 'warning' ?>">
                                    <?= $recoveryPercentage >= 50 ? 'Above average' : 'Below average' ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="avatar avatar-icon avatar-lg avatar-gold" aria-hidden="true">
                                <i class="anticon anticon-dollar"></i>
                            </div>
                            <div class="m-l-15">
                                <h2 class="m-b-0">$<?= number_format($stats['total_reported'], 2) ?></h2>
                                <p class="m-b-0 text-muted">Reported Loss</p>
                                <?php if ($outstandingAmount > 0): ?>
                                <small class="text-danger">$<?= number_format($outstandingAmount, 2) ?> outstanding</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="avatar avatar-icon avatar-lg avatar-purple" aria-hidden="true">
                                <i class="anticon anticon-dollar"></i>
                            </div>
                            <div class="m-l-15">
                                <h2 class="m-b-0">$<?= number_format($stats['total_recovered'], 2) ?></h2>
                                <p class="m-b-0 text-muted">Amount Recovered</p>
                                <?php if ($stats['total_recovered'] > 0): ?>
                                <small class="text-success"><?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>% of total</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recovery / Workflow -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recovery Status</h5>
                            <div>
                                <span class="badge badge-pill badge-<?= $recoveryPercentage > 70 ? 'success' : ($recoveryPercentage > 30 ? 'warning' : 'danger') ?>">
                                    <?= $recoveryPercentage > 70 ? 'Excellent' : ($recoveryPercentage > 30 ? 'Good' : 'Needs Attention') ?>
                                </span>
                            </div>
                        </div>

                        <div class="m-t-20">
                            <div class="algorithm-animation">
                                <div class="algorithm-steps" aria-hidden="true">
                                    <div class="step <?= $recoveryPercentage > 0 ? 'active' : '' ?>">
                                        <div class="step-icon">
                                            <i class="anticon anticon-search"></i>
                                        </div>
                                        <div class="step-label">Trace Funds</div>
                                    </div>
                                    <div class="step <?= $recoveryPercentage > 20 ? 'active' : '' ?>">
                                        <div class="step-icon">
                                            <i class="anticon anticon-lock"></i>
                                        </div>
                                        <div class="step-label">Freeze Assets</div>
                                    </div>
                                    <div class="step <?= $recoveryPercentage > 40 ? 'active' : '' ?>">
                                        <div class="step-icon">
                                            <i class="anticon anticon-solution"></i>
                                        </div>
                                        <div class="step-label">Legal Process</div>
                                    </div>
                                    <div class="step <?= $recoveryPercentage > 60 ? 'active' : '' ?>">
                                        <div class="step-icon">
                                            <i class="anticon anticon-sync"></i>
                                        </div>
                                        <div class="step-label">Recovery</div>
                                    </div>
                                    <div class="step <?= $recoveryPercentage > 80 ? 'active' : '' ?>">
                                        <div class="step-icon">
                                            <i class="anticon anticon-check-circle"></i>
                                        </div>
                                        <div class="step-label">Complete</div>
                                    </div>
                                </div>
                                <div class="algorithm-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>">
                                    <div class="progress-bar" style="width: <?= $recoveryPercentage ?>%"></div>
                                </div>
                            </div>

                            <div class="m-t-20 text-center">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-left">
                                        <p class="m-b-5"><strong>Total Cases:</strong> <?= htmlspecialchars($stats['total_cases'], ENT_QUOTES) ?></p>
                                        <p class="m-b-5"><strong>Active Cases:</strong> <?= array_sum($statusCounts) ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="m-b-5"><strong>Recovered:</strong> $<?= number_format($stats['total_recovered'], 2) ?></p>
                                        <p class="m-b-5"><strong>Outstanding:</strong> $<?= number_format($outstandingAmount, 2) ?></p>
                                    </div>
                                </div>
                                <button class="btn btn-outline-primary btn-sm" id="refresh-algorithm" aria-live="polite">
                                    <i class="anticon anticon-sync"></i> Refresh Status
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Cases + Side column -->
        <div class="row mt-3">
            <div class="col-md-12 col-lg-8">
                <!-- Recent Cases -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Recent Cases</h5>
                            <div>
                                <a href="cases.php" class="btn btn-sm btn-default">View All</a>
                                <a href="new-case.php" class="btn btn-sm btn-primary">New Case</a>
                            </div>
                        </div>
                        
                        <?php if (empty($cases)): ?>
                            <div class="alert alert-info m-t-20">No cases found. <a href="new-case.php">File your first case</a></div>
                        <?php else: ?>
                            <div class="m-t-10">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Case #</th>
                                                <th>Platform</th>
                                                <th>Reported</th>
                                                <th>Recovered</th>
                                                <th>Status</th>
                                                <th>Progress</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($cases as $case): 
                                                $reported = (float)($case['reported_amount'] ?? 0);
                                                $recovered = (float)($case['recovered_amount'] ?? 0);
                                                $status = $case['status'] ?? 'open';
                                                
                                                $progress = ($reported > 0) ? round(($recovered / $reported) * 100, 2) : 0;
                                                
                                                $statusClass = [
                                                    'open' => 'warning',
                                                    'documents_required' => 'secondary',
                                                    'under_review' => 'info',
                                                    'refund_approved' => 'success',
                                                    'refund_rejected' => 'danger',
                                                    'closed' => 'dark'
                                                ][$status] ?? 'light';
                                            ?>
                                            <tr>
                                                <td>
                                                    <a href="case-details.php?id=<?= htmlspecialchars($case['id'], ENT_QUOTES) ?>">
                                                        <?= htmlspecialchars($case['case_number'], ENT_QUOTES) ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="media align-items-center">
                                                        <?php if (!empty($case['platform_logo'])): ?>
                                                        <div class="avatar avatar-image" style="width: 34px; height: 34px">
                                                            <img src="<?= htmlspecialchars($case['platform_logo'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($case['platform_name'], ENT_QUOTES) ?>">
                                                        </div>
                                                        <?php endif; ?>
                                                        <div class="m-l-10">
                                                            <?= htmlspecialchars($case['platform_name'], ENT_QUOTES) ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>$<?= number_format($reported, 2) ?></td>
                                                <td>$<?= number_format($recovered, 2) ?></td>
                                                <td>
                                                    <span class="badge badge-pill badge-<?= htmlspecialchars($statusClass, ENT_QUOTES) ?>">
                                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status)), ENT_QUOTES) ?>
                                                    </span>
                                                </td>
                                                <td style="min-width:140px">
                                                    <div class="progress progress-sm position-relative" style="height:20px;">
                                                        <div class="progress-bar bg-<?= htmlspecialchars($statusClass, ENT_QUOTES) ?> live-progress"
                                                             data-final="<?= htmlspecialchars($progress, ENT_QUOTES) ?>"
                                                             style="width: 0%;"
                                                             aria-valuenow="0"
                                                             aria-valuemin="0"
                                                             aria-valuemax="100">
                                                        </div>
                                                        <span class="position-absolute w-100 text-center small" style="top:0;left:0;line-height:20px;" data-progress-label><?= htmlspecialchars($progress, ENT_QUOTES) ?>%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="case-details.php?id=<?= htmlspecialchars($case['id'], ENT_QUOTES) ?>" class="btn btn-sm btn-default" title="View case">
                                                        <i class="anticon anticon-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Active Recovery Operations -->
                <div class="card mt-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Active Recovery Operations</h5>
                            <small class="text-muted"><?= count($ongoingRecoveries) ?> active cases</small>
                        </div>
                        <div class="m-t-20">
                            <?php if (empty($ongoingRecoveries)): ?>
                                <div class="alert alert-info">No active recovery operations</div>
                            <?php else: ?>
                                <?php foreach ($ongoingRecoveries as $recovery): 
                                    $reported = (float)($recovery['reported_amount'] ?? 0);
                                    $recovered = (float)($recovery['recovered_amount'] ?? 0);
                                    $status = $recovery['status'] ?? 'open';
                                    
                                    $progress = ($reported > 0) ? round(($recovered / $reported) * 100, 2) : 0;
                                    
                                    $statusClass = 'info';
                                    $statusText = 'In progress';
                                    
                                    if ($status === 'documents_required') {
                                        $statusClass = 'danger';
                                        $statusText = 'Needs attention';
                                    } elseif ($progress > 70) {
                                        $statusClass = 'success';
                                        $statusText = 'On track';
                                    } elseif ($progress > 30) {
                                        $statusClass = 'warning';
                                        $statusText = 'In progress';
                                    }
                                ?>
                                <div class="m-b-25">
                                    <div class="d-flex justify-content-between m-b-5">
                                        <div>
                                            <a href="case-details.php?id=<?= htmlspecialchars($recovery['id'], ENT_QUOTES) ?>">
                                                <?= htmlspecialchars($recovery['case_number'], ENT_QUOTES) ?>
                                            </a>
                                        </div>
                                        <div class="text-right">
                                            <span><?= htmlspecialchars($progress, ENT_QUOTES) ?>%</span>
                                            <div class="text-<?= htmlspecialchars($statusClass, ENT_QUOTES) ?>">
                                                <?= htmlspecialchars($statusText, ENT_QUOTES) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-<?= htmlspecialchars($statusClass, ENT_QUOTES) ?>" 
                                             style="width: <?= htmlspecialchars($progress, ENT_QUOTES) ?>%" 
                                             aria-valuenow="<?= htmlspecialchars($progress, ENT_QUOTES) ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between m-t-10">
                                        <small class="text-muted">
                                            Reported: $<?= number_format($reported, 2) ?>
                                        </small>
                                        <small class="text-muted">
                                            Recovered: $<?= number_format($recovered, 2) ?>
                                        </small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                
                                <div class="text-center m-t-20">
                                    <a href="cases.php" class="btn btn-sm btn-outline-primary">
                                        <i class="anticon anticon-eye"></i> View All Cases
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right column -->
            <div class="col-md-12 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Account Security</h5>
                        <ul class="list-group list-group-flush mb-2" role="list">
                            <?php if (empty($loginLogs)): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="text-success">Current session</span>
                                    <small><?= date('Y-m-d H:i') ?></small>
                                </li>
                            <?php else: ?>
                                <?php foreach($loginLogs as $log): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <?= $log['success'] ? "<span class='text-success'>Success</span>" : "<span class='text-danger'>Failed</span>" ?>
                                            from <code><?= htmlspecialchars($log['ip_address'], ENT_QUOTES) ?></code>
                                        </span>
                                        <small><?= htmlspecialchars(date('Y-m-d H:i', strtotime($log['attempted_at'])), ENT_QUOTES) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                        <small class="text-muted">Recent login attempts. <a href="security-logs.php">View all</a></small>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <h5 class="m-b-0">Recent Transactions</h5>
                        <div class="m-v-30" style="height: 300px">
                            <?php if (empty($transactions)): ?>
                                <div class="alert alert-info m-t-20">No transactions yet</div>
                            <?php else: ?>
                                <div class="scrollable" style="height: 280px">
                                    <?php foreach ($transactions as $transaction): ?>
                                    <div class="m-b-20">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <?php
                                                $iconConfig = [
                                                    'refund' => ['icon' => 'arrow-up', 'color' => 'success'],
                                                    'deposit' => ['icon' => 'arrow-down', 'color' => 'primary'],
                                                    'withdrawal' => ['icon' => 'arrow-up', 'color' => 'danger'],
                                                    'fee' => ['icon' => 'minus', 'color' => 'warning']
                                                ];
                                                $config = $iconConfig[$transaction['type']] ?? ['icon' => 'swap', 'color' => 'info'];
                                                ?>
                                                <div class="avatar avatar-icon avatar-<?= htmlspecialchars($config['color'], ENT_QUOTES) ?>" aria-hidden="true">
                                                    <i class="anticon anticon-<?= htmlspecialchars($config['icon'], ENT_QUOTES) ?>"></i>
                                                </div>
                                                <div class="m-l-15">
                                                    <h6 class="m-b-0"><?= ucfirst(htmlspecialchars($transaction['type'], ENT_QUOTES)) ?></h6>
                                                    <p class="m-b-0 text-muted">
                                                        <?= htmlspecialchars($transaction['reference_name'], ENT_QUOTES) ?>
                                                        <br>
                                                        <small><?= date('M d, Y', strtotime($transaction['created_at'])) ?></small>
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="text-<?= in_array($transaction['type'], ['refund', 'deposit']) ? 'success' : 'danger' ?> font-weight-semibold">
                                                $<?= number_format($transaction['amount'], 2) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <h5 class="m-b-20">Case Status Summary</h5>
                        <div class="m-t-20">
                            <?php if (empty($statusCounts)): ?>
                                <div class="alert alert-info">No cases found</div>
                            <?php else: ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <canvas id="statusChart" height="200" aria-label="Case status chart"></canvas>
                                    </div>
                                </div>
                                <div class="m-t-20">
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($statusCounts as $status => $count): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center p-l-0 p-r-0">
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status)), ENT_QUOTES) ?>
                                            <span class="badge badge-pill badge-<?= htmlspecialchars([
                                                'open' => 'warning',
                                                'documents_required' => 'secondary',
                                                'under_review' => 'info',
                                                'refund_approved' => 'success',
                                                'refund_rejected' => 'danger',
                                                'closed' => 'dark'
                                            ][$status] ?? 'light', ENT_QUOTES) ?>"><?= htmlspecialchars($count, ENT_QUOTES) ?></span>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Safety resources -->
                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <h6 class="mb-2">Safety & Resources</h6>
                        <p class="small text-muted mb-2">If you suspect fraud, please:</p>
                        <ul class="small text-muted mb-0">
                            <li>Contact support immediately: <a href="support.php">support</a></li>
                            <li>Consider filing a police report and share case reference</li>
                            <li>Keep payment proofs and communications</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Recovery Progress -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Recovery Progress</h5>
                            <div>
                                <span class="text-<?= $recoveryPercentage >= 50 ? 'success' : 'warning' ?>">
                                    <?= $recoveryPercentage >= 50 ? 'Good progress' : 'Needs attention' ?>
                                </span>
                            </div>
                        </div>
                        <div class="m-t-30">
                            <div class="d-flex justify-content-between align-items-center m-b-20">
                                <span class="font-weight-semibold">
                                    Overall Recovery: <span class="count" data-value="<?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>"><?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>%</span>
                                    (<?= htmlspecialchars($stats['total_cases'], ENT_QUOTES) ?> cases)
                                </span>
                                <span>
                                    $<?= number_format($stats['total_recovered'], 2) ?> of $<?= number_format($stats['total_reported'], 2) ?>
                                </span>
                            </div>
                            <div class="progress progress-sm" aria-hidden="false" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>">
                                <div class="progress-bar bg-success" style="width: <?= $recoveryPercentage ?>%"></div>
                            </div>
                            <div class="m-t-10 d-flex justify-content-between">
                                <small class="text-muted">0%</small>
                                <small class="text-muted">100%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
// include footer safely
if (file_exists(__DIR__ . '/footer.php')) {
    include __DIR__ . '/footer.php';
} else {
    echo "<!-- footer.php missing; page ended -->\n";
}
?>

<script>
$(function(){
    // Tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // animate counts
    function animateCount(el, start, end, decimals, duration) {
        decimals = decimals || 0;
        var current = start;
        var range = end - start;
        var increment = range / (duration / 30);
        var timer = setInterval(function() {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }
            $(el).text((decimals ? current.toFixed(decimals) : Math.round(current)).toString() + (el.dataset.suffix || ''));
        }, 30);
    }

    $('.count').each(function(){
        var $el = $(this);
        var end = parseFloat($el.data('value')) || 0;
        var start = 0;
        var decimals = (String(end).indexOf('.') !== -1) ? 2 : 0;
        animateCount(this, start, end, decimals, 700);
    });

    var $balance = $('#balanceCounter');
    if ($balance.length) {
        var bval = parseFloat($balance.data('value')) || 0;
        animateCount($balance[0], 0, bval, 2, 800);
    }

    function animateLiveProgress(el) {
        var $bar = $(el);
        var finalVal = parseFloat($bar.data('final')) || 0;
        var progressLabel = $bar.parent().find('[data-progress-label]');
        var live = 0;
        $bar.css('width', live + '%');
        
        var step = function() {
            live += Math.max(0.5, (finalVal-live)/6);
            if (live >= finalVal) {
                live = finalVal;
                $bar.css('width', finalVal + '%');
                progressLabel.text(finalVal + '%');
            } else {
                $bar.css('width', live + '%');
                progressLabel.text(Math.round(live * 100) / 100 + '%');
                setTimeout(step, 60 + Math.random()*60);
            }
        };
        setTimeout(step, 200 + Math.random()*200);
    }
    $('.live-progress').each(function() { animateLiveProgress(this); });

    // Copy wallet address
    $(document).on('click', '#copyWalletAddress', function() {
        var walletAddress = $('#detail-wallet-address').val();
        if (!walletAddress) { toastr.warning('No address to copy'); return; }
        navigator.clipboard.writeText(walletAddress).then(function() {
            toastr.success('Wallet address copied to clipboard');
        }, function() {
            toastr.error('Failed to copy wallet address');
        });
    });

    // Payment method change
    $('#paymentMethod').change(function() {
        var selectedOption = $(this).find('option:selected');
        var details = selectedOption.data('details');
        var $paymentDetails = $('#paymentDetails');
        
        if (!details) {
            $paymentDetails.hide();
            return;
        }
        
        if (typeof details === 'string') {
            try {
                details = JSON.parse(details);
            } catch (e) {
                console.error('Error parsing payment details:', e);
                return;
            }
        }
        
        $('#bankDetails, #cryptoDetails, #generalInstructions').hide();
        
        if (details.bank_name) {
            $('#detail-bank-name').text(details.bank_name);
            $('#detail-account-number').text(details.account_number || '-');
            $('#detail-routing-number').text(details.routing_number || '-');
            $('#bankDetails').show();
        }
        
        if (details.wallet_address) {
            $('#detail-wallet-address').val(details.wallet_address);
            $('#cryptoDetails').show();
        }
        
        if (details.instructions) {
            $('#detail-instructions').text(details.instructions);
            $('#generalInstructions').show();
        }
        
        $paymentDetails.show();
    });

    // File input label
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Deposit submit
    $('#depositForm').submit(function(e) {
        e.preventDefault();
        var $form = $(this);
        var formData = new FormData($form[0]);
        var $submitBtn = $form.find('button[type="submit"]');
        
        $submitBtn.prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin"></i> Processing...');
        
        $.ajax({
            url: 'ajax/process-deposit.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    var data = typeof response === 'string' ? JSON.parse(response) : response;
                    if (data.success) {
                        toastr.success(data.message || 'Deposit submitted successfully');
                        $('#newDepositModal').modal('hide');
                        $form[0].reset();
                        $('.custom-file-label').html('Choose file');
                        $('#paymentDetails').hide();
                        setTimeout(function(){ location.reload(); }, 1200);
                    } else {
                        toastr.error(data.message || 'Error processing deposit');
                    }
                } catch (e) {
                    toastr.error('Error parsing server response');
                }
                $submitBtn.prop('disabled', false).html('Confirm Deposit');
            },
            error: function(xhr, status, error) {
                toastr.error('Error communicating with server: ' + error);
                $submitBtn.prop('disabled', false).html('Confirm Deposit');
            }
        });
    });

// =====================================================
// 💸 WITHDRAWAL FORM SUBMIT (WITH OTP + BALANCE CHECK)
// =====================================================
$('#withdrawalForm').submit(function (e) {
    e.preventDefault();
    const $form = $(this);
    const $submitBtn = $form.find('button[type="submit"]');

    // Ensure OTP verified (button enabled only after verification)
    if ($('#withdrawalSubmitBtn').prop('disabled')) {
        toastr.warning('Please verify your OTP before submitting.');
        return;
    }

    // Validate balance and amount before sending
    const available = parseFloat($('#availableBalance').val()) || 0;
    const amount = parseFloat($('#amount').val()) || 0;
    if (available < 10) {
        toastr.error('Insufficient funds. Minimum balance required is $10.');
        return;
    }
    if (amount < 10) {
        toastr.error('Minimum withdrawal amount is $10.');
        return;
    }
    if (amount > available) {
        toastr.error('Insufficient balance. Available: $' + available.toFixed(2));
        return;
    }

    // Send request
    $submitBtn.prop('disabled', true)
        .html('<i class="anticon anticon-loading anticon-spin"></i> Processing...');

    $.ajax({
        url: 'ajax/process-withdrawal.php',
        method: 'POST',
        data: $form.serialize(),
        success: function (response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                if (data.success) {
                    toastr.success(data.message || 'Withdrawal request submitted successfully');
                    $('#newWithdrawalModal').modal('hide');
                    $form[0].reset();
                    resetOtpFields();
                    setTimeout(() => location.reload(), 1200);
                } else {
                    toastr.error(data.message || 'Error processing withdrawal');
                    if (data.message && data.message.includes('OTP')) resetOtpFields();
                }
            } catch (err) {
                toastr.error('Error parsing server response');
                console.error(err);
            }
        },
        error: function (xhr, status, error) {
            toastr.error('Server communication error: ' + error);
        },
        complete: function () {
            $submitBtn.prop('disabled', false).html('Submit Request');
        }
    });
});


// =====================================================
// 🏦 WITHDRAWAL METHOD AUTO-FILL (BANK DETAILS)
// =====================================================
$('#withdrawalMethod').change(function () {
    const method = $(this).val() || '';
    if (method.toLowerCase().includes('bank')) {
        $.getJSON('ajax/get_bank_details.php')
            .done(function (res) {
                if (res.success) {
                    $('#user-bank-name').text(res.bank.bank_name || '-');
                    $('#user-account-holder').text(res.bank.account_holder || '-');
                    $('#user-iban').text(res.bank.iban || '-');
                    $('#user-bic').text(res.bank.bic || '-');
                    $('#bankDetailsContainer').show();

                    $('textarea[name="payment_details"]').val(
                        (res.bank.account_holder || '') + "\n" +
                        (res.bank.bank_name || '') + "\n" +
                        "IBAN: " + (res.bank.iban || '-') + "\n" +
                        "BIC: " + (res.bank.bic || '-')
                    );
                } else {
                    toastr.warning(res.message || 'No bank details found');
                    $('#bankDetailsContainer').hide();
                }
            })
            .fail(function () {
                toastr.error('Failed to load bank details');
                $('#bankDetailsContainer').hide();
            });
    } else {
        $('#bankDetailsContainer').hide();
        $('textarea[name="payment_details"]').val('');
    }
});


// =====================================================
// 💵 LIVE BALANCE CHECK (REAL DB VALUE)
// =====================================================
$('#amount').on('input', function () {
    const amount = parseFloat($(this).val()) || 0;
    const available = parseFloat($('#availableBalance').val()) || 0;

    $('#insufficientFundsWarning').remove();

    // Case 1: Balance too low to withdraw
    if (available < 10) {
        $(this).closest('.form-group').append(`
            <div id="insufficientFundsWarning" class="alert alert-danger mt-2 p-2 mb-0">
                <i class="anticon anticon-warning"></i>
                You need at least $10 available to withdraw. Current balance: $${available.toFixed(2)}
            </div>
        `);
        $('#sendOtpBtn, #withdrawalSubmitBtn').prop('disabled', true);
        return;
    }

    // Case 2: Amount greater than available
    if (amount > available) {
        $(this).closest('.form-group').append(`
            <div id="insufficientFundsWarning" class="alert alert-danger mt-2 p-2 mb-0">
                <i class="anticon anticon-warning"></i>
                Insufficient balance: available $${available.toFixed(2)}
            </div>
        `);
        $('#sendOtpBtn, #withdrawalSubmitBtn').prop('disabled', true);
        return;
    }

    // Case 3: Amount below minimum
    if (amount > 0 && amount < 10) {
        $(this).closest('.form-group').append(`
            <div id="insufficientFundsWarning" class="alert alert-warning mt-2 p-2 mb-0">
                <i class="anticon anticon-info-circle"></i>
                Minimum withdrawal amount is $10.
            </div>
        `);
        $('#sendOtpBtn, #withdrawalSubmitBtn').prop('disabled', true);
        return;
    }

    // ✅ All good
    $('#insufficientFundsWarning').remove();
    $('#sendOtpBtn').prop('disabled', false);
});


// =====================================================
// 🔐 OTP SEND + VERIFY
// =====================================================
$('#sendOtpBtn').click(function () {
    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin"></i> Sending...');
    $.post('ajax/otp-handler.php', {
        action: 'send',
        csrf_token: $('meta[name="csrf-token"]').attr('content')
    }, function (r) {
        if (r.success) {
            toastr.success(r.message);
            $('#otpCode').prop('disabled', false);
            $('#verifyOtpBtn').prop('disabled', false);
        } else {
            toastr.error(r.message);
        }
    }, 'json').fail(function () {
        toastr.error('Failed to send OTP');
    }).always(function () {
        $btn.prop('disabled', false).html('<i class="anticon anticon-mail"></i> Send OTP');
    });
});

$('#verifyOtpBtn').click(function () {
    const code = $('#otpCode').val().trim();
    if (!code) return toastr.error('Please enter the OTP code.');

    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin"></i> Verifying...');
    $.post('ajax/otp-handler.php', {
        action: 'verify',
        otp_code: code,
        csrf_token: $('meta[name="csrf-token"]').attr('content')
    }, function (r) {
        if (r.success) {
            toastr.success(r.message);
            $('#withdrawalSubmitBtn').prop('disabled', false);
            $('#otpCode, #sendOtpBtn, #verifyOtpBtn').prop('disabled', true);
        } else {
            toastr.error(r.message);
        }
    }, 'json').fail(function () {
        toastr.error('OTP verification failed');
    }).always(function () {
        $btn.prop('disabled', false).html('<i class="anticon anticon-check-circle"></i> Verify OTP');
    });
});


// =====================================================
// 🧹 RESET OTP FIELDS ON MODAL CLOSE
// =====================================================
$('#newWithdrawalModal').on('hidden.bs.modal', function () {
    resetOtpFields();
});

function resetOtpFields() {
    $('#otpCode').val('').prop('disabled', true);
    $('#sendOtpBtn').prop('disabled', false);
    $('#verifyOtpBtn').prop('disabled', true);
    $('#withdrawalSubmitBtn').prop('disabled', true);
}

    // Refresh algorithm
    $('#refresh-algorithm').click(function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin"></i> Refreshing...');
        
        setTimeout(function() {
            $.ajax({
                url: 'ajax/get_recovery_status.php',
                method: 'GET',
                success: function(response) {
                    try {
                        var data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            if (data.recoveryPercentage !== undefined) {
                                $('.algorithm-progress .progress-bar').css('width', data.recoveryPercentage + '%');
                                $('.count[data-value="<?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>"]').text(data.recoveryPercentage + '%');
                            }
                            toastr.success('Status refreshed successfully');
                        } else {
                            toastr.error(data.message || 'Error refreshing status');
                        }
                    } catch (e) {
                        toastr.error('Error parsing server response');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Error communicating with server: ' + error);
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="anticon anticon-sync"></i> Refresh Status');
                }
            });
        }, 400);
    });

    // Background refresh for AI status and balance (optional)
    function bgRefresh() {
        $.ajax({
            url: 'ajax/bg_status.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    if (data.aiStatus) {
                        $('#aiStatusText').text(data.aiStatus);
                    }
                    if (data.lastScan) {
                        $('#lastScanText').text(data.lastScan);
                    }
                    if (data.balance !== undefined) {
                        var b = parseFloat(data.balance) || 0;
                        animateCount($('#balanceCounter')[0], parseFloat($('#balanceCounter').text().replace(/[^\d.-]/g,'')) || 0, b, 2, 600);
                    }
                }
            }
        }).always(function(){
            setTimeout(bgRefresh, 30000);
        });
    }
    // Start bgRefresh only if endpoint exists in your system; safe to comment out if not present.
    bgRefresh();

    // Password modal interactions (if present)
    <?php if ($passwordChangeRequired): ?>
    $('#newPassword').on('input', function() {
        const val = $(this).val();
        const $bar = $('#passwordStrengthBar');
        const $text = $('#passwordStrengthText');

        let score = 0;
        const req = {
            length: val.length >= 8,
            upper: /[A-Z]/.test(val),
            number: /[0-9]/.test(val),
            special: /[^A-Za-z0-9]/.test(val)
        };

        for (let key in req) {
            const $item = $('#req-' + key);
            if (req[key]) {
                $item.removeClass('text-danger').addClass('text-success')
                     .html('<i class="anticon anticon-check"></i> ' + $item.text().replace(/^[✓✗]\s*/, ''));
                score++;
            } else {
                $item.removeClass('text-success').addClass('text-danger')
                     .html('<i class="anticon anticon-close"></i> ' + $item.text().replace(/^[✓✗]\s*/, ''));
            }
        }

        const width = (score / 4) * 100;
        let colorClass, label;
        switch (score) {
            case 0:
            case 1: colorClass = 'bg-danger'; label = 'Weak'; break;
            case 2: colorClass = 'bg-warning'; label = 'Fair'; break;
            case 3: colorClass = 'bg-info'; label = 'Good'; break;
            case 4: colorClass = 'bg-success'; label = 'Strong'; break;
        }

        $bar.removeClass('bg-danger bg-warning bg-info bg-success')
            .addClass(colorClass)
            .css('width', width + '%');
        $text.text('Strength: ' + label);

        $('#confirmPassword').trigger('input');
    });

    $('#confirmPassword, #newPassword').on('input', function() {
        const newPass = $('#newPassword').val();
        const confirm = $('#confirmPassword').val();
        const $match = $('#passwordMatchText');

        if (!confirm) {
            $match.text('Waiting for input...').removeClass('text-success text-danger').addClass('text-muted');
            return;
        }

        if (confirm === newPass) {
            $match.text('Passwords match ✅').removeClass('text-danger text-muted').addClass('text-success');
        } else {
            $match.text('Passwords do not match ❌').removeClass('text-success text-muted').addClass('text-danger');
        }
    });

    $('#submitPasswordChange').click(function() {
        const currentPassword = $('#currentPassword').val();
        const newPassword = $('#newPassword').val();
        const confirmPassword = $('#confirmPassword').val();

        if (!currentPassword || !newPassword || !confirmPassword) {
            toastr.error('All fields are required');
            return;
        }
        if (newPassword !== confirmPassword) {
            toastr.error('New passwords do not match');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin"></i> Processing...');

        $.ajax({
            url: 'change_password.php',
            method: 'POST',
            dataType: 'json',
            data: {
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword,
                force_change: 1,
                csrf_token: '<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>'
            },
            success: function(data) {
                if (data.success) {
                    toastr.success(data.message || 'Password changed successfully');
                    $('#passwordChangeModal').modal('hide');
                    $('.modal-backdrop').remove();
                    setTimeout(function(){ location.reload(); }, 800);
                } else {
                    toastr.error(data.message || 'Error changing password');
                }
                $btn.prop('disabled', false).html('<i class="anticon anticon-save"></i> Change Password');
            },
            error: function(xhr, status, error) {
                toastr.error('Server error: ' + error);
                $btn.prop('disabled', false).html('<i class="anticon anticon-save"></i> Change Password');
            }
        });
    });
    <?php endif; ?>

    // Print receipt
    $('#printReceiptBtn').click(function(){ window.print(); });

});
</script>
</body>
</html>