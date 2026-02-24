<?php
require_once 'config.php';
require_once 'header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_personal'])) {
        // Update personal information
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $phone = trim($_POST['phone']);
        $country = trim($_POST['country']);
        $street = trim($_POST['street']);
        $state = trim($_POST['state']);
        $postalCode = trim($_POST['postal_code']);
        
        try {
            $pdo->beginTransaction();
            
            // Update users table (only name and phone)
            $stmt = $pdo->prepare("UPDATE users SET 
                                  first_name = ?, last_name = ?, phone = ?
                                  WHERE id = ?");
            $stmt->execute([$firstName, $lastName, $phone, $_SESSION['user_id']]);
            
            // Update or insert onboarding record
            if ($onboarding) {
                // Update existing onboarding
                $stmt = $pdo->prepare("UPDATE user_onboarding SET 
                                      country = ?, street = ?, postal_code = ?, state = ?
                                      WHERE user_id = ?");
                $stmt->execute([$country, $street, $postalCode, $state, $_SESSION['user_id']]);
            } else {
                // Create new onboarding record
                $stmt = $pdo->prepare("INSERT INTO user_onboarding 
                                      (user_id, country, street, postal_code, state)
                                      VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $country, $street, $postalCode, $state]);
            }
            
            $pdo->commit();
            $_SESSION['success'] = "Personal information updated successfully!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Error updating personal information: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_bank'])) {
        // Update bank details
        $bankName = trim($_POST['bank_name']);
        $accountHolder = trim($_POST['account_holder']);
        $iban = trim($_POST['iban']);
        $bic = trim($_POST['bic']);
        
        try {
            if ($onboarding) {
                // Update existing onboarding
                $stmt = $pdo->prepare("UPDATE user_onboarding SET 
                                      bank_name = ?, account_holder = ?, iban = ?, bic = ?
                                      WHERE user_id = ?");
                $stmt->execute([$bankName, $accountHolder, $iban, $bic, $_SESSION['user_id']]);
            } else {
                // Create new onboarding record with just bank details
                $stmt = $pdo->prepare("INSERT INTO user_onboarding 
                                      (user_id, bank_name, account_holder, iban, bic)
                                      VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $bankName, $accountHolder, $iban, $bic]);
            }
            
            $_SESSION['success'] = "Bank details updated successfully!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error updating bank details: " . $e->getMessage();
        }
    } elseif (isset($_POST['change_password'])) {
        // Change password
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = "New passwords do not match!";
        } else {
            try {
                // Verify current password
                $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();
                
                if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
                    $_SESSION['error'] = "Current password is incorrect!";
                } else {
                    // Update password
                    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                    $stmt->execute([$newHash, $_SESSION['user_id']]);
                    
                    $_SESSION['success'] = "Password changed successfully!";
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error changing password: " . $e->getMessage();
            }
        }
    }
    
    header("Location: settings.php");
    exit();
}

// Get user data
$user = [];
$onboarding = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT * FROM user_onboarding WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $onboarding = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching user data: " . $e->getMessage();
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
                                <i class="anticon anticon-setting mr-2"></i>Account Settings
                            </h2>
                            <p class="mb-0" style="color: rgba(255,255,255,0.9); font-size: 15px;">
                                Manage your account preferences and security
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <ul class="nav nav-tabs border-0" id="settingsTabs" role="tablist" style="border-bottom: 2px solid #e9ecef !important;">
                                <li class="nav-item">
                                    <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal" role="tab" style="border: none; font-weight: 600; padding: 12px 20px;">
                                        <i class="anticon anticon-user mr-2"></i>Personal Info
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="bank-tab" data-toggle="tab" href="#bank" role="tab" style="border: none; font-weight: 600; padding: 12px 20px;">
                                        <i class="anticon anticon-bank mr-2"></i>Bank Details
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab" style="border: none; font-weight: 600; padding: 12px 20px;">
                                        <i class="anticon anticon-lock mr-2"></i>Password
                                    </a>
                                </li>
                            </ul>
                            
                            <div class="tab-content mt-4" id="settingsTabsContent">
                                <!-- Personal Information Tab -->
                                <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                    <form method="POST">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-600" style="color: #2c3e50;">First Name</label>
                                                    <input type="text" class="form-control" name="first_name" 
                                                           value="<?= htmlspecialchars($user['first_name']) ?>" required
                                                           style="border-radius: 8px; padding: 12px 15px;">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-600" style="color: #2c3e50;">Last Name</label>
                                                    <input type="text" class="form-control" name="last_name" 
                                                           value="<?= htmlspecialchars($user['last_name']) ?>" required
                                                           style="border-radius: 8px; padding: 12px 15px;">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-600" style="color: #2c3e50;">Phone Number</label>
                                                    <input type="tel" class="form-control" name="phone" 
                                                           value="<?= htmlspecialchars($user['phone']) ?>"
                                                           style="border-radius: 8px; padding: 12px 15px;">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-600" style="color: #2c3e50;">Country</label>
                                                    <input type="text" class="form-control" name="country" 
                                                           value="<?= htmlspecialchars($onboarding['country'] ?? '') ?>"
                                                           style="border-radius: 8px; padding: 12px 15px;">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="font-weight-600" style="color: #2c3e50;">Street Address</label>
                                            <input type="text" class="form-control" name="street" 
                                                   value="<?= htmlspecialchars($onboarding['street'] ?? '') ?>"
                                                   style="border-radius: 8px; padding: 12px 15px;">
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-600" style="color: #2c3e50;">City/State</label>
                                                    <input type="text" class="form-control" name="state" 
                                                           value="<?= htmlspecialchars($onboarding['state'] ?? '') ?>"
                                                           style="border-radius: 8px; padding: 12px 15px;">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-600" style="color: #2c3e50;">Postal Code</label>
                                                    <input type="text" class="form-control" name="postal_code" 
                                                           value="<?= htmlspecialchars($onboarding['postal_code'] ?? '') ?>"
                                                           style="border-radius: 8px; padding: 12px 15px;">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" name="update_personal" class="btn btn-primary" style="border-radius: 8px; background: linear-gradient(135deg, #2950a8, #2da9e3); border: none; padding: 12px 30px;">
                                            <i class="anticon anticon-save mr-1"></i>Save Changes
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- Bank Details Tab -->
                                <div class="tab-pane fade" id="bank" role="tabpanel">
                                    <form method="POST">
                                        <div class="form-group">
                                            <label class="font-weight-600" style="color: #2c3e50;">Bank Name</label>
                                            <input type="text" class="form-control" name="bank_name" 
                                                   value="<?= htmlspecialchars($onboarding['bank_name'] ?? '') ?>"
                                                   style="border-radius: 8px; padding: 12px 15px;">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="font-weight-600" style="color: #2c3e50;">Account Holder Name</label>
                                            <input type="text" class="form-control" name="account_holder" 
                                                   value="<?= htmlspecialchars($onboarding['account_holder'] ?? '') ?>"
                                                   style="border-radius: 8px; padding: 12px 15px;">
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-600" style="color: #2c3e50;">IBAN</label>
                                                    <input type="text" class="form-control" name="iban" 
                                                           value="<?= htmlspecialchars($onboarding['iban'] ?? '') ?>"
                                                           style="border-radius: 8px; padding: 12px 15px;">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-600" style="color: #2c3e50;">BIC/SWIFT</label>
                                                    <input type="text" class="form-control" name="bic" 
                                                           value="<?= htmlspecialchars($onboarding['bic'] ?? '') ?>"
                                                           style="border-radius: 8px; padding: 12px 15px;">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" name="update_bank" class="btn btn-primary" style="border-radius: 8px; background: linear-gradient(135deg, #2950a8, #2da9e3); border: none; padding: 12px 30px;">
                                            <i class="anticon anticon-save mr-1"></i>Save Bank Details
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- Password Tab -->
                                <div class="tab-pane fade" id="password" role="tabpanel">
                                    <form method="POST">
                                        <div class="form-group">
                                            <label class="font-weight-600" style="color: #2c3e50;">Current Password</label>
                                            <input type="password" class="form-control" name="current_password" required
                                                   style="border-radius: 8px; padding: 12px 15px;">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="font-weight-600" style="color: #2c3e50;">New Password</label>
                                            <input type="password" class="form-control" name="new_password" required
                                                   style="border-radius: 8px; padding: 12px 15px;">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="font-weight-600" style="color: #2c3e50;">Confirm New Password</label>
                                            <input type="password" class="form-control" name="confirm_password" required
                                                   style="border-radius: 8px; padding: 12px 15px;">
                                        </div>
                                        
                                        <button type="submit" name="change_password" class="btn btn-primary" style="border-radius: 8px; background: linear-gradient(135deg, #2950a8, #2da9e3); border: none; padding: 12px 30px;">
                                            <i class="anticon anticon-lock mr-1"></i>Change Password
                                        </button>
                                    </form>
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