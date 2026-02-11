<?php
require_once 'admin_header.php';

// Get current settings from database
try {
    // Get system settings
    $stmt = $pdo->prepare("SELECT * FROM system_settings LIMIT 1");
    $stmt->execute();
    $systemSettings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get SMTP settings
    $stmt = $pdo->prepare("SELECT * FROM smtp_settings LIMIT 1");
    $stmt->execute();
    $smtpSettings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Merge settings
    $config = $systemSettings ?: [];
    if ($smtpSettings) {
        $config = array_merge($config, $smtpSettings);
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">System Settings</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="admin_dashboard.php" class="breadcrumb-item"><i class="anticon anticon-home"></i> Dashboard</a>
                <span class="breadcrumb-item active">Settings</span>
            </nav>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <h5>General Settings</h5>
            
            <form id="systemSettingsForm">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['admin_csrf_token'] ?>">
                
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Brand Name</label>
                            <input type="text" class="form-control" name="brand_name" 
                                   value="<?= htmlspecialchars($config['brand_name'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Site URL</label>
                            <input type="text" class="form-control" name="site_url" 
                                   value="<?= htmlspecialchars($config['site_url'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" 
                                   value="<?= htmlspecialchars($config['contact_email'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Contact Phone</label>
                            <input type="text" class="form-control" name="contact_phone" 
                                   value="<?= htmlspecialchars($config['contact_phone'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                
                <h5 class="mt-4">SMTP Settings</h5>
                
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>SMTP Host</label>
                            <input type="text" class="form-control" name="smtp_host" 
                                   value="<?= htmlspecialchars($config['host'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>SMTP Port</label>
                            <input type="number" class="form-control" name="smtp_port" 
                                   value="<?= htmlspecialchars($config['port'] ?? 587) ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>SMTP Encryption</label>
                            <select class="form-control" name="smtp_encryption">
                                <option value="tls" <?= ($config['encryption'] ?? 'tls') == 'tls' ? 'selected' : '' ?>>TLS</option>
                                <option value="ssl" <?= ($config['encryption'] ?? 'tls') == 'ssl' ? 'selected' : '' ?>>SSL</option>
                                <option value="" <?= empty($config['encryption'] ?? '') ? 'selected' : '' ?>>None</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>SMTP Username</label>
                            <input type="text" class="form-control" name="smtp_username" 
                                   value="<?= htmlspecialchars($config['username'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>SMTP Password</label>
                            <input type="password" class="form-control" name="smtp_password" 
                                   value="<?= htmlspecialchars($config['password'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>From Email</label>
                            <input type="email" class="form-control" name="smtp_from_email" 
                                   value="<?= htmlspecialchars($config['from_email'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>From Name</label>
                    <input type="text" class="form-control" name="smtp_from_name" 
                           value="<?= htmlspecialchars($config['from_name'] ?? '') ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

<script>
$(document).ready(function() {
    // Save Settings Form Submission
    $('#systemSettingsForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: 'admin_ajax/save_settings.php',
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#systemSettingsForm button[type="submit"]').prop('disabled', true)
                    .html('<i class="anticon anticon-loading anticon-spin"></i> Saving...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            complete: function() {
                $('#systemSettingsForm button[type="submit"]').prop('disabled', false).html('Save Settings');
            }
        });
    });
});
</script>