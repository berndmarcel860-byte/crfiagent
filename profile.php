<?php
require_once 'config.php';
require_once 'header.php';

// Handle email verification resend
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_verification'])) {
    // Generate new verification token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET verification_token = ?, reset_expires = ? WHERE id = ?");
        $stmt->execute([$token, $expires, $_SESSION['user_id']]);
        
        // Send verification email (implementation depends on your email system)
        // sendVerificationEmail($user['email'], $token);
        
        $_SESSION['success'] = "Verification email sent! Please check your inbox.";
        header("Location: profile.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error resending verification: " . $e->getMessage();
    }
}

// Get user data with onboarding info
$user = [];
$onboarding = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT * FROM user_onboarding WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $onboarding = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get KYC status
    $stmt = $pdo->prepare("SELECT * FROM kyc_verification_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $kyc = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching profile data: " . $e->getMessage();
}
?>

<!--<div class="page-container">-->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #2950a8 0%, #2da9e3 100%); color: #fff;">
                        <div class="card-body py-4">
                            <h2 class="mb-2 text-white" style="font-weight: 700;">
                                <i class="anticon anticon-user mr-2"></i>My Profile
                            </h2>
                            <p class="mb-0" style="color: rgba(255,255,255,0.9); font-size: 15px;">
                                View and manage your account information
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="settings.php" class="btn btn-primary" style="border-radius: 8px; background: linear-gradient(135deg, #2950a8, #2da9e3); border: none;">
                            <i class="anticon anticon-edit mr-1"></i>Edit Profile
                        </a>
                    </div>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center p-4">
                                        <div class="avatar avatar-image mx-auto mb-3" style="width: 120px; height: 120px; border-radius: 50%; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                                            <img src="<?= htmlspecialchars($avatar) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <h4 class="mb-2 font-weight-bold" style="color: #2c3e50;"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h4>
                                        <p class="text-muted mb-4"><i class="anticon anticon-calendar mr-1"></i>Member Since: <?= date('M Y', strtotime($user['created_at'])) ?></p>
                                        
                                        <div class="text-left">
                                            <div class="mb-3">
                                                <label class="text-muted mb-1" style="font-size: 13px;">Email Address</label>
                                                <p class="mb-0"><strong><?= htmlspecialchars($user['email']) ?></strong>
                                                    <?php if ($user['is_verified']): ?>
                                                        <span class="badge badge-success ml-2"><i class="anticon anticon-check-circle mr-1"></i>Verified</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning ml-2">Unverified</span>
                                                        <form method="POST" class="mt-2">
                                                            <button type="submit" name="resend_verification" class="btn btn-sm btn-primary" style="border-radius: 6px;">
                                                                <i class="anticon anticon-mail mr-1"></i>Resend Verification
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            
                                            <?php if ($user['phone']): ?>
                                            <div class="mb-3">
                                                <label class="text-muted mb-1" style="font-size: 13px;">Phone Number</label>
                                                <p class="mb-0"><strong><?= htmlspecialchars($user['phone']) ?></strong>
                                                    <?= $user['phone_verified'] ? '<span class="badge badge-success ml-2"><i class="anticon anticon-check-circle mr-1"></i>Verified</span>' : '' ?>
                                                </p>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card border-0 shadow-sm mt-3">
                                    <div class="card-body">
                                        <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                            <i class="anticon anticon-safety-certificate mr-2" style="color: var(--brand);"></i>KYC Verification
                                        </h5>
                                        <?php if ($kyc): ?>
                                            <div class="mb-2">
                                                <label class="text-muted mb-1" style="font-size: 13px;">Verification Status</label>
                                                <div>
                                                    <span class="badge badge-<?= 
                                                        $kyc['status'] == 'approved' ? 'success' : 
                                                        ($kyc['status'] == 'rejected' ? 'danger' : 'warning') 
                                                    ?> px-3 py-2">
                                                        <i class="anticon anticon-<?= 
                                                            $kyc['status'] == 'approved' ? 'check-circle' : 
                                                            ($kyc['status'] == 'rejected' ? 'close-circle' : 'clock-circle') 
                                                        ?> mr-1"></i><?= ucfirst($kyc['status']) ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <?php if ($kyc['status'] == 'rejected' && $kyc['rejection_reason']): ?>
                                                <p class="text-danger">Reason: <?= htmlspecialchars($kyc['rejection_reason']) ?></p>
                                            <?php endif; ?>
                                            <a href="kyc.php" class="btn btn-sm btn-primary">
                                                <?= $kyc['status'] == 'rejected' ? 'Resubmit KYC' : 'View KYC Status' ?>
                                            </a>
                                        <?php else: ?>
                                            <p>You haven't completed KYC verification yet.</p>
                                            <a href="kyc.php" class="btn btn-sm btn-primary">Complete KYC</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Personal Information</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>First Name:</strong> <?= htmlspecialchars($user['first_name']) ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Last Name:</strong> <?= htmlspecialchars($user['last_name']) ?></p>
                                            </div>
                                        </div>
                                        
                                        <?php if ($onboarding): ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Country:</strong> <?= htmlspecialchars($onboarding['country']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>State:</strong> <?= htmlspecialchars($onboarding['state']) ?></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Street:</strong> <?= htmlspecialchars($onboarding['street']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Postal Code:</strong> <?= htmlspecialchars($onboarding['postal_code']) ?></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if ($onboarding && ($onboarding['bank_name'] || $onboarding['account_holder'])): ?>
                                <div class="card m-t-20">
                                    <div class="card-body">
                                        <h5>Bank Details</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Bank Name:</strong> <?= htmlspecialchars($onboarding['bank_name']) ?></p>
                                                <p><strong>Account Holder:</strong> <?= htmlspecialchars($onboarding['account_holder']) ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>IBAN:</strong> <?= htmlspecialchars($onboarding['iban']) ?></p>
                                                <p><strong>BIC/SWIFT:</strong> <?= htmlspecialchars($onboarding['bic']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="card m-t-20">
                                    <div class="card-body">
                                        <h5>Account Activity</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Last Login:</strong> 
                                                    <?= $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never' ?>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Account Created:</strong> <?= date('M d, Y', strtotime($user['created_at'])) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--</div>-->

<?php require_once 'footer.php'; ?>