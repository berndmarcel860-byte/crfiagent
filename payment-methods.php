<?php 
/**
 * Payment Methods Management Page
 * Allows users to manage their fiat and crypto payment methods
 * Updated: 2026-03-01 - Modern Professional Design
 */
include 'header.php'; 
?>

<style>
/* Modern Card Design */
.payment-card {
    border: none;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    background: #ffffff;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.payment-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4e73df, #224abe);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.payment-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.payment-card:hover::before {
    opacity: 1;
}

.payment-card.default {
    background: linear-gradient(135deg, #f8f9fc 0%, #e7f0ff 100%);
    border: 2px solid #4e73df;
}

.payment-card.default::before {
    opacity: 1;
    height: 4px;
}

/* Enhanced Badges */
.payment-badge {
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 12px;
    font-weight: 600;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}

.badge-default {
    background: linear-gradient(135deg, #4e73df, #224abe);
    color: white;
    box-shadow: 0 2px 8px rgba(78, 115, 223, 0.3);
}

/* Method Actions */
.method-actions {
    float: right;
    display: flex;
    gap: 8px;
}

.method-actions button {
    margin-left: 0;
    padding: 6px 12px;
    font-size: 13px;
    border-radius: 6px;
    transition: all 0.2s ease;
    border: none;
}

.method-actions button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Modern Add Button */
.add-method-btn {
    width: 100%;
    padding: 20px;
    border: 2px dashed #cbd5e0;
    background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%);
    color: #4a5568;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    font-size: 15px;
}

.add-method-btn:hover {
    border-color: #4e73df;
    border-style: solid;
    background: linear-gradient(135deg, #e7f1ff 0%, #f0f7ff 100%);
    color: #4e73df;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(78, 115, 223, 0.15);
}

.add-method-btn i {
    font-size: 18px;
    margin-right: 8px;
}

/* Typography */
.masked-text {
    font-family: 'Courier New', monospace;
    letter-spacing: 3px;
    font-weight: 600;
    color: #2d3748;
}

/* Status Icons */
.verification-status {
    display: inline-flex;
    align-items: center;
    margin-left: 10px;
    gap: 6px;
}

.status-icon {
    cursor: pointer;
    margin-left: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 1.4em;
}

.status-icon:hover {
    transform: scale(1.3) rotate(5deg);
}

/* QR Code Container */
.qr-code-container {
    text-align: center;
    padding: 30px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 12px;
    margin: 20px 0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

/* Copy Button */
.copy-btn {
    cursor: pointer;
    margin-left: 8px;
    transition: all 0.2s ease;
    padding: 4px 8px;
    border-radius: 6px;
}

.copy-btn:hover {
    background: #e7f1ff;
    color: #4e73df;
    transform: scale(1.1);
}

/* Info Boxes */
.verification-info {
    background: linear-gradient(135deg, #e7f3ff 0%, #f0f7ff 100%);
    border-left: 5px solid #4e73df;
    padding: 20px;
    margin: 20px 0;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(78, 115, 223, 0.1);
}

.security-box {
    background: linear-gradient(135deg, #fff3cd 0%, #fffaf0 100%);
    border: 2px solid #ffc107;
    padding: 24px;
    border-radius: 12px;
    margin: 24px 0;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.15);
}

.security-box h6 {
    color: #856404;
    margin-bottom: 16px;
    font-weight: 700;
    font-size: 16px;
}

.security-box ul {
    margin-bottom: 0;
    padding-left: 20px;
}

.security-box li {
    margin-bottom: 10px;
    color: #856404;
    line-height: 1.6;
}

/* Prominent Alert Boxes for Verification Status */
.verification-alert {
    padding: 24px;
    border-radius: 12px;
    margin-bottom: 20px;
    border-left: 5px solid;
    font-size: 16px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    animation: slideIn 0.5s ease-out;
    backdrop-filter: blur(10px);
}

.alert-pending {
    background: linear-gradient(135deg, #fff3cd 0%, #fffaf0 100%);
    border-color: #ffc107;
    color: #856404;
}

.alert-verifying {
    background: linear-gradient(135deg, #d1ecf1 0%, #e7f9fc 100%);
    border-color: #17a2b8;
    color: #0c5460;
}

.alert-verified {
    background: linear-gradient(135deg, #d4edda 0%, #e7f5ea 100%);
    border-color: #28a745;
    color: #155724;
}

.alert-failed {
    background: linear-gradient(135deg, #f8d7da 0%, #fde7ea 100%);
    border-color: #dc3545;
    color: #721c24;
}

/* Animations */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.7;
        transform: scale(0.95);
    }
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.pulse-icon {
    animation: pulse 2s ease-in-out infinite;
}

.rotate-icon {
    animation: rotate 2s linear infinite;
}

.fade-in {
    animation: fadeIn 0.5s ease-in;
}

/* Enhanced visibility */
.verification-status {
    font-size: 1.3em;
    font-weight: 700;
    padding: 8px 14px;
    border-radius: 20px;
}

.status-icon {
    font-size: 1.6em;
    cursor: pointer;
    margin-left: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.status-icon:hover {
    transform: scale(1.4) rotate(-5deg);
}

/* Large action buttons in alerts */
.verification-alert .btn {
    white-space: nowrap;
    font-size: 1.05em;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.verification-alert .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.verification-alert h6 {
    margin: 0 0 10px 0;
    font-size: 1.3em;
    font-weight: 700;
}

/* Card Enhancements */
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    overflow: hidden;
}

.card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.card-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    border-bottom: none;
    padding: 20px;
    font-weight: 600;
}

.card-header .card-title {
    color: white;
    font-size: 1.3em;
    font-weight: 700;
}

.card-header .badge {
    font-size: 13px;
    padding: 6px 12px;
    border-radius: 12px;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
}

.card-body {
    padding: 24px;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    padding: 32px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 4px 16px rgba(78, 115, 223, 0.3);
}

.page-header h2 {
    font-size: 2em;
    font-weight: 700;
    margin-bottom: 8px;
}

.page-header p {
    color: rgba(255,255,255,0.9);
    font-size: 1.1em;
    margin-bottom: 0;
}

/* Alert Improvements */
.alert {
    border-radius: 12px;
    border: none;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #e7f9fc 100%);
    color: #0c5460;
    border-left: 5px solid #17a2b8;
}

.alert-heading {
    font-weight: 700;
    font-size: 1.2em;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #718096;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    color: #cbd5e0;
}

.empty-state h5 {
    font-size: 1.3em;
    font-weight: 600;
    margin-bottom: 12px;
    color: #4a5568;
}

.empty-state p {
    font-size: 1em;
    color: #718096;
}

/* Loading State */
.loading-state {
    text-align: center;
    padding: 40px 20px;
}

.loading-state i {
    font-size: 48px;
    color: #4e73df;
}

.loading-state p {
    margin-top: 16px;
    color: #718096;
    font-size: 1.1em;
}

/* Responsive Design */
@media (max-width: 768px) {
    .payment-card {
        padding: 20px;
    }
    
    .method-actions {
        float: none;
        display: flex;
        justify-content: flex-end;
        margin-top: 12px;
    }
    
    .page-header {
        padding: 24px;
    }
    
    .page-header h2 {
        font-size: 1.6em;
    }
    
    .add-method-btn {
        padding: 16px;
        font-size: 14px;
    }
    
    .card-header {
        padding: 16px;
    }
    
    .card-body {
        padding: 20px;
    }
}

</style>

<!-- Content Wrapper START -->
<div class="main-content">
    <div class="container-fluid">
        <div class="page-header">
            <h2 class="header-title"><i class="fas fa-wallet"></i> Payment Methods</h2>
            <p class="text-white-50">Manage your fiat and cryptocurrency payment methods securely</p>
        </div>

        <!-- Info Alert -->
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">
                <i class="fas fa-shield-alt"></i> About Cryptocurrency Wallet Verification
            </h5>
            <p class="mb-2">
                Cryptocurrency wallets require verification through a <strong>Satoshi Test</strong> before they can be used for withdrawals. 
                This is a security measure to prove wallet ownership and protect your funds.
            </p>
            <p class="mb-0">
                <a href="satoshi-test-guide.php" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-book-open"></i> Learn About Satoshi Test
                </a>
            </p>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="row">
            <!-- Fiat Payment Methods -->
            <div class="col-lg-6 mb-4">
                <div class="card fade-in">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-university"></i> Bank Accounts
                        </h4>
                        <span class="badge badge-light" id="fiatCount">0</span>
                    </div>
                    <div class="card-body">
                        <div id="fiatMethods">
                            <div class="loading-state">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Loading bank accounts...</p>
                            </div>
                        </div>
                        <button class="add-method-btn mt-3" onclick="showAddFiatModal()">
                            <i class="fas fa-plus-circle"></i> Add Bank Account
                        </button>
                    </div>
                </div>
            </div>

            <!-- Crypto Wallets -->
            <div class="col-lg-6 mb-4">
                <div class="card fade-in">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fab fa-bitcoin"></i> Crypto Wallets
                        </h4>
                        <span class="badge badge-light" id="cryptoCount">0</span>
                    </div>
                    <div class="card-body">
                        <div id="cryptoMethods">
                            <div class="loading-state">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Loading crypto wallets...</p>
                            </div>
                        </div>
                        <button class="add-method-btn mt-3" onclick="showAddCryptoModal()">
                            <i class="fas fa-plus-circle"></i> Add Crypto Wallet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Content Wrapper END -->

<!-- Add Fiat Payment Method Modal -->
<div class="modal fade" id="addFiatModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Bank Account</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addFiatForm">
                <div class="modal-body">
                    <input type="hidden" name="type" value="fiat">
                    
                    <div class="form-group">
                        <label>Payment Method Type <span class="text-danger">*</span></label>
                        <select class="form-control" name="payment_method" required>
                            <option value="">Select...</option>
                            <option value="Bank Transfer">Bank Transfer (SEPA)</option>
                            <option value="Wire Transfer">Wire Transfer</option>
                            <option value="Credit Card">Credit/Debit Card</option>
                            <option value="PayPal">PayPal</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Label (Optional)</label>
                        <input type="text" class="form-control" name="label" placeholder="e.g., My Main Account">
                        <small class="form-text text-muted">Friendly name for this payment method</small>
                    </div>

                    <div class="form-group">
                        <label>Account Holder Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="account_holder" required placeholder="Full name as on account">
                    </div>

                    <div class="form-group">
                        <label>Bank Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="bank_name" required placeholder="Name of your bank">
                    </div>

                    <div class="form-group">
                        <label>IBAN</label>
                        <input type="text" class="form-control" name="iban" placeholder="DE89370400440532013000" maxlength="34">
                        <small class="form-text text-muted">International Bank Account Number</small>
                    </div>

                    <div class="form-group">
                        <label>BIC/SWIFT Code</label>
                        <input type="text" class="form-control" name="bic" placeholder="COBADEFFXXX" maxlength="11">
                    </div>

                    <div class="form-group">
                        <label>Account Number (if no IBAN)</label>
                        <input type="text" class="form-control" name="account_number" placeholder="Account number">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_default" id="fiatIsDefault">
                        <label class="form-check-label" for="fiatIsDefault">
                            Set as default payment method
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Payment Method
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Crypto Wallet Modal -->
<div class="modal fade" id="addCryptoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Crypto Wallet</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addCryptoForm">
                <div class="modal-body">
                    <input type="hidden" name="type" value="crypto">
                    <input type="hidden" name="payment_method" value="Cryptocurrency">
                    
                    <div class="form-group">
                        <label>Cryptocurrency <span class="text-danger">*</span></label>
                        <select class="form-control" name="cryptocurrency" required id="cryptoSelect">
                            <option value="">Loading...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Network <span class="text-danger">*</span></label>
                        <select class="form-control" name="network" required id="networkSelect">
                            <option value="">Select cryptocurrency first</option>
                        </select>
                        <small class="form-text text-muted">Blockchain network for this wallet</small>
                    </div>

                    <div class="form-group">
                        <label>Wallet Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="wallet_address" required placeholder="Enter wallet address">
                        <small class="form-text text-muted">Your cryptocurrency wallet address</small>
                    </div>

                    <div class="form-group">
                        <label>Label (Optional)</label>
                        <input type="text" class="form-control" name="label" placeholder="e.g., My BTC Wallet">
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Important:</strong> Make sure the wallet address and network are correct. Sending funds to the wrong address may result in permanent loss.
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_default" id="cryptoIsDefault">
                        <label class="form-check-label" for="cryptoIsDefault">
                            Set as default payment method
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Wallet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Verification Details Modal -->
<div class="modal fade" id="verificationDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-shield-alt"></i> Wallet Verification Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Why Satoshi Test Section -->
                <div class="security-box">
                    <h6><i class="fas fa-question-circle"></i> Why Do I Need to Make a Satoshi Test?</h6>
                    <p><strong>For Your Security and Platform Safety:</strong></p>
                    <ul>
                        <li><i class="fas fa-check-circle text-success"></i> <strong>Proves Wallet Ownership:</strong> Confirms you control this wallet address</li>
                        <li><i class="fas fa-check-circle text-success"></i> <strong>Prevents Fraud:</strong> Stops fake wallet submissions</li>
                        <li><i class="fas fa-check-circle text-success"></i> <strong>Protects Your Funds:</strong> Ensures withdrawals go to the right address</li>
                        <li><i class="fas fa-check-circle text-success"></i> <strong>One-Time Only:</strong> You only need to verify each wallet once</li>
                        <li><i class="fas fa-check-circle text-success"></i> <strong>Industry Standard:</strong> All secure crypto platforms require this</li>
                    </ul>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle"></i> <strong>Note:</strong> The small test amount will be credited to your account after verification.
                    </div>
                </div>

                <!-- Verification Instructions -->
                <div id="verificationInstructions">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Loading verification details...</p>
                    </div>
                </div>

                <!-- Submit Transaction Hash Section -->
                <div id="submitTxHashSection" style="display:none;">
                    <hr>
                    <h6><i class="fas fa-check-circle"></i> Submit Your Transaction Hash</h6>
                    <p>After sending the test amount, paste your transaction hash (TXID) below:</p>
                    <form id="submitVerificationForm">
                        <input type="hidden" id="verifyWalletId" name="wallet_id">
                        <div class="form-group">
                            <label>Transaction Hash (TXID) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="verification_txid" required 
                                   placeholder="0xf088dbc09554739ba15d5788378f6b3f76e85f53294213b03fceadf891446487" pattern="0x[a-fA-F0-9]{64}">
                            <small class="form-text text-muted">66-character transaction hash (0x + 64 hex characters)</small>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-paper-plane"></i> Submit for Verification
                        </button>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Store available cryptocurrencies globally
let availableCryptos = [];

// Load payment methods on page load
$(document).ready(function() {
    loadPaymentMethods();
    loadAvailableCryptocurrencies();
});

// Load available cryptocurrencies from database
function loadAvailableCryptocurrencies() {
    console.log('Loading cryptocurrencies...');
    $.ajax({
        url: 'ajax/get_available_cryptocurrencies.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('AJAX Response:', response);
            if (response.success) {
                console.log('Cryptocurrencies loaded:', response.cryptocurrencies.length);
                availableCryptos = response.cryptocurrencies;
                populateCryptoDropdown();
            } else {
                console.error('Server returned error:', response.message);
                alert('Error loading cryptocurrencies: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.error('Response:', xhr.responseText);
            alert('Failed to load cryptocurrencies. Check console for details.');
        }
    });
}

// Populate cryptocurrency dropdown with data from database
function populateCryptoDropdown() {
    const cryptoSelect = $('#cryptoSelect');
    cryptoSelect.html('<option value="">Select...</option>');
    
    availableCryptos.forEach(crypto => {
        cryptoSelect.append(`<option value="${crypto.symbol}" data-crypto-id="${crypto.id}">${crypto.name} (${crypto.symbol})</option>`);
    });
}

// Update network options when cryptocurrency changes
$('#cryptoSelect').change(function() {
    const symbol = $(this).val();
    const networkSelect = $('#networkSelect');
    networkSelect.html('<option value="">Select network...</option>');
    
    if (symbol) {
        const crypto = availableCryptos.find(c => c.symbol === symbol);
        if (crypto && crypto.networks) {
            crypto.networks.forEach(network => {
                networkSelect.append(`<option value="${network.network_name}">${network.network_name}</option>`);
            });
        }
    }
});

function loadPaymentMethods() {
    $.ajax({
        url: 'ajax/get_payment_methods.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayFiatMethods(response.methods.fiat);
                displayCryptoMethods(response.methods.crypto);
                $('#fiatCount').text(response.counts.fiat);
                $('#cryptoCount').text(response.counts.crypto);
            } else {
                showError(response.message);
            }
        },
        error: function() {
            showError('Failed to load payment methods');
        }
    });
}

function displayFiatMethods(methods) {
    const container = $('#fiatMethods');
    
    if (methods.length === 0) {
        container.html(`
            <div class="text-center py-4">
                <i class="fas fa-university fa-3x text-muted mb-3"></i>
                <p class="text-muted">No bank accounts added yet</p>
            </div>
        `);
        return;
    }

    let html = '';
    methods.forEach(method => {
        const isDefault = method.is_default == 1;
        const verified = method.is_verified == 1;
        
        html += `
            <div class="payment-card ${isDefault ? 'default' : ''}" id="method-${method.id}">
                <div class="method-actions">
                    ${!isDefault ? `<button class="btn btn-sm btn-outline-primary" onclick="setDefault(${method.id})"><i class="fas fa-star"></i></button>` : ''}
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteMethod(${method.id})"><i class="fas fa-trash"></i></button>
                </div>
                <h5 class="mb-2">
                    <i class="fas fa-university text-primary"></i> ${method.label || method.payment_method}
                    ${isDefault ? '<span class="badge badge-success payment-badge">Default</span>' : ''}
                    ${verified ? '<span class="badge badge-info payment-badge">Verified</span>' : ''}
                </h5>
                <p class="mb-1"><strong>${method.account_holder}</strong></p>
                <p class="mb-1">${method.bank_name}</p>
                ${method.iban ? `<p class="mb-1 masked-text">IBAN: ${method.iban_masked || method.iban}</p>` : ''}
                ${method.bic ? `<p class="mb-1">BIC: ${method.bic}</p>` : ''}
                <small class="text-muted">Added ${new Date(method.created_at).toLocaleDateString()}</small>
            </div>
        `;
    });
    
    container.html(html);
}

function displayCryptoMethods(methods) {
    const container = $('#cryptoMethods');
    
    if (methods.length === 0) {
        container.html(`
            <div class="text-center py-4">
                <i class="fab fa-bitcoin fa-3x text-muted mb-3"></i>
                <p class="text-muted">No crypto wallets added yet</p>
            </div>
        `);
        return;
    }

    let html = '';
    methods.forEach(method => {
        const isDefault = method.is_default == 1;
        const verified = method.is_verified == 1;
        const status = method.verification_status || 'pending';
        
        // Determine status badge and icon
        let statusBadge = '';
        let statusIcon = '';
        let alertBox = '';
        
        switch(status) {
            case 'pending':
                statusBadge = '<span class="badge badge-warning verification-status" style="font-size: 1.2em; font-weight: bold; padding: 6px 12px;">⚠️ PENDING VERIFICATION</span>';
                statusIcon = `<i class="fas fa-info-circle text-warning status-icon pulse-icon" 
                               onclick="showVerificationDetails(${method.id})" 
                               title="View verification details" 
                               style="font-size: 1.5em; cursor: pointer;"></i>`;
                alertBox = `
                    <div class="verification-alert alert-pending">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <h6 class="mb-2"><i class="fas fa-exclamation-triangle pulse-icon"></i> <strong>ACTION REQUIRED: Wallet Verification Pending</strong></h6>
                                <p class="mb-0">This wallet needs verification before use. Click below to view payment instructions.</p>
                            </div>
                            <button class="btn btn-warning btn-lg" onclick="showVerificationDetails(${method.id})">
                                <i class="fas fa-eye"></i> View Instructions
                            </button>
                        </div>
                    </div>
                `;
                break;
            case 'verifying':
                statusBadge = '<span class="badge badge-info verification-status" style="font-size: 1.2em; font-weight: bold; padding: 6px 12px;">⏳ VERIFYING</span>';
                statusIcon = `<i class="fas fa-clock text-info status-icon rotate-icon" 
                               title="Awaiting admin approval" 
                               style="font-size: 1.5em;"></i>`;
                alertBox = `
                    <div class="verification-alert alert-verifying">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <h6 class="mb-2"><i class="fas fa-clock rotate-icon"></i> <strong>Verification in Progress</strong></h6>
                                <p class="mb-0">Transaction submitted and under review. You'll be notified once verified.</p>
                            </div>
                            <button class="btn btn-info" onclick="showVerificationDetails(${method.id})">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                `;
                break;
            case 'verified':
                statusBadge = '<span class="badge badge-success verification-status" style="font-size: 1.2em; font-weight: bold; padding: 6px 12px;">✅ VERIFIED</span>';
                statusIcon = '<i class="fas fa-check-circle text-success status-icon" title="Verified" style="font-size: 1.5em;"></i>';
                alertBox = `
                    <div class="verification-alert alert-verified">
                        <h6 class="mb-0"><i class="fas fa-check-circle"></i> <strong>Wallet Verified and Ready</strong> - This wallet is verified and ready to use for transactions.</h6>
                    </div>
                `;
                break;
            case 'failed':
                statusBadge = '<span class="badge badge-danger verification-status" style="font-size: 1.2em; font-weight: bold; padding: 6px 12px;">❌ VERIFICATION FAILED</span>';
                statusIcon = `<i class="fas fa-exclamation-circle text-danger status-icon pulse-icon" 
                               onclick="showVerificationDetails(${method.id})" 
                               title="View details" 
                               style="font-size: 1.5em; cursor: pointer;"></i>`;
                alertBox = `
                    <div class="verification-alert alert-failed">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <h6 class="mb-2"><i class="fas fa-times-circle"></i> <strong>Verification Failed</strong></h6>
                                <p class="mb-0">${method.verification_notes || 'Please contact support or try again.'}</p>
                            </div>
                            <button class="btn btn-danger" onclick="showVerificationDetails(${method.id})">
                                <i class="fas fa-redo"></i> Try Again
                            </button>
                        </div>
                    </div>
                `;
                break;
        }
        
        html += `
            ${alertBox}
            <div class="payment-card ${isDefault ? 'default' : ''}" id="method-${method.id}">
                <div class="method-actions">
                    ${!isDefault && status === 'verified' ? `<button class="btn btn-sm btn-outline-primary" onclick="setDefault(${method.id})"><i class="fas fa-star"></i></button>` : ''}
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteMethod(${method.id})"><i class="fas fa-trash"></i></button>
                </div>
                <h5 class="mb-2">
                    <i class="fab fa-bitcoin text-warning"></i> ${method.label || method.cryptocurrency}
                    ${isDefault ? '<span class="badge badge-success payment-badge">Default</span>' : ''}
                    ${statusBadge}
                    ${statusIcon}
                </h5>
                <p class="mb-1"><strong>${method.cryptocurrency}</strong> - ${method.network}</p>
                <p class="mb-1 masked-text">${method.wallet_address_masked || method.wallet_address}</p>
                ${method.verification_notes ? `<p class="mb-1 text-danger"><small><i class="fas fa-exclamation-triangle"></i> ${method.verification_notes}</small></p>` : ''}
                <small class="text-muted">Added ${new Date(method.created_at).toLocaleDateString()}</small>
            </div>
        `;
    });
    
    container.html(html);
}

function showAddFiatModal() {
    $('#addFiatModal').modal('show');
}

function showAddCryptoModal() {
    $('#addCryptoModal').modal('show');
}

// Handle fiat form submission
$('#addFiatForm').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'ajax/add_payment_method.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#addFiatModal').modal('hide');
                $('#addFiatForm')[0].reset();
                loadPaymentMethods();
                showSuccess('Bank account added successfully');
            } else {
                showError(response.message);
            }
        },
        error: function() {
            showError('Failed to add payment method');
        }
    });
});

// Handle crypto form submission
$('#addCryptoForm').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'ajax/add_payment_method.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#addCryptoModal').modal('hide');
                $('#addCryptoForm')[0].reset();
                loadPaymentMethods();
                showSuccess('Crypto wallet added successfully');
            } else {
                showError(response.message);
            }
        },
        error: function() {
            showError('Failed to add wallet');
        }
    });
});

function setDefault(paymentId) {
    if (!confirm('Set this as your default payment method?')) return;
    
    $.ajax({
        url: 'ajax/set_default_payment_method.php',
        type: 'POST',
        data: { payment_id: paymentId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                loadPaymentMethods();
                showSuccess('Default payment method updated');
            } else {
                showError(response.message);
            }
        }
    });
}

function deleteMethod(paymentId) {
    if (!confirm('Are you sure you want to delete this payment method?')) return;
    
    $.ajax({
        url: 'ajax/delete_payment_method.php',
        type: 'POST',
        data: { payment_id: paymentId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                loadPaymentMethods();
                showSuccess('Payment method deleted');
            } else {
                showError(response.message);
            }
        }
    });
}

function showSuccess(message) {
    alert(message); // Replace with toastr or your notification library
}

function showError(message) {
    alert('Error: ' + message); // Replace with toastr or your notification library
}

// Show verification details modal
function showVerificationDetails(walletId) {
    $('#verificationDetailsModal').modal('show');
    $('#verifyWalletId').val(walletId);
    
    // Show loading state
    $('#verificationInstructions').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Loading verification details...</p>
        </div>
    `);
    
    // Load verification details
    $.ajax({
        url: 'ajax/get_wallet_verification_details.php',
        type: 'GET',
        data: { wallet_id: walletId },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayVerificationInstructions(response);
            } else {
                $('#verificationInstructions').html(`
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> ${response?.message || 'Unable to load verification details'}
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Verification details error:', error);
            $('#verificationInstructions').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Failed to load verification details. Please try again.
                </div>
            `);
        }
    });
}

function displayVerificationInstructions(data) {
    // Add null check to prevent undefined errors
    if (!data) {
        $('#verificationInstructions').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <strong>Error:</strong> No data received from server.
            </div>
        `);
        return;
    }
    
    const status = data.verification_status || 'pending';
    
    // Create masked wallet address if not provided
    let maskedAddress = data.wallet_address || '';
    if (maskedAddress && maskedAddress.length > 13) {
        maskedAddress = maskedAddress.substring(0, 6) + '...' + maskedAddress.substring(maskedAddress.length - 6);
    }
    
    let html = '';
    
    // Check if admin has set verification details
    const hasVerificationDetails = data.verification_amount && data.verification_address;
    
    // Handle different statuses
    if (status === 'verified') {
        html = `
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <strong>Wallet Verified!</strong><br>
                Your wallet has been successfully verified and is ready to use.
            </div>
        `;
    } else if (status === 'verifying') {
        html = `
            <div class="alert alert-info">
                <i class="fas fa-hourglass-half"></i> <strong>Under Review</strong><br>
                Your transaction is being verified by our admin team. This usually takes a few minutes to a few hours.
            </div>
            <div class="verification-info">
                <p><strong>Transaction Hash Submitted:</strong></p>
                <p class="masked-text" style="word-break: break-all;">${data.verification_txid}</p>
            </div>
        `;
    } else if (status === 'failed') {
        html = `
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i> <strong>Verification Failed</strong><br>
                ${data.verification_notes || 'Please contact support for more information.'}
            </div>
        `;
        $('#submitTxHashSection').show();
    } else if (status === 'pending') {
        // Pending can mean two things:
        // 1. Awaiting admin to set verification details
        // 2. Awaiting user to make payment (admin already set details)
        
        if (hasVerificationDetails) {
            // Admin has set details, show payment instructions
            html = `
                <div class="verification-info">
                    <h6><i class="fas fa-coins"></i> Step 1: Send Test Amount</h6>
                    <p>Send <strong>exactly</strong> this amount from your wallet:</p>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="testAmount" value="${data.verification_amount}" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary copy-btn" onclick="copyToClipboard('testAmount')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>

                <div class="verification-info">
                    <h6><i class="fas fa-wallet"></i> Step 2: Send to This Address</h6>
                    <p>Send the test amount to this platform wallet address:</p>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="testAddress" value="${data.verification_address}" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary copy-btn" onclick="copyToClipboard('testAddress')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    <div class="qr-code-container">
                        <p><small class="text-muted">Scan QR code to send (coming soon)</small></p>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong>
                    <ul class="mb-0">
                        <li>Send from the wallet address you registered: <code>${maskedAddress}</code></li>
                        <li>Send the <strong>exact amount</strong> shown above</li>
                        <li>Double-check the destination address</li>
                        <li>Keep your transaction hash (TXID) ready</li>
                    </ul>
                </div>
            `;
            $('#submitTxHashSection').show();
        } else {
            // Admin has NOT set details yet, show waiting message
            html = `
                <div class="alert alert-info">
                    <i class="fas fa-clock"></i> <strong>⏳ Awaiting Admin Setup</strong><br>
                    Your wallet is pending verification setup by our admin team. 
                    You will be notified once verification details are available.
                </div>
            `;
        }
    }
    
    $('#verificationInstructions').html(html);
}

// Copy to clipboard function
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    document.execCommand('copy');
    
    // Visual feedback
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
    setTimeout(() => {
        btn.innerHTML = originalHTML;
    }, 2000);
}

// Handle verification form submission
$('#submitVerificationForm').submit(function(e) {
    e.preventDefault();
    
    const walletId = $('#verifyWalletId').val();
    const txid = $(this).find('[name="verification_txid"]').val();
    
    if (!txid || txid.length !== 66 || !txid.startsWith('0x')) {
        showError('Please enter a valid transaction hash (0x + 64 hex characters)');
        return;
    }
    
    $.ajax({
        url: 'ajax/submit_wallet_verification.php',
        type: 'POST',
        data: {
            wallet_id: walletId,
            verification_txid: txid
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#verificationDetailsModal').modal('hide');
                $('#submitVerificationForm')[0].reset();
                loadPaymentMethods();
                showSuccess('Transaction hash submitted successfully! Your wallet is now under review.');
            } else {
                showError(response.message);
            }
        },
        error: function() {
            showError('Failed to submit transaction hash. Please try again.');
        }
    });
});

</script>

<?php include 'footer.php'; ?>