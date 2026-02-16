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
                            <option value="">Select...</option>
                            <option value="BTC">Bitcoin (BTC)</option>
                            <option value="ETH">Ethereum (ETH)</option>
                            <option value="USDT">Tether (USDT)</option>
                            <option value="USDC">USD Coin (USDC)</option>
                            <option value="BNB">Binance Coin (BNB)</option>
                            <option value="XRP">Ripple (XRP)</option>
                            <option value="ADA">Cardano (ADA)</option>
                            <option value="SOL">Solana (SOL)</option>
                            <option value="DOT">Polkadot (DOT)</option>
                            <option value="DOGE">Dogecoin (DOGE)</option>
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

<script>
// Network options based on cryptocurrency
const cryptoNetworks = {
    'BTC': ['Bitcoin'],
    'ETH': ['Ethereum (ERC-20)'],
    'USDT': ['Ethereum (ERC-20)', 'Tron (TRC-20)', 'BSC (BEP-20)', 'Polygon', 'Solana'],
    'USDC': ['Ethereum (ERC-20)', 'Polygon', 'Solana', 'Avalanche'],
    'BNB': ['BSC (BEP-20)', 'Beacon Chain (BEP-2)'],
    'XRP': ['XRP Ledger'],
    'ADA': ['Cardano'],
    'SOL': ['Solana'],
    'DOT': ['Polkadot'],
    'DOGE': ['Dogecoin']
};

// Update network options when cryptocurrency changes
$('#cryptoSelect').change(function() {
    const crypto = $(this).val();
    const networkSelect = $('#networkSelect');
    networkSelect.html('<option value="">Select network...</option>');
    
    if (crypto && cryptoNetworks[crypto]) {
        cryptoNetworks[crypto].forEach(network => {
            networkSelect.append(`<option value="${network}">${network}</option>`);
        });
    }
});

// Load payment methods on page load
$(document).ready(function() {
    loadPaymentMethods();
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
        
        html += `
            <div class="payment-card ${isDefault ? 'default' : ''}" id="method-${method.id}">
                <div class="method-actions">
                    ${!isDefault ? `<button class="btn btn-sm btn-outline-primary" onclick="setDefault(${method.id})"><i class="fas fa-star"></i></button>` : ''}
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteMethod(${method.id})"><i class="fas fa-trash"></i></button>
                </div>
                <h5 class="mb-2">
                    <i class="fab fa-bitcoin text-warning"></i> ${method.label || method.cryptocurrency}
                    ${isDefault ? '<span class="badge badge-success payment-badge">Default</span>' : ''}
                    ${verified ? '<span class="badge badge-info payment-badge">Verified</span>' : ''}
                </h5>
                <p class="mb-1"><strong>${method.cryptocurrency}</strong> - ${method.network}</p>
                <p class="mb-1 masked-text">${method.wallet_address_masked || method.wallet_address}</p>
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
</script>

<?php include 'footer.php'; ?>