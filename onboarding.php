<?php
/**
 * User Onboarding Wizard
 * 
 * UPDATED: 2026-02-19
 * Branch: copilot/sub-pr-1
 * 
 * Features:
 * - Multi-step registration wizard
 * - Case details collection
 * - Address information
 * - Payment method setup (Bank & Crypto support)
 * - Modern responsive card-based design
 * 
 * Security: CSRF protection, input validation, PDO prepared statements
 */
// =============================================================
// 🧠 Scam Recovery - User Onboarding
// =============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

// === CSRF TOKEN ===
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// === Redirect if not logged in ===
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// === Check if onboarding already completed ===
try {
    $stmt = $pdo->prepare("SELECT completed FROM user_onboarding WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $onboarding = $stmt->fetch();
    if ($onboarding && $onboarding['completed']) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    die("System error: " . $e->getMessage());
}

// === Handle Form Submissions ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $step = (int)($_GET['step'] ?? 1);

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Invalid security token.";
        header("Location: onboarding.php?step=$step");
        exit();
    }

    try {
        switch ($step) {

            // =========================================================
            // STEP 1: Case details
            // =========================================================
            case 1:
                $lostAmount = filter_input(INPUT_POST, 'lost_amount', FILTER_VALIDATE_FLOAT);
                $yearLost = filter_input(INPUT_POST, 'year_lost', FILTER_VALIDATE_INT);
                $description = trim($_POST['description'] ?? '');
                $platforms = isset($_POST['platforms']) ? array_map('intval', $_POST['platforms']) : [];

                if (!$lostAmount || !$yearLost || empty($description) || empty($platforms)) {
                    throw new Exception("Please complete all required fields.");
                }

                $stmt = $pdo->prepare("
                    INSERT INTO user_onboarding (user_id, lost_amount, platforms, year_lost, case_description)
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        lost_amount=VALUES(lost_amount),
                        platforms=VALUES(platforms),
                        year_lost=VALUES(year_lost),
                        case_description=VALUES(case_description)
                ");
                $stmt->execute([$userId, $lostAmount, json_encode($platforms), $yearLost, $description]);
                break;

            // =========================================================
            // STEP 2: Address Information
            // =========================================================
            case 2:
                $required = ['country','street','postal_code','state'];
                foreach ($required as $f)
                    if (empty($_POST[$f])) throw new Exception("Please complete all address fields.");

                $stmt = $pdo->prepare("UPDATE user_onboarding SET country=?, street=?, postal_code=?, state=? WHERE user_id=?");
                $stmt->execute([
                    htmlspecialchars($_POST['country']),
                    htmlspecialchars($_POST['street']),
                    htmlspecialchars($_POST['postal_code']),
                    htmlspecialchars($_POST['state']),
                    $userId
                ]);
                break;

            // =========================================================
            // STEP 3: Payment Methods (Bank AND Crypto - BOTH REQUIRED)
            // =========================================================
            case 3:
                // Validate BANK ACCOUNT fields
                $bankRequired = ['bank_name', 'account_holder', 'iban', 'bic'];
                foreach ($bankRequired as $f) {
                    if (empty($_POST[$f])) {
                        throw new Exception("Please complete all bank account fields. Both bank and crypto are required.");
                    }
                }
                
                // Validate IBAN format
                if (!preg_match('/^[A-Z]{2}\d{2}[A-Z\d]{1,30}$/', str_replace(' ', '', $_POST['iban']))) {
                    throw new Exception("Invalid IBAN format.");
                }
                
                // Validate CRYPTOCURRENCY fields
                $cryptoRequired = ['cryptocurrency', 'network', 'wallet_address'];
                foreach ($cryptoRequired as $f) {
                    if (empty($_POST[$f])) {
                        throw new Exception("Please complete all cryptocurrency fields. Both bank and crypto are required.");
                    }
                }
                
                // Save BOTH payment methods
                $stmt = $pdo->prepare("UPDATE user_onboarding SET 
                    payment_type=?, 
                    bank_name=?, 
                    account_holder=?, 
                    iban=?, 
                    bic=?,
                    cryptocurrency=?, 
                    network=?, 
                    wallet_address=? 
                    WHERE user_id=?");
                    
                $stmt->execute([
                    'both', // Payment type is now 'both'
                    htmlspecialchars($_POST['bank_name']),
                    htmlspecialchars($_POST['account_holder']),
                    strtoupper(str_replace(' ', '', $_POST['iban'])),
                    strtoupper($_POST['bic']),
                    htmlspecialchars($_POST['cryptocurrency']),
                    htmlspecialchars($_POST['network']),
                    htmlspecialchars($_POST['wallet_address']),
                    $userId
                ]);
                
                // Insert bank account into user_payment_methods
                $bankName = htmlspecialchars($_POST['bank_name']);
                $accountHolder = htmlspecialchars($_POST['account_holder']);
                $iban = strtoupper(str_replace(' ', '', $_POST['iban']));
                $bic = strtoupper($_POST['bic']);
                
                $stmt_bank = $pdo->prepare("INSERT INTO user_payment_methods 
                    (user_id, type, payment_method, label, bank_name, account_holder, iban, bic, 
                     is_default, verification_status, created_at) 
                    VALUES (?, 'fiat', 'bank_transfer', ?, ?, ?, ?, ?, 1, 'pending', NOW())");
                $stmt_bank->execute([$userId, $bankName, $bankName, $accountHolder, $iban, $bic]);
                
                // Insert crypto wallet into user_payment_methods
                $cryptocurrency = htmlspecialchars($_POST['cryptocurrency']);
                $network = htmlspecialchars($_POST['network']);
                $walletAddress = htmlspecialchars($_POST['wallet_address']);
                
                $stmt_crypto = $pdo->prepare("INSERT INTO user_payment_methods 
                    (user_id, type, payment_method, label, cryptocurrency, network, wallet_address, 
                     is_default, verification_status, verification_requested_at, created_at) 
                    VALUES (?, 'crypto', ?, ?, ?, ?, ?, 1, 'pending', NOW(), NOW())");
                $stmt_crypto->execute([$userId, strtolower($cryptocurrency), $cryptocurrency, 
                    $cryptocurrency, $network, $walletAddress]);
                
                break;

            // =========================================================
            // STEP 4: Complete Onboarding
            // =========================================================
            case 4:
                // Mark onboarding completed
                $pdo->prepare("UPDATE user_onboarding SET completed = 1 WHERE user_id=?")->execute([$userId]);

                // =========================================================
                // Send Onboarding Completion Email with Payment Details
                // =========================================================
                try {
                    // Get user details
                    $stmt_user = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
                    $stmt_user->execute([$userId]);
                    $user = $stmt_user->fetch();
                    
                    // Get platform settings for footer
                    $stmt_settings = $pdo->query("SELECT * FROM settings WHERE id = 1");
                    $settings = $stmt_settings->fetch();
                    
                    // Get onboarding data with payment details
                    $stmt_onboarding = $pdo->prepare("SELECT * FROM user_onboarding WHERE user_id = ?");
                    $stmt_onboarding->execute([$userId]);
                    $onboarding_data = $stmt_onboarding->fetch();
                    
                    // Get email template from database
                    $stmt_template = $pdo->prepare("SELECT * FROM email_templates WHERE name = 'onboarding_completed'");
                    $stmt_template->execute();
                    $template = $stmt_template->fetch();
                    
                    if ($template && $user && $onboarding_data) {
                        // Prepare email variables
                        $variables = [
                            'user_name' => $user['name'] ?? 'Valued Customer',
                            'company_name' => $settings['site_name'] ?? 'Crypto Recovery',
                            'bank_name' => $onboarding_data['bank_name'] ?? 'N/A',
                            'account_holder' => $onboarding_data['account_holder'] ?? 'N/A',
                            'iban' => $onboarding_data['iban'] ?? 'N/A',
                            'bic' => $onboarding_data['bic'] ?? 'N/A',
                            'cryptocurrency' => $onboarding_data['cryptocurrency'] ?? 'N/A',
                            'network' => $onboarding_data['network'] ?? 'N/A',
                            'wallet_address' => $onboarding_data['wallet_address'] ?? 'N/A',
                            'dashboard_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/index.php",
                            'support_email' => $settings['support_email'] ?? 'support@example.com',
                            'support_phone' => $settings['support_phone'] ?? '+1 (555) 123-4567',
                            'company_address' => $settings['company_address'] ?? 'Main Street 123',
                            'company_city' => $settings['company_city'] ?? 'Berlin',
                            'company_country' => $settings['company_country'] ?? 'Germany',
                            'website_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}",
                            'terms_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/terms.php",
                            'privacy_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/privacy.php",
                            'current_year' => date('Y')
                        ];
                        
                        // Replace variables in template
                        $email_subject = $template['subject'];
                        $email_body = $template['body'];
                        
                        foreach ($variables as $key => $value) {
                            $email_subject = str_replace('{{'.$key.'}}', $value, $email_subject);
                            $email_body = str_replace('{{'.$key.'}}', $value, $email_body);
                        }
                        
                        // Setup email headers
                        $headers = "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                        $headers .= "From: " . ($settings['site_name'] ?? 'Crypto Recovery') . " <" . ($settings['from_email'] ?? 'noreply@example.com') . ">\r\n";
                        $headers .= "Reply-To: " . ($settings['support_email'] ?? 'support@example.com') . "\r\n";
                        
                        // Send the email
                        $mail_sent = mail($user['email'], $email_subject, $email_body, $headers);
                        
                        // Log email sending
                        if ($mail_sent) {
                            $stmt_log = $pdo->prepare("INSERT INTO email_logs (user_id, email_type, recipient, subject, sent_at, status) VALUES (?, 'onboarding_completed', ?, ?, NOW(), 'sent')");
                            $stmt_log->execute([$userId, $user['email'], $email_subject]);
                        } else {
                            $stmt_log = $pdo->prepare("INSERT INTO email_logs (user_id, email_type, recipient, subject, sent_at, status, error_message) VALUES (?, 'onboarding_completed', ?, ?, NOW(), 'failed', 'Mail function returned false')");
                            $stmt_log->execute([$userId, $user['email'], $email_subject]);
                        }
                    }
                } catch (Exception $e) {
                    // Log error but don't stop onboarding completion
                    error_log("Onboarding email error: " . $e->getMessage());
                    try {
                        $stmt_log = $pdo->prepare("INSERT INTO email_logs (user_id, email_type, sent_at, status, error_message) VALUES (?, 'onboarding_completed', NOW(), 'error', ?)");
                        $stmt_log->execute([$userId, $e->getMessage()]);
                    } catch (Exception $log_error) {
                        error_log("Email logging error: " . $log_error->getMessage());
                    }
                }

                header("Location: onboarding_complete.php");
                exit();
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['error'] = $e->getMessage();
        header("Location: onboarding.php?step=$step");
        exit();
    }

    header("Location: onboarding.php?step=" . ($step + 1));
    exit();
}

// === Load Data for Steps ===
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$maxSteps = 4;

try {
    $platforms = $pdo->query("SELECT id,name FROM scam_platforms WHERE is_active=1")->fetchAll();
    $data = $pdo->prepare("SELECT * FROM user_onboarding WHERE user_id=?");
    $data->execute([$_SESSION['user_id']]);
    $saved = $data->fetch();
} catch (PDOException $e) {
    die("Database error.");
}

require_once __DIR__ . '/header.php';
if (!empty($_SESSION['error'])) {
    echo '<div class="alert alert-danger">'.htmlspecialchars($_SESSION['error']).'</div>';
    unset($_SESSION['error']);
}
?>

<!-- =========================================================
 FRONTEND HTML SECTION - Modern Responsive Design
========================================================= -->
<div class="main-content">
<div class="container" style="max-width: 800px;">
<div class="card shadow-lg" style="border-radius: 15px; border: none;">
<div class="card-body p-4">

<!-- Modern Progress Indicator -->
<div class="mb-5">
<div class="progress" style="height: 8px; border-radius: 10px;">
<div class="progress-bar bg-gradient-primary" style="width:<?= ($step / $maxSteps) * 100 ?>%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
</div>
<div class="d-flex justify-content-between mt-3">
<?php 
$stepIcons = ['📋', '🏠', '💳', '✅'];
for ($i=1;$i<=$maxSteps;$i++): 
    $active = $i <= $step;
?>
<div class="text-center">
    <div class="step-icon mb-2" style="font-size: 2rem;"><?= $stepIcons[$i-1] ?></div>
    <span class="<?= $active ? 'text-primary font-weight-bold' : 'text-muted' ?>" style="font-size: 0.9rem;">
        Step <?= $i ?>
    </span>
</div>
<?php endfor; ?>
</div></div>

<?php if ($step == 1): ?>
<!-- ============================================================
 STEP 1: Case Details
============================================================ -->
<h4 class="mb-4">Tell us about your case</h4>

<form method="post" action="onboarding.php?step=<?= $step ?>">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <!-- Lost Amount -->
    <div class="form-group">
        <label>Lost Amount (USD)</label>
        <select name="lost_amount" class="form-control" required>
            <option value="">Select amount</option>
            <?php
            $amounts = [
                1000 => 'Less than $1,000',
                5000 => '$1,000 - $5,000',
                10000 => '$5,000 - $10,000',
                25000 => '$10,000 - $25,000',
                50000 => '$25,000 - $50,000',
                100000 => '$50,000 - $100,000',
                250000 => '$100,000 - $250,000',
                500000 => 'More than $250,000'
            ];
            foreach ($amounts as $v => $label):
                $sel = ($saved['lost_amount'] ?? '') == $v ? 'selected' : '';
            ?>
                <option value="<?= $v ?>" <?= $sel ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Platforms -->
    <div class="form-group">
        <label>Platforms Used</label>
        <select name="platforms[]" class="form-control" multiple required>
            <?php
            $chosen = !empty($saved['platforms']) ? json_decode($saved['platforms'], true) : [];
            foreach ($platforms as $p):
                $sel = in_array($p['id'], $chosen) ? 'selected' : '';
            ?>
                <option value="<?= $p['id'] ?>" <?= $sel ?>>
                    <?= htmlspecialchars($p['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Year Lost -->
    <div class="form-group">
        <label>Year of Loss</label>
        <select name="year_lost" class="form-control" required>
            <option value="">Select year</option>
            <?php for ($y = date('Y'); $y >= 2000; $y--):
                $sel = ($saved['year_lost'] ?? '') == $y ? 'selected' : ''; ?>
                <option value="<?= $y ?>" <?= $sel ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <!-- Description -->
    <div class="form-group">
        <label>Brief Description</label>
        <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($saved['case_description'] ?? '') ?></textarea>
    </div>

    <div class="text-right">
        <button class="btn btn-primary">Next Step</button>
    </div>
</form>

<?php elseif ($step == 2): ?>
<!-- ============================================================
 STEP 2: Address Information
============================================================ -->
<h4 class="mb-4">Your Address</h4>

<form method="post" action="onboarding.php?step=<?= $step ?>">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="form-group">
        <label>Country</label>
        <input name="country" class="form-control" value="<?= htmlspecialchars($saved['country'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label>Street</label>
        <input name="street" class="form-control" value="<?= htmlspecialchars($saved['street'] ?? '') ?>" required>
    </div>

    <div class="form-row">
        <div class="col-md-6">
            <label>Postal Code</label>
            <input name="postal_code" class="form-control" value="<?= htmlspecialchars($saved['postal_code'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
            <label>State / Province</label>
            <input name="state" class="form-control" value="<?= htmlspecialchars($saved['state'] ?? '') ?>" required>
        </div>
    </div>

    <div class="text-right mt-3">
        <button class="btn btn-primary">Next Step</button>
    </div>
</form>

<?php elseif ($step == 3): ?>
<!-- ============================================================
 STEP 3: Payment Methods (Bank Account AND Cryptocurrency - BOTH REQUIRED)
============================================================ -->
<h4 class="mb-4" style="color: #667eea; font-weight: 600;">💳 Add Payment Methods</h4>

<div class="alert alert-danger mb-4">
    <i class="fas fa-exclamation-circle"></i>
    <strong>⚠️ BOTH Required:</strong> You must provide BOTH a bank account AND a cryptocurrency wallet for receiving recovered funds. Both will be verified before you can receive payments.
</div>

<form method="post" action="onboarding.php?step=<?= $step ?>" id="paymentForm">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <!-- Bank Account Section (REQUIRED) -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
            <h5 class="mb-0">
                <i class="fas fa-university mr-2"></i>
                🏦 Bank Account (Required)
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="form-group">
                <label class="font-weight-bold">Bank Name <span class="text-danger">*</span></label>
                <input type="text" name="bank_name" class="form-control form-control-lg" 
                       value="<?= htmlspecialchars($saved['bank_name'] ?? '') ?>" 
                       placeholder="e.g., Chase Bank, Deutsche Bank"
                       required>
            </div>

            <div class="form-group">
                <label class="font-weight-bold">Account Holder <span class="text-danger">*</span></label>
                <input type="text" name="account_holder" class="form-control form-control-lg" 
                       value="<?= htmlspecialchars($saved['account_holder'] ?? '') ?>" 
                       placeholder="Full name as it appears on bank account"
                       required>
            </div>

            <div class="form-group">
                <label class="font-weight-bold">IBAN <span class="text-danger">*</span></label>
                <input type="text" name="iban" class="form-control form-control-lg" 
                       value="<?= htmlspecialchars($saved['iban'] ?? '') ?>" 
                       placeholder="DE89 3704 0044 0532 0130 00"
                       pattern="[A-Z]{2}\d{2}[A-Z\d]{1,30}"
                       required>
                <small class="text-muted">International Bank Account Number</small>
            </div>

            <div class="form-group">
                <label class="font-weight-bold">BIC / SWIFT <span class="text-danger">*</span></label>
                <input type="text" name="bic" class="form-control form-control-lg" 
                       value="<?= htmlspecialchars($saved['bic'] ?? '') ?>" 
                       placeholder="COBADEFFXXX"
                       required>
                <small class="text-muted">Bank Identifier Code</small>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Note:</strong> Your bank account will be verified before receiving funds.
            </div>
        </div>
    </div>

    <!-- Cryptocurrency Section (REQUIRED) -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
            <h5 class="mb-0">
                <i class="fab fa-bitcoin mr-2"></i>
                💰 Cryptocurrency Wallet (Required)
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="form-group">
                <label class="font-weight-bold">Cryptocurrency <span class="text-danger">*</span></label>
                <select name="cryptocurrency" class="form-control form-control-lg" required>
                    <option value="">Select cryptocurrency</option>
                    <option value="BTC" <?= ($saved['cryptocurrency'] ?? '') == 'BTC' ? 'selected' : '' ?>>Bitcoin (BTC)</option>
                    <option value="ETH" <?= ($saved['cryptocurrency'] ?? '') == 'ETH' ? 'selected' : '' ?>>Ethereum (ETH)</option>
                    <option value="USDT" <?= ($saved['cryptocurrency'] ?? '') == 'USDT' ? 'selected' : '' ?>>Tether (USDT)</option>
                    <option value="USDC" <?= ($saved['cryptocurrency'] ?? '') == 'USDC' ? 'selected' : '' ?>>USD Coin (USDC)</option>
                    <option value="BNB" <?= ($saved['cryptocurrency'] ?? '') == 'BNB' ? 'selected' : '' ?>>Binance Coin (BNB)</option>
                    <option value="ADA" <?= ($saved['cryptocurrency'] ?? '') == 'ADA' ? 'selected' : '' ?>>Cardano (ADA)</option>
                    <option value="LTC" <?= ($saved['cryptocurrency'] ?? '') == 'LTC' ? 'selected' : '' ?>>Litecoin (LTC)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="font-weight-bold">Network <span class="text-danger">*</span></label>
                <select name="network" class="form-control form-control-lg" required>
                    <option value="">Select network</option>
                    <option value="Bitcoin Network" <?= ($saved['network'] ?? '') == 'Bitcoin Network' ? 'selected' : '' ?>>Bitcoin Network</option>
                    <option value="Ethereum (ERC-20)" <?= ($saved['network'] ?? '') == 'Ethereum (ERC-20)' ? 'selected' : '' ?>>Ethereum (ERC-20)</option>
                    <option value="Tron (TRC-20)" <?= ($saved['network'] ?? '') == 'Tron (TRC-20)' ? 'selected' : '' ?>>Tron (TRC-20)</option>
                    <option value="Binance Smart Chain (BEP-20)" <?= ($saved['network'] ?? '') == 'Binance Smart Chain (BEP-20)' ? 'selected' : '' ?>>Binance Smart Chain (BEP-20)</option>
                    <option value="Polygon Network" <?= ($saved['network'] ?? '') == 'Polygon Network' ? 'selected' : '' ?>>Polygon Network</option>
                    <option value="Solana Network" <?= ($saved['network'] ?? '') == 'Solana Network' ? 'selected' : '' ?>>Solana Network</option>
                </select>
                <small class="text-muted">Choose the blockchain network for your wallet</small>
            </div>

            <div class="form-group">
                <label class="font-weight-bold">Wallet Address <span class="text-danger">*</span></label>
                <input type="text" name="wallet_address" class="form-control form-control-lg" 
                       value="<?= htmlspecialchars($saved['wallet_address'] ?? '') ?>" 
                       placeholder="0xabcd1234..." 
                       style="font-family: monospace;"
                       required>
                <small class="text-muted">Your cryptocurrency wallet address</small>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-shield-alt"></i>
                <strong>Note:</strong> Your wallet will be verified through a Satoshi test (small test transaction).
            </div>
        </div>
    </div>

    <div class="text-right mt-4">
        <button type="submit" class="btn btn-primary btn-lg px-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 25px;">
            Complete Setup <i class="fas fa-check ml-2"></i>
        </button>
    </div>
</form>

<?php elseif ($step == 4): ?>
<!-- ============================================================
 STEP 4: Complete Onboarding
============================================================ -->
<h4 class="mb-4">Complete Your Registration</h4>

<form method="post" action="onboarding.php?step=<?= $step ?>">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="alert alert-success">
        <i class="anticon anticon-check-circle"></i>
        <strong>Almost Done!</strong> Click the button below to complete your onboarding process.
    </div>

    <div class="text-right mt-3">
        <button class="btn btn-primary">Complete Registration</button>
    </div>
</form>
<?php endif; ?>


</div></div></div></div>


<style>
/* Modern Onboarding Styles */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important;
}

.form-control-lg {
    border-radius: 10px;
    border: 2px solid #e1e8ed;
    padding: 12px 20px;
    font-size: 1rem;
}

.form-control-lg:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.nav-tabs {
    border: none;
}

.nav-tabs .nav-link {
    transition: all 0.3s ease;
    font-size: 1.1rem;
    padding: 12px 24px;
}

.nav-tabs .nav-link:hover {
    border-bottom: 3px solid #764ba2 !important;
    color: #764ba2 !important;
}

.step-icon {
    transition: transform 0.3s ease;
}

.step-icon:hover {
    transform: scale(1.2);
}

.btn-primary {
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.alert {
    border-radius: 10px;
    border-left: 4px solid;
}

.alert-info {
    border-left-color: #17a2b8;
    background-color: #d1ecf1;
}

.alert-warning {
    border-left-color: #ffc107;
    background-color: #fff3cd;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.form-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.text-muted {
    font-size: 0.875rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem !important;
    }
    
    .step-icon {
        font-size: 1.5rem !important;
    }
    
    .nav-tabs .nav-link {
        font-size: 0.9rem;
        padding: 10px 16px;
    }
}
</style>


<?php require_once __DIR__ . '/footer.php'; ?>