<?php include 'header.php'; 

// Check if user has any verified payment methods
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM user_payment_methods 
                       WHERE user_id = ? AND type = 'crypto' AND verification_status = 'verified'");
$stmt->execute([$_SESSION['user_id']]);
$verifiedMethods = $stmt->fetch();
$hasVerifiedMethods = $verifiedMethods['count'] > 0;

// Get user's verified payment methods for withdrawal
$stmt = $pdo->prepare("SELECT id, payment_method, type, label, cryptocurrency, network, 
                       wallet_address, bank_name, account_holder, verification_status
                       FROM user_payment_methods 
                       WHERE user_id = ? AND verification_status = 'verified'
                       ORDER BY is_default DESC, created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$userPaymentMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Main Content START -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Withdrawal Requests</h4>
                        <div class="float-right">
                            <?php if ($hasVerifiedMethods): ?>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#newWithdrawalModal">
                                <i class="anticon anticon-plus"></i> New Withdrawal
                            </button>
                            <?php else: ?>
                            <button class="btn btn-warning" data-toggle="modal" data-target="#noVerifiedMethodsModal">
                                <i class="anticon anticon-warning"></i> New Withdrawal
                            </button>
                            <?php endif; ?>
                            <button class="btn btn-success ml-2" id="refreshWithdrawals">
                                <i class="anticon anticon-reload"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!$hasVerifiedMethods): ?>
                        <div class="alert alert-warning">
                            <h5><i class="anticon anticon-exclamation-circle"></i> <strong>Verification Required</strong></h5>
                            <p>You need at least one <strong>verified cryptocurrency wallet</strong> to make withdrawals.</p>
                            <p class="mb-0">
                                <a href="payment-methods.php" class="btn btn-sm btn-primary">
                                    <i class="anticon anticon-credit-card"></i> Add & Verify Payment Method
                                </a>
                                <a href="satoshi-test-guide.php" class="btn btn-sm btn-info ml-2">
                                    <i class="anticon anticon-question-circle"></i> Learn About Verification
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>
                        <div class="alert alert-danger d-none" id="withdrawalError"></div>
                        <div class="alert alert-info">
                            <strong>Current Balance:</strong> 
                            <span id="currentBalance">$<?= number_format($user['balance'], 2) ?></span>
                        </div>
                        <div class="table-responsive">
                            <table id="withdrawalsTable" class="table table-bordered nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Request Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Main Content END -->

<!-- New Withdrawal Modal -->
<div class="modal fade" id="newWithdrawalModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Withdrawal Request</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <form id="withdrawalForm">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Amount (USD)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" name="amount" min="10" step="0.01" required>
                        </div>
                        <small class="form-text text-muted">Minimum withdrawal: $10.00 | Available: <span id="currentBalanceDisplay">$<?= number_format($user['balance'], 2) ?></span></small>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select class="form-control" name="payment_method_id" id="paymentMethodSelect" required>
                            <option value="">Select Verified Payment Method</option>
                            <?php foreach ($userPaymentMethods as $method): ?>
                                <?php if ($method['type'] === 'crypto'): ?>
                                    <option value="<?= $method['id'] ?>" 
                                            data-type="crypto"
                                            data-cryptocurrency="<?= htmlspecialchars($method['cryptocurrency']) ?>"
                                            data-network="<?= htmlspecialchars($method['network']) ?>"
                                            data-address="<?= htmlspecialchars($method['wallet_address']) ?>">
                                        <?= htmlspecialchars($method['label'] ?? $method['cryptocurrency']) ?> 
                                        (<?= htmlspecialchars($method['cryptocurrency']) ?> - <?= htmlspecialchars($method['network']) ?>)
                                        - ✓ Verified
                                    </option>
                                <?php else: ?>
                                    <option value="<?= $method['id'] ?>" 
                                            data-type="fiat"
                                            data-bank="<?= htmlspecialchars($method['bank_name']) ?>"
                                            data-holder="<?= htmlspecialchars($method['account_holder']) ?>">
                                        <?= htmlspecialchars($method['label'] ?? $method['bank_name']) ?> 
                                        (<?= htmlspecialchars($method['payment_method']) ?>)
                                        - ✓ Verified
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">
                            Only verified payment methods are shown. 
                            <a href="payment-methods.php" target="_blank">Add more payment methods</a>
                        </small>
                    </div>
                    <div class="form-group" id="paymentDetailsGroup">
                        <label>Payment Details</label>
                        <div id="selectedMethodDetails" class="alert alert-info" style="display: none;">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        <textarea class="form-control" name="payment_details" id="paymentDetailsField" rows="3" 
                                  placeholder="Payment details will be auto-filled from your selected method" readonly></textarea>
                        <small class="form-text text-muted">Details from your verified payment method</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Withdrawal Details Modal -->
<div class="modal fade" id="withdrawalDetailsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div id="withdrawalDetailsContent"></div>
        </div>
    </div>
</div>

<!-- No Verified Methods Modal -->
<div class="modal fade" id="noVerifiedMethodsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">
                    <i class="anticon anticon-exclamation-circle"></i> Verification Required
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="anticon anticon-lock" style="font-size: 64px; color: #ffc107;"></i>
                </div>
                <h5 class="text-center mb-3">No Verified Payment Methods</h5>
                <p class="text-center">
                    To protect your funds and prevent fraud, you need at least one <strong>verified cryptocurrency wallet</strong> before you can make withdrawals.
                </p>
                
                <div class="alert alert-info">
                    <h6><strong>🛡️ Why Verification is Required:</strong></h6>
                    <ul class="mb-0">
                        <li>Proves you own the wallet</li>
                        <li>Prevents unauthorized withdrawals</li>
                        <li>Protects your funds</li>
                        <li>Industry standard security practice</li>
                    </ul>
                </div>
                
                <div class="alert alert-success">
                    <h6><strong>✅ Quick Verification Process:</strong></h6>
                    <ol class="mb-0">
                        <li>Add your cryptocurrency wallet</li>
                        <li>Make a small test payment (Satoshi Test)</li>
                        <li>Submit transaction hash</li>
                        <li>Wait for admin approval (usually 1-24 hours)</li>
                        <li>Start withdrawing!</li>
                    </ol>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a href="satoshi-test-guide.php" class="btn btn-info">
                    <i class="anticon anticon-question-circle"></i> Learn About Verification
                </a>
                <a href="payment-methods.php" class="btn btn-primary">
                    <i class="anticon anticon-credit-card"></i> Add Payment Method
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Handle payment method selection
$(document).ready(function() {
    $('#paymentMethodSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const methodType = selectedOption.data('type');
        
        if (!selectedOption.val()) {
            $('#selectedMethodDetails').hide();
            $('#paymentDetailsField').val('');
            return;
        }
        
        let detailsHTML = '';
        let paymentDetails = '';
        
        if (methodType === 'crypto') {
            const crypto = selectedOption.data('cryptocurrency');
            const network = selectedOption.data('network');
            const address = selectedOption.data('address');
            
            detailsHTML = `
                <strong>Selected Method:</strong> ${crypto} (${network})<br>
                <strong>Wallet Address:</strong> ${address}
            `;
            paymentDetails = `${crypto} Wallet (${network})\nAddress: ${address}`;
        } else if (methodType === 'fiat') {
            const bank = selectedOption.data('bank');
            const holder = selectedOption.data('holder');
            
            detailsHTML = `
                <strong>Bank:</strong> ${bank}<br>
                <strong>Account Holder:</strong> ${holder}
            `;
            paymentDetails = `Bank: ${bank}\nAccount Holder: ${holder}`;
        }
        
        $('#selectedMethodDetails').html(detailsHTML).show();
        $('#paymentDetailsField').val(paymentDetails);
    });
});
</script>

<?php include 'footer.php'; ?>
