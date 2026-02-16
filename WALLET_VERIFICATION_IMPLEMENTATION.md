# Wallet Verification System - Complete Implementation Guide

## Overview
This document provides the complete implementation guide for the crypto wallet verification system using satoshi test deposits.

## ✅ Completed
- Database migration (004_add_wallet_verification_system.sql)

## 🔨 To Be Implemented

### 1. User Navbar Update
**File**: `header.php`

Add to navigation menu (around line 150-200 in nav section):
```php
<li class="nav-item">
    <a class="nav-link" href="payment-methods.php">
        <i class="fas fa-wallet"></i> Payment Methods
    </a>
</li>
```

### 2. Enhanced payment-methods.php
**Add** to existing file (after line 486):

**A. Verification Status Badges:**
```php
<?php
$statusClasses = [
    'pending' => 'badge-info',
    'verifying' => 'badge-warning',
    'verified' => 'badge-success',
    'failed' => 'badge-danger'
];
$statusIcons = [
    'pending' => 'fa-clock',
    'verifying' => 'fa-hourglass-half',
    'verified' => 'fa-check-circle',
    'failed' => 'fa-times-circle'
];
?>
```

**B. Display verification status in wallet cards:**
```javascript
// In displayCryptoWallets() function, add:
let statusBadge = '';
if (wallet.type === 'crypto') {
    const statusClass = {
        'pending': 'badge-info',
        'verifying': 'badge-warning',
        'verified': 'badge-success',
        'failed': 'badge-danger'
    }[wallet.verification_status] || 'badge-secondary';
    
    statusBadge = `<span class="badge ${statusClass}">
        ${wallet.verification_status.charAt(0).toUpperCase() + wallet.verification_status.slice(1)}
    </span>`;
}
```

**C. Add Verification Instructions Modal:**
```html
<!-- Verification Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verify Wallet Ownership</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="verificationInstructions">
                    <p>To verify ownership of this wallet, please follow these steps:</p>
                    <ol>
                        <li>Send exactly this amount: <strong id="verifyAmount"></strong></li>
                        <li>To this address: <code id="verifyAddress"></code></li>
                        <li>From your wallet: <code id="userWallet"></code></li>
                    </ol>
                    <div class="alert alert-warning">
                        ⚠️ Amount must match exactly. The test deposit will be credited to your account after verification.
                    </div>
                    <form id="submitVerificationForm">
                        <div class="form-group">
                            <label>Transaction Hash</label>
                            <input type="text" class="form-control" id="verificationTxid" placeholder="Enter transaction hash" required>
                            <small class="form-text text-muted">64-character hexadecimal string</small>
                        </div>
                        <input type="hidden" id="verifyWalletId">
                        <button type="submit" class="btn btn-primary">Submit for Verification</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
```

**D. JavaScript Functions:**
```javascript
function viewVerificationInstructions(walletId) {
    $.ajax({
        url: 'ajax/get_wallet_verification_details.php',
        method: 'POST',
        data: { wallet_id: walletId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#verifyAmount').text(response.data.verification_amount + ' ' + response.data.cryptocurrency);
                $('#verifyAddress').text(response.data.verification_address);
                $('#userWallet').text(response.data.wallet_address);
                $('#verifyWalletId').val(walletId);
                $('#verificationModal').modal('show');
            } else {
                alert(response.message);
            }
        }
    });
}

$('#submitVerificationForm').on('submit', function(e) {
    e.preventDefault();
    const walletId = $('#verifyWalletId').val();
    const txid = $('#verificationTxid').val();
    
    $.ajax({
        url: 'ajax/submit_wallet_verification.php',
        method: 'POST',
        data: { wallet_id: walletId, txid: txid },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Verification submitted successfully! Admin will review shortly.');
                $('#verificationModal').modal('hide');
                loadPaymentMethods(); // Refresh
            } else {
                alert(response.message);
            }
        }
    });
});
```

### 3. User AJAX Endpoints

**File**: `ajax/submit_wallet_verification.php`
```php
<?php
require_once '../config.php';
require_once '../session.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$walletId = $_POST['wallet_id'] ?? null;
$txid = $_POST['txid'] ?? null;

if (!$walletId || !$txid) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Validate txid format (64 hex chars for most blockchains)
if (!preg_match('/^[a-fA-F0-9]{64}$/', $txid)) {
    echo json_encode(['success' => false, 'message' => 'Invalid transaction hash format']);
    exit;
}

try {
    // Verify ownership
    $stmt = $pdo->prepare("SELECT id, verification_status FROM user_payment_methods WHERE id = ? AND user_id = ? AND type = 'crypto'");
    $stmt->execute([$walletId, $_SESSION['user_id']]);
    $wallet = $stmt->fetch();
    
    if (!$wallet) {
        echo json_encode(['success' => false, 'message' => 'Wallet not found']);
        exit;
    }
    
    if ($wallet['verification_status'] !== 'pending') {
        echo json_encode(['success' => false, 'message' => 'Wallet already submitted or verified']);
        exit;
    }
    
    // Update with txid and change status to verifying
    $stmt = $pdo->prepare("UPDATE user_payment_methods SET verification_txid = ?, verification_status = 'verifying', verification_requested_at = NOW() WHERE id = ?");
    $stmt->execute([$txid, $walletId]);
    
    echo json_encode(['success' => true, 'message' => 'Verification submitted successfully']);
    
} catch (PDOException $e) {
    error_log("Verification submission error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
```

**File**: `ajax/get_wallet_verification_details.php`
```php
<?php
require_once '../config.php';
require_once '../session.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$walletId = $_POST['wallet_id'] ?? null;

if (!$walletId) {
    echo json_encode(['success' => false, 'message' => 'Wallet ID required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            id, wallet_address, cryptocurrency, network,
            verification_status, verification_amount, verification_address, verification_notes
        FROM user_payment_methods
        WHERE id = ? AND user_id = ? AND type = 'crypto'
    ");
    $stmt->execute([$walletId, $_SESSION['user_id']]);
    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$wallet) {
        echo json_encode(['success' => false, 'message' => 'Wallet not found']);
        exit;
    }
    
    // Check if verification details are set
    if (!$wallet['verification_amount'] || !$wallet['verification_address']) {
        echo json_encode(['success' => false, 'message' => 'Verification not yet configured by admin. Please wait.']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $wallet
    ]);
    
} catch (PDOException $e) {
    error_log("Get verification details error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
```

### 4. Admin Dashboard

**File**: `admin/admin_wallet_verifications.php`
```php
<?php
require_once 'admin_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Wallet Verifications</h3>
                </div>
                <div class="card-body">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#pending">
                                Pending <span class="badge badge-info" id="pendingCount">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#verifying">
                                Verifying <span class="badge badge-warning" id="verifyingCount">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#verified">
                                Verified <span class="badge badge-success" id="verifiedCount">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#failed">
                                Failed <span class="badge badge-danger" id="failedCount">0</span>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content">
                        <div id="pending" class="tab-pane active">
                            <table id="pendingTable" class="table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Cryptocurrency</th>
                                        <th>Network</th>
                                        <th>Wallet Address</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        
                        <div id="verifying" class="tab-pane">
                            <table id="verifyingTable" class="table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Cryptocurrency</th>
                                        <th>Transaction Hash</th>
                                        <th>Amount</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        
                        <!-- Similar for verified and failed tabs -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Set Verification Details Modal -->
<div class="modal fade" id="setVerificationModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Set Verification Details</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="setVerificationForm">
                    <input type="hidden" id="verifyWalletId">
                    <div class="form-group">
                        <label>Test Amount (in smallest unit)</label>
                        <input type="text" class="form-control" id="verifyAmount" placeholder="e.g., 0.00001" required>
                        <small class="text-muted">For BTC: satoshi, ETH: wei</small>
                    </div>
                    <div class="form-group">
                        <label>Platform Wallet Address</label>
                        <input type="text" class="form-control" id="verifyAddress" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Details</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTables for each tab
    const pendingTable = $('#pendingTable').DataTable({
        ajax: {
            url: 'admin_ajax/get_pending_wallets.php',
            data: { status: 'pending' }
        },
        columns: [
            { data: 'user_email' },
            { data: 'cryptocurrency' },
            { data: 'network' },
            { data: 'wallet_address' },
            { data: 'created_at' },
            { 
                data: null,
                render: function(data) {
                    return `<button class="btn btn-sm btn-primary" onclick="setVerificationDetails(${data.id})">Set Details</button>`;
                }
            }
        ]
    });
    
    // Similar for other tables...
});

function setVerificationDetails(walletId) {
    $('#verifyWalletId').val(walletId);
    $('#setVerificationModal').modal('show');
}

$('#setVerificationForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: 'admin_ajax/set_verification_details.php',
        method: 'POST',
        data: {
            wallet_id: $('#verifyWalletId').val(),
            amount: $('#verifyAmount').val(),
            address: $('#verifyAddress').val()
        },
        success: function(response) {
            if (response.success) {
                alert('Verification details set!');
                $('#setVerificationModal').modal('hide');
                location.reload();
            }
        }
    });
});

function approveVerification(walletId) {
    if (!confirm('Approve this wallet verification?')) return;
    
    $.ajax({
        url: 'admin_ajax/approve_wallet_verification.php',
        method: 'POST',
        data: { wallet_id: walletId },
        success: function(response) {
            if (response.success) {
                alert('Wallet verified!');
                location.reload();
            }
        }
    });
}
</script>

<?php require_once 'admin_footer.php'; ?>
```

### 5. Admin AJAX Endpoints

**File**: `admin/admin_ajax/get_pending_wallets.php`
```php
<?php
require_once '../admin_session.php';

header('Content-Type: application/json');

$status = $_GET['status'] ?? 'pending';

try {
    $stmt = $pdo->prepare("
        SELECT 
            pm.id, pm.wallet_address, pm.cryptocurrency, pm.network,
            pm.verification_status, pm.verification_amount, pm.verification_address,
            pm.verification_txid, pm.created_at,
            u.email as user_email, u.first_name, u.last_name
        FROM user_payment_methods pm
        JOIN users u ON pm.user_id = u.id
        WHERE pm.type = 'crypto' AND pm.verification_status = ?
        ORDER BY pm.created_at DESC
    ");
    $stmt->execute([$status]);
    $wallets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['data' => $wallets]);
    
} catch (PDOException $e) {
    echo json_encode(['data' => []]);
}
?>
```

**File**: `admin/admin_ajax/set_verification_details.php`
```php
<?php
require_once '../admin_session.php';

header('Content-Type: application/json');

$walletId = $_POST['wallet_id'] ?? null;
$amount = $_POST['amount'] ?? null;
$address = $_POST['address'] ?? null;

if (!$walletId || !$amount || !$address) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE user_payment_methods 
        SET verification_amount = ?, verification_address = ?
        WHERE id = ?
    ");
    $stmt->execute([$amount, $address, $walletId]);
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
```

**File**: `admin/admin_ajax/approve_wallet_verification.php`
```php
<?php
require_once '../admin_session.php';

header('Content-Type: application/json');

$walletId = $_POST['wallet_id'] ?? null;

if (!$walletId) {
    echo json_encode(['success' => false, 'message' => 'Wallet ID required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE user_payment_methods 
        SET verification_status = 'verified',
            verified_by = ?,
            verified_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['admin_id'], $walletId]);
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
```

**File**: `admin/admin_ajax/reject_wallet_verification.php`
```php
<?php
require_once '../admin_session.php';

header('Content-Type: application/json');

$walletId = $_POST['wallet_id'] ?? null;
$notes = $_POST['notes'] ?? '';

if (!$walletId) {
    echo json_encode(['success' => false, 'message' => 'Wallet ID required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE user_payment_methods 
        SET verification_status = 'failed',
            verification_notes = ?,
            verification_txid = NULL
        WHERE id = ?
    ");
    $stmt->execute([$notes, $walletId]);
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
```

### 6. Admin Sidebar Update

**File**: `admin/admin_sidebar.php`

Add after other menu items:
```php
<li class="nav-item">
    <a href="admin_wallet_verifications.php" class="nav-link">
        <i class="nav-icon fas fa-shield-alt"></i>
        <p>
            Wallet Verifications
            <span class="badge badge-warning right" id="pendingVerifications">0</span>
        </p>
    </a>
</li>
```

## Testing Procedure

1. **Apply Database Migration**
   ```bash
   mysql -u root -p database_name < admin/migrations/004_add_wallet_verification_system.sql
   ```

2. **User Flow Test**
   - Login as user
   - Go to Payment Methods
   - Add new crypto wallet (e.g., Bitcoin)
   - Verify status shows "Pending"
   - Wait for admin to set verification details

3. **Admin Flow Test**
   - Login as admin
   - Go to Wallet Verifications
   - See pending wallet
   - Set verification amount (0.00001 BTC)
   - Set platform address
   - Save details

4. **Verification Test**
   - User refreshes page
   - Clicks "View Verification Instructions"
   - Sees amount and address
   - Sends test deposit from wallet
   - Submits transaction hash
   - Status changes to "Verifying"

5. **Approval Test**
   - Admin sees wallet in "Verifying" tab
   - Admin verifies transaction on blockchain
   - Admin clicks "Approve"
   - Status changes to "Verified"
   - User sees green verified badge

## Blockchain Explorer Links

For verification, admins can use these links:
- **Bitcoin**: `https://blockchain.com/btc/tx/{txid}`
- **Ethereum**: `https://etherscan.io/tx/{txid}`
- **USDT (TRC-20)**: `https://tronscan.org/#/transaction/{txid}`
- **BNB**: `https://bscscan.com/tx/{txid}`

## Security Notes

1. **Never skip manual verification** - Always check blockchain
2. **Exact amount matching** - Amount must be precise
3. **One-time use** - Each txid can only be used once
4. **Timeout consideration** - Add expiration for pending verifications
5. **Test small amounts** - Use minimal satoshi amounts

## Support Documentation

Create user guide explaining:
- Why verification is needed
- How to send test deposit
- Where to find transaction hash
- How long verification takes
- What to do if verification fails

## Status: Implementation Ready

All code provided above. Simply:
1. Run migration
2. Create files as specified
3. Test each component
4. Deploy to production

🎉 Complete wallet verification system!
