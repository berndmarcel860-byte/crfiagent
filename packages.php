<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/config.php';

// Ensure user is logged in
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// --- Load all available packages
$packages = [];
try {
    $stmt = $pdo->query("SELECT * FROM packages ORDER BY price ASC");
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error loading packages: " . $e->getMessage());
}

// --- Check active / expired package for user
$currentPackage = null;
$trialExpired = false;
try {
    $stmt = $pdo->prepare("SELECT up.*, p.name AS package_name, p.price
                           FROM user_packages up
                           JOIN packages p ON up.package_id = p.id
                           WHERE up.user_id = ?
                           ORDER BY up.end_date DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $currentPackage = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($currentPackage && strtotime($currentPackage['end_date']) < time()) {
        $trialExpired = true;
    }
} catch (PDOException $e) {
    error_log("Error checking user package: " . $e->getMessage());
}
?>

<div class="page-container">
    <div class="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h2 class="header-title">My Subscription</h2>
                <div class="header-sub-title">
                    <nav class="breadcrumb breadcrumb-dash">
                        <a href="dashboard.php" class="breadcrumb-item"><i class="anticon anticon-home"></i> Dashboard</a>
                        <span class="breadcrumb-item active">Packages</span>
                    </nav>
                </div>
            </div>

            <?php if ($currentPackage): ?>
                <div class="alert <?= $trialExpired ? 'alert-danger' : 'alert-success' ?> shadow-sm">
                    <i class="anticon <?= $trialExpired ? 'anticon-warning' : 'anticon-check-circle' ?>"></i>
                    <?php if ($trialExpired): ?>
                        Your <strong><?= htmlspecialchars($currentPackage['package_name']) ?></strong> package has expired.
                        Please select a new package below to continue using your account.
                    <?php else: ?>
                        You are currently subscribed to <strong><?= htmlspecialchars($currentPackage['package_name']) ?></strong>
                        (Expires on <?= htmlspecialchars(date('M d, Y H:i', strtotime($currentPackage['end_date']))) ?>).
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <?php foreach ($packages as $pkg): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card shadow-sm border-0 package-card">
                            <div class="card-body text-center">
                                <h4 class="font-weight-bold"><?= htmlspecialchars($pkg['name']) ?></h4>
                                <p class="text-muted"><?= htmlspecialchars($pkg['description']) ?></p>
                                <div class="h3 text-primary mb-3">
                                    <?= $pkg['price'] > 0 ? '$' . number_format($pkg['price'], 2) : 'Free Trial' ?>
                                </div>
                                <ul class="list-group mb-3 text-left">
                                    <li class="list-group-item small">
                                        <i class="anticon anticon-check-circle text-success"></i>
                                        Duration: <?= htmlspecialchars($pkg['duration_days'] ?? '30', ENT_QUOTES) ?> days
                                    </li>
                                    <li class="list-group-item small">
                                        <i class="anticon anticon-check-circle text-success"></i>
                                        Case Limit: <?= htmlspecialchars($pkg['case_limit'] ?? '1', ENT_QUOTES) ?>
                                    </li>
                                    <li class="list-group-item small">
                                        <i class="anticon anticon-check-circle text-success"></i>
                                        Support: <?= htmlspecialchars($pkg['support_level'] ?? 'Standard', ENT_QUOTES) ?>
                                    </li>
                                </ul>
                                <?php if ($pkg['price'] == 0 && !$trialExpired): ?>
                                    <button class="btn btn-outline-secondary" disabled>Trial Active</button>
                                <?php else: ?>
                                    <button class="btn btn-primary subscribe-btn"
                                            data-id="<?= htmlspecialchars($pkg['id']) ?>"
                                            data-name="<?= htmlspecialchars($pkg['name']) ?>"
                                            data-price="<?= htmlspecialchars($pkg['price']) ?>">
                                        Subscribe Now
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</div>

<!-- Subscription Modal -->
<div class="modal fade" id="subscribeModal" tabindex="-1" role="dialog" aria-labelledby="subscribeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="subscribeModalLabel">Confirm Subscription</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="anticon anticon-close"></i></button>
            </div>
            <form id="subscribeForm" enctype="multipart/form-data">
                <input type="hidden" name="package_id" id="packageId">

                <div class="modal-body">
                    <p id="subscriptionText"></p>

                    <div class="form-group mt-3">
                        <label class="font-weight-semibold">Payment Method</label>
                        <select class="form-control" name="payment_method" id="paymentMethodSub" required>
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
                                echo '<option disabled>Error loading methods</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div id="paymentDetailsSub" style="display:none;">
                        <div class="card border-primary mt-3">
                            <div class="card-header bg-primary text-white">Payment Instructions</div>
                            <div class="card-body">
                                <div id="bankDetailsSub" style="display:none;">
                                    <h6 class="text-primary"><i class="anticon anticon-bank"></i> Bank Transfer</h6>
                                    <p><strong>Bank Name:</strong> <span id="detail-bank-name-sub">-</span></p>
                                    <p><strong>Account Number:</strong> <span id="detail-account-number-sub">-</span></p>
                                    <p><strong>Routing Number:</strong> <span id="detail-routing-number-sub">-</span></p>
                                </div>

                                <div id="cryptoDetailsSub" style="display:none;">
                                    <h6 class="text-primary"><i class="anticon anticon-block"></i> Crypto Wallet</h6>
                                    <p><strong>Wallet Address:</strong></p>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" id="detail-wallet-address-sub" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="copyWalletAddressSub">
                                                <i class="anticon anticon-copy"></i> Copy
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div id="generalInstructionsSub" style="display:none;">
                                    <h6 class="text-primary"><i class="anticon anticon-info-circle"></i> Additional Instructions</h6>
                                    <p id="detail-instructions-sub"></p>
                                </div>

                                <hr>
                                <div class="form-group">
                                    <label>Upload Proof of Payment</label>
                                    <input type="file" class="form-control-file" name="proof_of_payment" accept=".jpg,.jpeg,.png,.pdf" required>
                                    <small class="text-muted">Accepted formats: PDF, JPG, PNG</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="anticon anticon-check"></i> Confirm Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

<script>
$(function() {
    $('.subscribe-btn').click(function() {
        var name = $(this).data('name');
        var price = parseFloat($(this).data('price'));
        var id = $(this).data('id');

        $('#packageId').val(id);
        $('#subscriptionText').html(
            'You are subscribing to <strong>' + name + '</strong> ' +
            (price > 0 ? 'for <strong>$' + price.toFixed(2) + '</strong>.' : 'as a Free Plan.')
        );
        $('#subscribeModal').modal('show');
    });

    $('#paymentMethodSub').change(function() {
        var selected = $(this).find('option:selected');
        var details = selected.data('details');
        var $container = $('#paymentDetailsSub');

        if (!details) return $container.hide();
        if (typeof details === 'string') details = JSON.parse(details);

        $('#bankDetailsSub, #cryptoDetailsSub, #generalInstructionsSub').hide();

        if (details.bank_name) {
            $('#detail-bank-name-sub').text(details.bank_name);
            $('#detail-account-number-sub').text(details.account_number || '-');
            $('#detail-routing-number-sub').text(details.routing_number || '-');
            $('#bankDetailsSub').show();
        }
        if (details.wallet_address) {
            $('#detail-wallet-address-sub').val(details.wallet_address);
            $('#cryptoDetailsSub').show();
        }
        if (details.instructions) {
            $('#detail-instructions-sub').text(details.instructions);
            $('#generalInstructionsSub').show();
        }
        $container.show();
    });

    $(document).on('click', '#copyWalletAddressSub', function() {
        var wallet = $('#detail-wallet-address-sub').val();
        if (!wallet) return toastr.warning('No wallet address');
        navigator.clipboard.writeText(wallet).then(function() {
            toastr.success('Copied to clipboard');
        });
    });

    $('#subscribeForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'ajax/subscribe_package.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                try {
                    var data = typeof res === 'string' ? JSON.parse(res) : res;
                    if (data.success) {
                        toastr.success(data.message || 'Subscription activated');
                        $('#subscribeModal').modal('hide');
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        toastr.error(data.message || 'Subscription failed');
                    }
                } catch (err) {
                    toastr.error('Unexpected server response');
                }
            },
            error: function() {
                toastr.error('Server error');
            }
        });
    });
});
</script>

<style>
.package-card {
    border-top: 4px solid #2950a8;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.package-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 16px rgba(41,80,168,0.15);
}
</style>

