<?php
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
            // STEP 4: Complete Onboarding
            // =========================================================
            case 4:
                // Mark onboarding completed
                $pdo->prepare("UPDATE user_onboarding SET completed = 1 WHERE user_id=?")->execute([$userId]);

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
 STEP 4: Complete Onboarding
============================================================ -->
<h4 class="mb-4">Complete Your Registration</h4>

<form method="post">
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


</div></div></div>



<style>
.form-group label {
    font-weight: 500;
}
.form-control {
    border-radius: 6px;
}
</style>


<?php require_once __DIR__ . '/footer.php'; ?>
