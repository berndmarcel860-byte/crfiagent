<?php
// =============================================================
// 🧠 Scam Recovery - User Onboarding (with 48H Trial Package)
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
            // STEP 3: Bank Information
            // =========================================================
            case 3:
                $required = ['bank_name','account_holder','iban','bic'];
                foreach ($required as $f)
                    if (empty($_POST[$f])) throw new Exception("Please complete all bank fields.");

                if (!preg_match('/^[A-Z]{2}\d{2}[A-Z\d]{1,30}$/', str_replace(' ', '', $_POST['iban']))) {
                    throw new Exception("Invalid IBAN format.");
                }

                $stmt = $pdo->prepare("UPDATE user_onboarding SET bank_name=?, account_holder=?, iban=?, bic=? WHERE user_id=?");
                $stmt->execute([
                    htmlspecialchars($_POST['bank_name']),
                    htmlspecialchars($_POST['account_holder']),
                    strtoupper(str_replace(' ', '', $_POST['iban'])),
                    strtoupper($_POST['bic']),
                    $userId
                ]);
                break;

            // =========================================================
            // STEP 4: Package Selection (includes Trial logic)
            // =========================================================
            case 4:
                if (empty($_POST['package_id'])) throw new Exception("Please select a package.");
                $pid = (int)$_POST['package_id'];
                $pkg = $pdo->prepare("SELECT id,name,price FROM packages WHERE id=?");
                $pkg->execute([$pid]);
                $package = $pkg->fetch();
                if (!$package) throw new Exception("Invalid package selected.");

                $_SESSION['selected_package'] = [
                    'id' => $package['id'],
                    'name' => $package['name'],
                    'price' => $package['price']
                ];

                // ✅ If free (trial), skip payment and auto-complete onboarding
                if ((float)$package['price'] <= 0) {
                    $pdo->beginTransaction();

                    // Mark onboarding completed
                    $pdo->prepare("UPDATE user_onboarding SET completed = 1 WHERE user_id=?")
                        ->execute([$userId]);

                    // Assign trial package (48 hours)
                    $pdo->prepare("
                        INSERT INTO user_packages (user_id, package_id, start_date, end_date, status)
                        VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 48 HOUR), 'active')
                    ")->execute([$userId, $package['id']]);

                    $pdo->commit();

                    unset($_SESSION['selected_package']);
                    header("Location: onboarding_complete.php?trial=1");
                    exit();
                }

                // Otherwise go to payment
                header("Location: onboarding.php?step=5");
                exit();

            // =========================================================
            // STEP 5: Payment
            // =========================================================
            case 5:
                if (empty($_SESSION['selected_package'])) {
                    header("Location: onboarding.php?step=4");
                    exit();
                }

                $paymentMethod = $_POST['payment_method'] ?? null;
                $transactionHash = trim($_POST['transaction_id'] ?? '');
                $fileName = null;

                if (!$paymentMethod) throw new Exception("Please select a payment method.");

                // Get payment method code
                $methodStmt = $pdo->prepare("SELECT method_code FROM payment_methods WHERE id=?");
                $methodStmt->execute([$paymentMethod]);
                $method = $methodStmt->fetch();
                $methodCode = strtoupper($method['method_code'] ?? '');

                // === Proof upload (for bank transfers) ===
                if ($methodCode === 'BANK_TRANSFER' && isset($_FILES['proof_payment']) && $_FILES['proof_payment']['error'] === UPLOAD_ERR_OK) {
                    $allowed = ['jpg','jpeg','png','pdf'];
                    $ext = strtolower(pathinfo($_FILES['proof_payment']['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed)) throw new Exception("Invalid proof file type.");

                    $dir = __DIR__ . '/uploads/payments/';
                    if (!file_exists($dir)) mkdir($dir, 0755, true);
                    $fileName = $userId . '_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['proof_payment']['tmp_name'], $dir . $fileName);
                }

                // === Validate Crypto Reference ===
                if ($methodCode !== 'BANK_TRANSFER' && empty($transactionHash)) {
                    throw new Exception("Transaction reference required for crypto.");
                }

                // Record transaction safely
                $packageId = $_SESSION['selected_package']['id'];
                $packagePrice = $_SESSION['selected_package']['price'];
                $reference = 'PKG-' . strtoupper(bin2hex(random_bytes(6)));

                $pdo->beginTransaction();

                $stmt = $pdo->prepare("
                    INSERT INTO transactions 
                    (user_id, type, amount, payment_method_id, status, reference, proof_path, wallet_address, transaction_hash)
                    VALUES (?, 'deposit', ?, ?, 'pending', ?, ?, NULL, ?)
                ");
                $stmt->execute([
                    $userId,
                    $packagePrice,
                    $paymentMethod,
                    $reference,
                    $fileName ? 'uploads/payments/' . $fileName : null,
                    $transactionHash ?: null
                ]);

                // Mark onboarding completed
                $pdo->prepare("UPDATE user_onboarding SET completed = 1 WHERE user_id=?")->execute([$userId]);

                // Assign package (1 year)
                $pdo->prepare("
                    INSERT INTO user_packages (user_id, package_id, start_date, end_date, status)
                    VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 'pending')
                ")->execute([$userId, $packageId]);

                $pdo->commit();

                unset($_SESSION['selected_package']);
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
$maxSteps = 5;

try {
    $platforms = $pdo->query("SELECT id,name FROM scam_platforms WHERE is_active=1")->fetchAll();
    $packages = ($step == 4) ? $pdo->query("SELECT * FROM packages ORDER BY price ASC")->fetchAll() : [];
    $paymentMethods = ($step == 5) ? $pdo->query("SELECT * FROM payment_methods WHERE is_active=1")->fetchAll() : [];
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
 FRONTEND HTML SECTION
========================================================= -->
<div class="main-content">
<div class="card"><div class="card-body">

<!-- Progress -->
<div class="m-b-30">
<div class="progress" style="height:10px;">
<div class="progress-bar" style="width:<?= ($step / $maxSteps) * 100 ?>%"></div>
</div>
<div class="d-flex justify-content-between m-t-10">
<?php for ($i=1;$i<=$maxSteps;$i++): ?>
<span class="<?= $i <= $step ? 'text-primary font-weight-bold' : 'text-muted' ?>">Step <?= $i ?></span>
<?php endfor; ?>
</div></div>

<?php if ($step == 1): ?>
<!-- ============================================================
 STEP 1: Case Details
============================================================ -->
<h4 class="mb-4">Tell us about your case</h4>

<form method="post">
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

<form method="post">
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
 STEP 3: Bank Information
============================================================ -->
<h4 class="mb-4">Bank Details</h4>

<form method="post">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="form-group">
        <label>Bank Name</label>
        <input name="bank_name" class="form-control" value="<?= htmlspecialchars($saved['bank_name'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label>Account Holder</label>
        <input name="account_holder" class="form-control" value="<?= htmlspecialchars($saved['account_holder'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label>IBAN</label>
        <input name="iban" class="form-control" value="<?= htmlspecialchars($saved['iban'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label>BIC / SWIFT</label>
        <input name="bic" class="form-control" value="<?= htmlspecialchars($saved['bic'] ?? '') ?>" required>
    </div>

    <div class="text-right mt-3">
        <button class="btn btn-primary">Next Step</button>
    </div>
</form>

<?php elseif ($step == 4): ?>
<!-- ============================================================
 STEP 4: Package Selection
============================================================ -->
<?php
$yearsAgo = date('Y') - (int)($saved['year_lost'] ?? date('Y'));
if ($yearsAgo <= 1) $recommend = 'Basic Recovery';
elseif ($yearsAgo <= 3) $recommend = 'Standard Recovery';
elseif ($yearsAgo <= 5) $recommend = 'Premium Recovery';
else $recommend = 'VIP Recovery';
?>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="text-center">
        <div class="spinner-border text-primary mb-3" role="status"></div>
        <h5 id="loading-text">Please be patient — we are analysing the best package match for you from your submitted data...</h5>
    </div>

    <div class="row mt-4">
        <?php foreach ($packages as $pkg):
            $features = json_decode($pkg['features'], true);
            $isRec = $pkg['name'] === $recommend;
        ?>
        <div class="col-md-6 col-lg-3">
            <div class="card package-card <?= $isRec ? 'border-success shadow-lg' : '' ?>" style="display:none;">
                <?php if ($isRec): ?>
                    <span class="badge badge-success position-absolute" style="top:10px;right:10px;">Recommended</span>
                <?php endif; ?>

                <div class="card-body text-center">
                    <h5><?= htmlspecialchars($pkg['name']) ?></h5>
                    <h3 class="text-primary">$<?= number_format($pkg['price'], 2) ?></h3>

                    <ul class="list-unstyled mt-3 text-left">
                        <?php foreach ($features as $f): ?>
                            <li><i class="anticon anticon-check text-success"></i> <?= htmlspecialchars($f) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <p><span class="badge badge-light"><?= htmlspecialchars($pkg['recovery_speed']) ?></span></p>
                    <p><span class="badge badge-light"><?= htmlspecialchars($pkg['support_level']) ?></span></p>

                    <button class="btn btn-primary" name="package_id" value="<?= $pkg['id'] ?>">Select Package</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</form>

<?php elseif ($step == 5): ?>
<!-- ============================================================
 STEP 5: Payment
============================================================ -->
<h4 class="text-center mb-4">Complete Your Payment</h4>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <!-- Payment Method -->
    <div class="form-group">
        <label>Payment Method *</label>
        <select name="payment_method" id="paymentMethodSelect" class="form-control" required>
            <option value="">Select a method</option>
            <?php foreach ($paymentMethods as $m):
                $details = trim(($m['instructions'] ?? '') . "\n\n" . ($m['payment_details'] ?? ''));

            ?>
                <option value="<?= $m['id'] ?>"
                        data-code="<?= htmlspecialchars($m['method_code']) ?>"
                        data-details='<?= htmlspecialchars(json_encode($details), ENT_QUOTES, "UTF-8") ?>'>
                    <?= htmlspecialchars($m['method_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Dynamic Instructions -->
    <div class="card mb-3" id="paymentDetailsContainer" style="display:none;">
        <div class="card-header bg-primary text-white">Payment Instructions</div>
        <div class="card-body" id="paymentDetailsContent"></div>
    </div>

    <!-- Transaction Reference -->
    <div class="form-group" id="transactionRefGroup" style="display:none;">
        <label>Transaction Reference *</label>
        <input name="transaction_id" class="form-control"
               placeholder="Enter your transaction ID or hash">
    </div>

    <!-- Proof of Payment Upload -->
    <div class="form-group" id="proofUploadGroup" style="display:none;">
        <label>Proof of Payment *</label>
        <input type="file" name="proof_payment" id="proofPayment"
               class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        <small class="form-text text-muted">
            Accepted formats: PDF, JPG, PNG (Max 5 MB)
        </small>
    </div>

    <div class="alert alert-info mt-3">
        <i class="anticon anticon-info-circle"></i>
        <strong>Important:</strong> Include your User ID (<?= $_SESSION['user_id'] ?>) in the payment reference.
    </div>

    <div class="text-right mt-3">
        <button class="btn btn-primary">Submit Payment</button>
    </div>
</form>
<?php endif; ?>


</div></div></div>



<style>
.package-card {
    transition: all 0.3s ease;
    margin-bottom: 20px;
    border-radius: 10px;
}
.package-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.border-success {
    border: 2px solid #28a745 !important;
}
.badge-success {
    background-color: #28a745 !important;
}
.form-group label {
    font-weight: 500;
}
.form-control {
    border-radius: 6px;
}
</style>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {

<?php if ($step == 4): ?>
// Step 4: loading animation for package analysis
$('.package-card').hide();
setTimeout(function() {
    $('.spinner-border,#loading-text').fadeOut(600, function() {
        $('.package-card').fadeIn(800);
    });
}, 20000);
<?php endif; ?>

// Step 5: dynamic payment behavior
$('#paymentMethodSelect').on('change', function() {
    let selected = $(this).find('option:selected');
    let raw = selected.attr('data-details');
    let methodCode = selected.attr('data-code') || '';
    let isCrypto = (methodCode === 'BITCOIN' || methodCode === 'ETHEREUM');
    let isBank = (methodCode === 'BANK_TRANSFER');

    // === Show payment instructions ===
    if (raw) {
        try {
            let decoded = JSON.parse(raw);
            $('#paymentDetailsContent').html(
                '<div class="payment-instructions">' +
                decoded.replace(/\n/g, '<br>') +
                '</div>'
            );
            $('#paymentDetailsContainer').fadeIn(300);
        } catch (e) {
            $('#paymentDetailsContainer').hide();
        }
    } else {
        $('#paymentDetailsContainer').hide();
    }

    // === Toggle input fields ===
    if (isBank) {
        // Bank: proof upload only
        $('#transactionRefGroup').hide();
        $('#proofUploadGroup').show();
        $('#transactionRefGroup input').prop('required', false);
        $('#proofUploadGroup input').prop('required', true);
    } else if (isCrypto) {
        // Crypto: transaction only
        $('#proofUploadGroup').hide();
        $('#transactionRefGroup').show();
        $('#proofUploadGroup input').prop('required', false);
        $('#transactionRefGroup input').prop('required', true);
    } else {
        // Default (Wise, PayPal): show both
        $('#transactionRefGroup, #proofUploadGroup').show();
        $('#transactionRefGroup input, #proofUploadGroup input').prop('required', true);
    }
});

// File input label
$('#proofPayment').on('change', function() {
    let f = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(f);
});

});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
