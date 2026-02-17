<?php 
/**
 * Payment Methods Management Page
 * Allows users to manage their fiat and crypto payment methods
 */
include 'header.php'; 
?>

<style>
.payment-card {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s;
}
.payment-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.payment-card.default {
    border-color: #4e73df;
    background: #f8f9fc;
}
.payment-badge {
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 3px;
}
.method-actions {
    float: right;
}
.method-actions button {
    margin-left: 5px;
    padding: 3px 8px;
    font-size: 12px;
}
.add-method-btn {
    width: 100%;
    padding: 15px;
    border: 2px dashed #cbd5e0;
    background: #f7fafc;
    color: #4a5568;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}
.add-method-btn:hover {
    border-color: #4e73df;
    background: #e7f1ff;
    color: #4e73df;
}
.masked-text {
    font-family: monospace;
    letter-spacing: 2px;
}
.verification-status {
    display: inline-block;
    margin-left: 8px;
}
.status-icon {
    cursor: pointer;
    margin-left: 8px;
    transition: all 0.3s;
}
.status-icon:hover {
    transform: scale(1.2);
}
.qr-code-container {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    margin: 15px 0;
}
.copy-btn {
    cursor: pointer;
    margin-left: 5px;
}
.verification-info {
    background: #e7f3ff;
    border-left: 4px solid #4e73df;
    padding: 15px;
    margin: 15px 0;
    border-radius: 4px;
}
.security-box {
    background: #fff3cd;
    border: 1px solid #ffc107;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}
.security-box h6 {
    color: #856404;
    margin-bottom: 15px;
}
.security-box ul {
    margin-bottom: 0;
}
.security-box li {
    margin-bottom: 8px;
    color: #856404;
}

/* Prominent Alert Boxes for Verification Status */
.verification-alert {
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 5px solid;
    font-size: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    animation: slideIn 0.5s ease-out;
}

.alert-pending {
    background-color: #fff3cd;
    border-color: #ffc107;
    color: #856404;
}

.alert-verifying {
    background-color: #d1ecf1;
    border-color: #17a2b8;
    color: #0c5460;
}

.alert-verified {
    background-color: #d4edda;
    border-color: #28a745;
    color: #155724;
}

.alert-failed {
    background-color: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}

/* Animations */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
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
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.pulse-icon {
    animation: pulse 2s ease-in-out infinite;
}

.rotate-icon {
    animation: rotate 2s linear infinite;
}

/* Enhanced visibility */
.verification-status {
    font-size: 1.2em;
    font-weight: bold;
    padding: 6px 12px;
}

.status-icon {
    font-size: 1.5em;
    cursor: pointer;
    margin-left: 10px;
    transition: all 0.3s;
}

.status-icon:hover {
    transform: scale(1.3);
}

/* Large action buttons in alerts */
.verification-alert .btn {
    white-space: nowrap;
    font-size: 1em;
    padding: 10px 20px;
}

.verification-alert h6 {
    margin: 0;
    font-size: 1.2em;
}
</style>

<!-- Content Wrapper START -->
<div class="main-content">
    <div class="container-fluid">
        <div class="page-header">
            <h2 class="header-title">Payment Methods</h2>
            <p class="text-muted">Manage your fiat and cryptocurrency payment methods</p>
        </div>

        <div class="row">
            <!-- Fiat Payment Methods -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-university"></i> Bank Accounts
                        </h4>
                        <span class="badge badge-primary" id="fiatCount">0</span>
                    </div>
                    <div class="card-body">
                        <div id="fiatMethods">
                            <div class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                <p class="mt-2 text-muted">Loading...</p>
                            </div>
                        </div>
                        <button class="add-method-btn mt-3" onclick="showAddFiatModal()">
                            <i class="fas fa-plus-circle"></i> Add Bank Account
                        </button>
                    </div>
                </div>
            </div>

            <!-- Crypto Wallets -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fab fa-bitcoin"></i> Crypto Wallets
                        </h4>
                        <span class="badge badge-primary" id="cryptoCount">0</span>
                    </div>
                    <div class="card-body">
                        <div id="cryptoMethods">
                            <div class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                <p class="mt-2 text-muted">Loading...</p>
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
                                   placeholder="Enter transaction hash" pattern="[a-fA-F0-9]{64}">
                            <small class="form-text text-muted">64-character hexadecimal transaction ID from blockchain</small>
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
                displayVerificationInstructions(response.data);
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
    let html = '';
    
    if (status === 'pending') {
        html = `
            <div class="alert alert-info">
                <i class="fas fa-clock"></i> <strong>Awaiting Admin Setup</strong><br>
                Your wallet is pending verification setup by our admin team. 
                You will be notified once verification details are available.
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
    } else {
        // Show verification instructions with amount and address
        if (data.verification_amount && data.verification_address) {
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
                        <li>Send from the wallet address you registered: <code>${data.wallet_address_masked}</code></li>
                        <li>Send the <strong>exact amount</strong> shown above</li>
                        <li>Double-check the destination address</li>
                        <li>Keep your transaction hash (TXID) ready</li>
                    </ul>
                </div>
            `;
            $('#submitTxHashSection').show();
        } else {
            html = `
                <div class="alert alert-info">
                    <i class="fas fa-clock"></i> Verification details not yet available. Please wait for admin setup.
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
    
    if (!txid || txid.length !== 64) {
        showError('Please enter a valid 64-character transaction hash');
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