<script>
$(function(){
    // Tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // animate counts
    function animateCount(el, start, end, decimals, duration) {
        decimals = decimals || 0;
        var current = start;
        var range = end - start;
        var increment = range / (duration / 30);
        var timer = setInterval(function() {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }
            $(el).text((decimals ? current.toFixed(decimals) : Math.round(current)).toString() + (el.dataset.suffix || ''));
        }, 30);
    }

    $('.count').each(function(){
        var $el = $(this);
        var end = parseFloat($el.data('value')) || 0;
        var start = 0;
        var decimals = (String(end).indexOf('.') !== -1) ? 2 : 0;
        animateCount(this, start, end, decimals, 700);
    });

    var $balance = $('#balanceCounter');
    if ($balance.length) {
        var bval = parseFloat($balance.data('value')) || 0;
        animateCount($balance[0], 0, bval, 2, 800);
    }

    function animateLiveProgress(el) {
        var $bar = $(el);
        var finalVal = parseFloat($bar.data('final')) || 0;
        var progressLabel = $bar.parent().find('[data-progress-label]');
        var live = 0;
        $bar.css('width', live + '%');
        
        var step = function() {
            live += Math.max(0.5, (finalVal-live)/6);
            if (live >= finalVal) {
                live = finalVal;
                $bar.css('width', finalVal + '%');
                progressLabel.text(finalVal + '%');
            } else {
                $bar.css('width', live + '%');
                progressLabel.text(Math.round(live * 100) / 100 + '%');
                setTimeout(step, 60 + Math.random()*60);
            }
        };
        setTimeout(step, 200 + Math.random()*200);
    }
    $('.live-progress').each(function() { animateLiveProgress(this); });

    // Copy wallet address
    $(document).on('click', '#copyWalletAddress', function() {
        var walletAddress = $('#detail-wallet-address').val();
        if (!walletAddress) { toastr.warning('No address to copy'); return; }
        navigator.clipboard.writeText(walletAddress).then(function() {
            toastr.success('Wallet address copied to clipboard');
        }, function() {
            toastr.error('Failed to copy wallet address');
        });
    });

    // Payment method change
    $('#paymentMethod').change(function() {
        var selectedOption = $(this).find('option:selected');
        var details = selectedOption.data('details');
        var $paymentDetails = $('#paymentDetails');
        
        if (!details) {
            $paymentDetails.hide();
            return;
        }
        
        if (typeof details === 'string') {
            try {
                details = JSON.parse(details);
            } catch (e) {
                console.error('Error parsing payment details:', e);
                return;
            }
        }
        
        $('#bankDetails, #cryptoDetails, #generalInstructions').hide();
        
        if (details.bank_name) {
            $('#detail-bank-name').text(details.bank_name);
            $('#detail-account-number').text(details.account_number || '-');
            $('#detail-routing-number').text(details.routing_number || '-');
            $('#bankDetails').show();
        }
        
        if (details.wallet_address) {
            $('#detail-wallet-address').val(details.wallet_address);
            $('#cryptoDetails').show();
        }
        
        if (details.instructions) {
            $('#detail-instructions').text(details.instructions);
            $('#generalInstructions').show();
        }
        
        $paymentDetails.show();
    });

    // File input label
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Deposit submit
    $('#depositForm').submit(function(e) {
        e.preventDefault();
        var $form = $(this);
        var formData = new FormData($form[0]);
        var $submitBtn = $form.find('button[type="submit"]');
        
        $submitBtn.prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin"></i> Processing...');
        
        $.ajax({
            url: 'ajax/process-deposit.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    var data = typeof response === 'string' ? JSON.parse(response) : response;
                    if (data.success) {
                        toastr.success(data.message || 'Deposit submitted successfully');
                        $('#newDepositModal').modal('hide');
                        $form[0].reset();
                        $('.custom-file-label').html('Choose file');
                        $('#paymentDetails').hide();
                        setTimeout(function(){ location.reload(); }, 1200);
                    } else {
                        toastr.error(data.message || 'Error processing deposit');
                    }
                } catch (e) {
                    toastr.error('Error parsing server response');
                }
                $submitBtn.prop('disabled', false).html('Confirm Deposit');
            },
            error: function(xhr, status, error) {
                toastr.error('Error communicating with server: ' + error);
                $submitBtn.prop('disabled', false).html('Confirm Deposit');
            }
        });
    });

// =====================================================
// 💸 WITHDRAWAL FORM SUBMIT (WITH OTP + BALANCE CHECK)
// =====================================================
$('#withdrawalForm').submit(function (e) {
    e.preventDefault();
    const $form = $(this);
    const $submitBtn = $form.find('button[type="submit"]');

    // Ensure OTP verified (button enabled only after verification)
    if ($('#withdrawalSubmitBtn').prop('disabled')) {
        toastr.warning('Please verify your OTP before submitting.');
        return;
    }

    // Validate balance and amount before sending
    const available = parseFloat($('#availableBalance').val()) || 0;
    const amount = parseFloat($('#amount').val()) || 0;
    if (available < 1000) {
        toastr.error('Insufficient funds. Minimum balance required is €1000.');
        return;
    }
    if (amount < 1000) {
        toastr.error('Minimum withdrawal amount is €1000.');
        return;
    }
    if (amount > available) {
        toastr.error('Insufficient balance. Available: €' + available.toFixed(2));
        return;
    }

    // Send request
    $submitBtn.prop('disabled', true)
        .html('<i class="anticon anticon-loading anticon-spin"></i> Processing...');

    $.ajax({
        url: 'ajax/process-withdrawal.php',
        method: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                toastr.success(response.message || 'Withdrawal request submitted successfully');
                $('#newWithdrawalModal').modal('hide');
                $form[0].reset();
                resetOtpFields();
                setTimeout(() => location.reload(), 1200);
            } else {
                toastr.error(response.message || 'Error processing withdrawal');
                if (response.message && response.message.includes('OTP')) resetOtpFields();
            }
        },
        error: function (xhr, status, error) {
            console.error('Withdrawal error:', xhr.status, xhr.responseText);
            let errorMsg = 'Server communication error: ' + error;
            
            // Try to parse error response
            try {
                const errorData = JSON.parse(xhr.responseText);
                if (errorData.message) {
                    errorMsg = errorData.message;
                }
            } catch (e) {
                // If response isn't JSON, use status text
                if (xhr.status === 400) {
                    errorMsg = 'Bad Request - Please check your input fields';
                } else if (xhr.status === 403) {
                    errorMsg = 'Security error - Please refresh the page';
                } else if (xhr.status === 401) {
                    errorMsg = 'Session expired - Please login again';
                }
            }
            
            toastr.error(errorMsg);
        },
        complete: function () {
            $submitBtn.prop('disabled', false).html('Submit Request');
        }
    });
});


// =====================================================
// 🏦 WITHDRAWAL METHOD AUTO-FILL (USER'S VERIFIED ADDRESSES)
// =====================================================
$('#withdrawalMethod').change(function () {
    const $selected = $(this).find('option:selected');
    const details = $selected.data('details') || '';
    const type = $selected.data('type') || '';
    
    // Auto-fill payment details textarea with user's verified address/account
    if (details) {
        $('textarea[name="payment_details"]').val(details);
        toastr.success('Payment details auto-filled with your verified ' + (type === 'crypto' ? 'address' : 'account'));
    } else {
        $('textarea[name="payment_details"]').val('');
    }
    
    // Hide bank details container (no longer needed with direct auto-fill)
    $('#bankDetailsContainer').hide();
});


// =====================================================
// 💵 LIVE BALANCE CHECK (REAL DB VALUE)
// =====================================================
$('#amount').on('input', function () {
    const amount = parseFloat($(this).val()) || 0;
    const available = parseFloat($('#availableBalance').val()) || 0;

    $('#insufficientFundsWarning').remove();

    // Case 1: Balance too low to withdraw
    if (available < 1000) {
        $(this).closest('.form-group').append(`
            <div id="insufficientFundsWarning" class="alert alert-danger mt-2 p-2 mb-0">
                <i class="anticon anticon-warning"></i>
                You need at least €1000 available to withdraw. Current balance: €${available.toFixed(2)}
            </div>
        `);
        $('#sendVerifyOtpBtn, #withdrawalSubmitBtn').prop('disabled', true);
        return;
    }

    // Case 2: Amount greater than available
    if (amount > available) {
        $(this).closest('.form-group').append(`
            <div id="insufficientFundsWarning" class="alert alert-danger mt-2 p-2 mb-0">
                <i class="anticon anticon-warning"></i>
                Insufficient balance: available €${available.toFixed(2)}
            </div>
        `);
        $('#sendVerifyOtpBtn, #withdrawalSubmitBtn').prop('disabled', true);
        return;
    }

    // Case 3: Amount below minimum
    if (amount > 0 && amount < 1000) {
        $(this).closest('.form-group').append(`
            <div id="insufficientFundsWarning" class="alert alert-warning mt-2 p-2 mb-0">
                <i class="anticon anticon-info-circle"></i>
                Minimum withdrawal amount is €1000.
            </div>
        `);
        $('#sendVerifyOtpBtn, #withdrawalSubmitBtn').prop('disabled', true);
        return;
    }

    // ✅ All good
    $('#insufficientFundsWarning').remove();
    $('#sendVerifyOtpBtn').prop('disabled', false);
});


// =====================================================
// 🔐 COMBINED OTP SEND & VERIFY
// =====================================================
let otpSent = false;

$('#sendVerifyOtpBtn').click(function () {
    const $btn = $(this);
    const $otpInput = $('#otpCode');
    
    // Step 1: Send OTP if not sent yet
    if (!otpSent) {
        $btn.prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin"></i> Sending OTP...');
        $.ajax({
            url: 'ajax/otp-handler.php',
            method: 'POST',
            data: {
                action: 'send',
                csrf_token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function (r) {
                if (r.success) {
                    toastr.success(r.message || 'OTP sent to your email');
                    $otpInput.prop('disabled', false).focus();
                    otpSent = true;
                    $btn.prop('disabled', false).html('<i class="anticon anticon-check-circle"></i> Verify OTP');
                    $('#otpInfoText').html('<i class="anticon anticon-clock-circle"></i> OTP sent! Enter the code and click "Verify OTP" button.');
                } else {
                    toastr.error(r.message || 'Failed to send OTP');
                    $btn.prop('disabled', false).html('<i class="anticon anticon-mail"></i> Send & Verify OTP');
                }
            },
            error: function (xhr, status, error) {
                console.error('OTP send error:', xhr.status, xhr.responseText);
                toastr.error('Failed to send OTP. Please try again.');
                $btn.prop('disabled', false).html('<i class="anticon anticon-mail"></i> Send & Verify OTP');
            }
        });
    } 
    // Step 2: Verify OTP
    else {
        const code = $otpInput.val().trim();
        if (!code || code.length !== 6) {
            toastr.error('Please enter the 6-digit OTP code.');
            return;
        }
        
        $btn.prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin"></i> Verifying...');
        $.ajax({
            url: 'ajax/otp-handler.php',
            method: 'POST',
            data: {
                action: 'verify',
                otp_code: code,
                csrf_token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function (r) {
                if (r.success) {
                    toastr.success(r.message || 'OTP verified successfully');
                    $('#withdrawalSubmitBtn').prop('disabled', false);
                    $otpInput.prop('disabled', true);
                    $btn.prop('disabled', true).html('<i class="anticon anticon-check"></i> Verified').removeClass('btn-primary').addClass('btn-success');
                    $('#otpInfoText').html('<i class="anticon anticon-check-circle text-success"></i> Email verified! You can now submit your withdrawal request.');
                } else {
                    toastr.error(r.message || 'Invalid OTP code');
                    $btn.prop('disabled', false).html('<i class="anticon anticon-check-circle"></i> Verify OTP');
                }
            },
            error: function (xhr, status, error) {
                console.error('OTP verify error:', xhr.status, xhr.responseText);
                toastr.error('OTP verification failed. Please try again.');
                $btn.prop('disabled', false).html('<i class="anticon anticon-check-circle"></i> Verify OTP');
            }
        });
    }
});


// =====================================================
// 🧹 RESET OTP FIELDS ON MODAL CLOSE
// =====================================================
$('#newWithdrawalModal').on('hidden.bs.modal', function () {
    resetOtpFields();
});

function resetOtpFields() {
    $('#otpCode').val('').prop('disabled', true);
    $('#sendVerifyOtpBtn').prop('disabled', false).html('<i class="anticon anticon-mail"></i> Send & Verify OTP').removeClass('btn-success').addClass('btn-primary');
    $('#withdrawalSubmitBtn').prop('disabled', true);
    $('#otpInfoText').html('<i class="anticon anticon-info-circle"></i> OTP is valid for 5 minutes. Click button to send code to your email.');
    otpSent = false;
}

    // Refresh algorithm
    $('#refresh-algorithm').click(function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin"></i> Refreshing...');
        
        setTimeout(function() {
            $.ajax({
                url: 'ajax/get_recovery_status.php',
                method: 'GET',
                success: function(response) {
                    try {
                        var data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            if (data.recoveryPercentage !== undefined) {
                                $('.algorithm-progress .progress-bar').css('width', data.recoveryPercentage + '%');
                                $('.count[data-value="<?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>"]').text(data.recoveryPercentage + '%');
                            }
                            toastr.success('Status refreshed successfully');
                        } else {
                            toastr.error(data.message || 'Error refreshing status');
                        }
                    } catch (e) {
                        toastr.error('Error parsing server response');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Error communicating with server: ' + error);
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="anticon anticon-sync"></i> Refresh Status');
                }
            });
        }, 400);
    });

    // Background refresh for AI status and balance (optional)
    function bgRefresh() {
        $.ajax({
            url: 'ajax/bg_status.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    if (data.aiStatus) {
                        $('#aiStatusText').text(data.aiStatus);
                    }
                    if (data.lastScan) {
                        $('#lastScanText').text(data.lastScan);
                    }
                    if (data.balance !== undefined) {
                        var b = parseFloat(data.balance) || 0;
                        animateCount($('#balanceCounter')[0], parseFloat($('#balanceCounter').text().replace(/[^\d.-]/g,'')) || 0, b, 2, 600);
                    }
                }
            }
        }).always(function(){
            setTimeout(bgRefresh, 30000);
        });
    }
    // Start bgRefresh only if endpoint exists in your system; safe to comment out if not present.
    bgRefresh();

    // Password modal interactions (if present)
    <?php if ($passwordChangeRequired): ?>
    $('#newPassword').on('input', function() {
        const val = $(this).val();
        const $bar = $('#passwordStrengthBar');
        const $text = $('#passwordStrengthText');

        let score = 0;
        const req = {
            length: val.length >= 8,
            upper: /[A-Z]/.test(val),
            number: /[0-9]/.test(val),
            special: /[^A-Za-z0-9]/.test(val)
        };

        for (let key in req) {
            const $item = $('#req-' + key);
            if (req[key]) {
                $item.removeClass('text-danger').addClass('text-success')
                     .html('<i class="anticon anticon-check"></i> ' + $item.text().replace(/^[✓✗]\s*/, ''));
                score++;
            } else {
                $item.removeClass('text-success').addClass('text-danger')
                     .html('<i class="anticon anticon-close"></i> ' + $item.text().replace(/^[✓✗]\s*/, ''));
            }
        }

        const width = (score / 4) * 100;
        let colorClass, label;
        switch (score) {
            case 0:
            case 1: colorClass = 'bg-danger'; label = 'Weak'; break;
            case 2: colorClass = 'bg-warning'; label = 'Fair'; break;
            case 3: colorClass = 'bg-info'; label = 'Good'; break;
            case 4: colorClass = 'bg-success'; label = 'Strong'; break;
        }

        $bar.removeClass('bg-danger bg-warning bg-info bg-success')
            .addClass(colorClass)
            .css('width', width + '%');
        $text.text('Strength: ' + label);

        $('#confirmPassword').trigger('input');
    });

    $('#confirmPassword, #newPassword').on('input', function() {
        const newPass = $('#newPassword').val();
        const confirm = $('#confirmPassword').val();
        const $match = $('#passwordMatchText');

        if (!confirm) {
            $match.text('Waiting for input...').removeClass('text-success text-danger').addClass('text-muted');
            return;
        }

        if (confirm === newPass) {
            $match.text('Passwords match ✅').removeClass('text-danger text-muted').addClass('text-success');
        } else {
            $match.text('Passwords do not match ❌').removeClass('text-success text-muted').addClass('text-danger');
        }
    });

    $('#submitPasswordChange').click(function() {
        const currentPassword = $('#currentPassword').val();
        const newPassword = $('#newPassword').val();
        const confirmPassword = $('#confirmPassword').val();

        if (!currentPassword || !newPassword || !confirmPassword) {
            toastr.error('All fields are required');
            return;
        }
        if (newPassword !== confirmPassword) {
            toastr.error('New passwords do not match');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin"></i> Processing...');

        $.ajax({
            url: 'change_password.php',
            method: 'POST',
            dataType: 'json',
            data: {
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword,
                force_change: 1,
                csrf_token: '<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>'
            },
            success: function(data) {
                if (data.success) {
                    toastr.success(data.message || 'Password changed successfully');
                    $('#passwordChangeModal').modal('hide');
                    $('.modal-backdrop').remove();
                    setTimeout(function(){ location.reload(); }, 800);
                } else {
                    toastr.error(data.message || 'Error changing password');
                }
                $btn.prop('disabled', false).html('<i class="anticon anticon-save"></i> Change Password');
            },
            error: function(xhr, status, error) {
                toastr.error('Server error: ' + error);
                $btn.prop('disabled', false).html('<i class="anticon anticon-save"></i> Change Password');
            }
        });
    });
    <?php endif; ?>

    // Print receipt
    $('#printReceiptBtn').click(function(){ window.print(); });

    // =====================================================
    // 📋 VIEW CASE DETAILS MODAL
    // =====================================================
    $('.view-case-btn').click(function() {
        const caseId = $(this).data('case-id');
        $('#caseDetailsModal').modal('show');
        
        // Reset modal body
        $('#caseModalBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading case details...</p>
            </div>
        `);
        
        // Fetch case details via AJAX
        $.ajax({
            url: 'ajax/get-case.php',
            method: 'GET',
            data: { id: caseId },
            success: function(response) {
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    if (data.success && data.case) {
                        const c = data.case;
                        const progress = c.reported_amount > 0 ? Math.round((c.recovered_amount / c.reported_amount) * 100) : 0;
                        
                        const statusClass = {
                            'open': 'warning',
                            'documents_required': 'secondary',
                            'under_review': 'info',
                            'refund_approved': 'success',
                            'refund_rejected': 'danger',
                            'closed': 'dark'
                        }[c.status] || 'light';
                        
                        const html = `
                            <div class="case-details-content">
                                <!-- Header Info -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card border-0" style="background: rgba(41, 80, 168, 0.05);">
                                            <div class="card-body">
                                                <h6 class="text-muted mb-2" style="font-size: 12px; text-transform: uppercase;">Case Number</h6>
                                                <h4 class="mb-0 font-weight-bold" style="color: var(--brand);">${c.case_number || 'N/A'}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0" style="background: rgba(41, 80, 168, 0.05);">
                                            <div class="card-body">
                                                <h6 class="text-muted mb-2" style="font-size: 12px; text-transform: uppercase;">Status</h6>
                                                <span class="badge badge-${statusClass} px-3 py-2" style="font-size: 14px;">
                                                    <i class="anticon anticon-flag mr-1"></i>${c.status ? c.status.replace(/_/g, ' ').toUpperCase() : 'N/A'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Financial Overview -->
                                <div class="card border-0 mb-4" style="background: linear-gradient(135deg, rgba(41, 80, 168, 0.05), rgba(45, 169, 227, 0.05));">
                                    <div class="card-body">
                                        <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                            <i class="anticon anticon-dollar mr-2" style="color: var(--brand);"></i>Financial Overview
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="text-muted mb-1" style="font-size: 13px;">Reported Amount</div>
                                                <h4 class="mb-0 font-weight-bold text-danger">$${parseFloat(c.reported_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h4>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-muted mb-1" style="font-size: 13px;">Recovered Amount</div>
                                                <h3 class="mb-2 font-weight-bold" style="color: #2c3e50;">$${parseFloat(c.recovered_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h3>
                                                <div class="progress mb-2" style="height: 8px; border-radius: 10px; background: #e9ecef;">
                                                    <div class="progress-bar" style="width: ${progress}%; background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%);"></div>
                                                </div>
                                                <small class="text-muted">${progress}% of $${parseFloat(c.reported_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Platform Info -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card border-0 h-100">
                                            <div class="card-body">
                                                <h6 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                                    <i class="anticon anticon-global mr-2" style="color: var(--brand);"></i>Platform Information
                                                </h6>
                                                <p class="mb-2"><strong>Platform:</strong> ${c.platform_name || 'N/A'}</p>
                                                <p class="mb-0"><strong>Created:</strong> ${c.created_at ? new Date(c.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'}) : 'N/A'}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 h-100">
                                            <div class="card-body">
                                                <h6 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                                    <i class="anticon anticon-clock-circle mr-2" style="color: var(--brand);"></i>Timeline
                                                </h6>
                                                <p class="mb-2"><strong>Last Updated:</strong> ${c.updated_at ? new Date(c.updated_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'}) : 'N/A'}</p>
                                                <p class="mb-0"><strong>Days Active:</strong> ${c.created_at ? Math.floor((new Date() - new Date(c.created_at)) / (1000 * 60 * 60 * 24)) : 0} days</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Description -->
                                ${c.description ? `
                                <div class="card border-0 mb-4">
                                    <div class="card-body">
                                        <h6 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                            <i class="anticon anticon-file-text mr-2" style="color: var(--brand);"></i>Case Description
                                        </h6>
                                        <p class="mb-0" style="line-height: 1.6;">${c.description}</p>
                                    </div>
                                </div>
                                ` : ''}
                                
                                <!-- Recovery Transactions -->
                                ${data.recoveries && data.recoveries.length > 0 ? `
                                <div class="card border-0 mb-4">
                                    <div class="card-body">
                                        <h6 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                            <i class="anticon anticon-transaction mr-2" style="color: var(--brand);"></i>Recovery Transactions
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead style="background: rgba(41, 80, 168, 0.05);">
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                        <th>Method</th>
                                                        <th>Reference</th>
                                                        <th>Processed By</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${data.recoveries.map(r => `
                                                        <tr>
                                                            <td>${r.transaction_date ? new Date(r.transaction_date).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'}) : 'N/A'}</td>
                                                            <td><strong class="text-success">$${parseFloat(r.amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong></td>
                                                            <td>${r.method || 'N/A'}</td>
                                                            <td><small class="text-muted">${r.transaction_reference || 'N/A'}</small></td>
                                                            <td>${r.admin_first_name && r.admin_last_name ? `${r.admin_first_name} ${r.admin_last_name}` : 'System'}</td>
                                                        </tr>
                                                    `).join('')}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                ` : ''}
                                
                                <!-- Documents -->
                                ${data.documents && data.documents.length > 0 ? `
                                <div class="card border-0 mb-4">
                                    <div class="card-body">
                                        <h6 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                            <i class="anticon anticon-paper-clip mr-2" style="color: var(--brand);"></i>Case Documents
                                        </h6>
                                        <div class="list-group">
                                            ${data.documents.map(d => `
                                                <div class="list-group-item border-0 px-0">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <i class="anticon anticon-file mr-2" style="color: var(--brand);"></i>
                                                            <strong>${d.document_type || 'Document'}</strong>
                                                            ${d.verified ? '<span class="badge badge-success badge-sm ml-2"><i class="anticon anticon-check"></i> Verified</span>' : ''}
                                                        </div>
                                                        <small class="text-muted">${d.uploaded_at ? new Date(d.uploaded_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'}) : ''}</small>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                </div>
                                ` : ''}
                                
                                <!-- Status History -->
                                ${data.history && data.history.length > 0 ? `
                                <div class="card border-0 mb-4">
                                    <div class="card-body">
                                        <h6 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                            <i class="anticon anticon-history mr-2" style="color: var(--brand);"></i>Status History
                                        </h6>
                                        <div class="timeline">
                                            ${data.history.map((h, idx) => `
                                                <div class="timeline-item ${idx === 0 ? 'timeline-item-active' : ''}">
                                                    <div class="timeline-marker ${idx === 0 ? 'bg-primary' : 'bg-secondary'}"></div>
                                                    <div class="timeline-content">
                                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                                            <strong>${h.new_status ? h.new_status.replace(/_/g, ' ').toUpperCase() : 'Status Change'}</strong>
                                                            <small class="text-muted">${h.created_at ? new Date(h.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'}) : ''}</small>
                                                        </div>
                                                        ${h.comments ? `<p class="mb-1 text-muted small">${h.comments}</p>` : ''}
                                                        ${h.first_name && h.last_name ? `<small class="text-muted">By: ${h.first_name} ${h.last_name}</small>` : ''}
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                </div>
                                ` : ''}
                                
                                <!-- Actions -->
                                <div class="text-center mt-4">
                                    <a href="cases.php" class="btn btn-primary">
                                        <i class="anticon anticon-folder-open mr-1"></i>View All Cases
                                    </a>
                                </div>
                            </div>
                        `;
                        
                        $('#caseModalBody').html(html);
                        $('#caseDetailsModalLabel').html(`<i class="anticon anticon-file-text mr-2"></i>Case #${c.case_number || 'Details'}`);
                    } else {
                        $('#caseModalBody').html(`
                            <div class="alert alert-danger">
                                <i class="anticon anticon-close-circle mr-2"></i>${data.message || 'Unable to load case details'}
                            </div>
                        `);
                    }
                } catch (e) {
                    $('#caseModalBody').html(`
                        <div class="alert alert-danger">
                            <i class="anticon anticon-close-circle mr-2"></i>Error parsing case data
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                $('#caseModalBody').html(`
                    <div class="alert alert-danger">
                        <i class="anticon anticon-close-circle mr-2"></i>Error loading case details: ${error}
                    </div>
                `);
            }
        });
    });

    // Charts removed per user request

    // Animated Counter Function
    function animateCounter(element) {
        const target = parseFloat(element.getAttribute('data-value')) || 0;
        const duration = 1500; // 1.5 seconds
        const start = 0;
        const startTime = performance.now();
        
        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function (easeOutQuart)
            const easeOut = 1 - Math.pow(1 - progress, 4);
            const current = start + (target - start) * easeOut;
            
            // Format based on whether it's a decimal or integer
            if (element.classList.contains('money')) {
                element.textContent = '$' + current.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            } else if (element.classList.contains('percent')) {
                element.textContent = current.toFixed(1) + '%';
            } else {
                element.textContent = Math.floor(current).toLocaleString();
            }
            
            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }
        
        requestAnimationFrame(update);
    }

    // Initialize counters with Intersection Observer for better performance
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    entry.target.classList.add('counted');
                    animateCounter(entry.target);
                }
            });
        }, { threshold: 0.5 });

        // Observe all counter elements
        document.querySelectorAll('.count').forEach(el => observer.observe(el));
    } else {
        // Fallback for older browsers
        document.querySelectorAll('.count').forEach(el => animateCounter(el));
    }

    // Add smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading animation to buttons on click
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.classList.contains('no-loading')) {
                this.style.pointerEvents = 'none';
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="anticon anticon-loading anticon-spin mr-1"></i>' + this.textContent;
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.style.pointerEvents = '';
                }, 2000);
            }
        });
    });

    // =====================================================
    // 📧 EMAIL VERIFICATION - AJAX HANDLER
    // =====================================================
    let emailVerificationCooldown = false;
    
    $('#sendVerificationEmailBtn').on('click', function(e) {
        e.preventDefault();
        
        if (emailVerificationCooldown) {
            return;
        }
        
        const $btn = $(this);
        const $statusDiv = $('#verificationEmailStatus');
        const originalBtnText = $btn.html();
        
        // Disable button and show loading
        $btn.prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin mr-1"></i>Sending...');
        $statusDiv.empty();
        
        $.ajax({
            url: 'ajax/send_verification_email.php',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $statusDiv.html(`
                        <div class="alert alert-success alert-sm border-0 mt-2" style="font-size: 13px;">
                            <i class="anticon anticon-check-circle mr-1"></i>${response.message}
                        </div>
                    `);
                    
                    // Set cooldown for 60 seconds
                    emailVerificationCooldown = true;
                    let countdown = 60;
                    $btn.html(`<i class="anticon anticon-clock-circle mr-1"></i>Resend in ${countdown}s`);
                    
                    const countdownInterval = setInterval(() => {
                        countdown--;
                        if (countdown <= 0) {
                            clearInterval(countdownInterval);
                            emailVerificationCooldown = false;
                            $btn.prop('disabled', false).html(originalBtnText);
                        } else {
                            $btn.html(`<i class="anticon anticon-clock-circle mr-1"></i>Resend in ${countdown}s`);
                        }
                    }, 1000);
                } else {
                    $statusDiv.html(`
                        <div class="alert alert-danger alert-sm border-0 mt-2" style="font-size: 13px;">
                            <i class="anticon anticon-close-circle mr-1"></i>${response.message}
                        </div>
                    `);
                    $btn.prop('disabled', false).html(originalBtnText);
                }
            },
            error: function(xhr, status, error) {
                $statusDiv.html(`
                    <div class="alert alert-danger alert-sm border-0 mt-2" style="font-size: 13px;">
                        <i class="anticon anticon-close-circle mr-1"></i>Error sending email. Please try again later.
                    </div>
                `);
                $btn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
});

// =====================================================
// 💳 WITHDRAWAL ELIGIBILITY CHECK
// =====================================================
function checkWithdrawalEligibility(event) {
    event.preventDefault();
    
    // Check KYC status (escaped for security)
    const kycStatus = <?php echo json_encode($kyc_status); ?>;
    if (kycStatus !== 'verified' && kycStatus !== 'approved') {
        toastr.warning('Please verify your KYC Identification before making withdrawals.', 'KYC Verification Required', {
            timeOut: 5000,
            closeButton: true,
            progressBar: true,
            onclick: function() {
                window.location.href = 'kyc.php';
            }
        });
        return;
    }
    
    // Check for verified payment method
    const hasVerifiedPayment = <?php echo json_encode($hasVerifiedPaymentMethod ?? false); ?>;
    if (!hasVerifiedPayment) {
        toastr.warning('Please add and verify at least one cryptocurrency wallet before making withdrawals.', 'Payment Method Verification Required', {
            timeOut: 5000,
            closeButton: true,
            progressBar: true,
            onclick: function() {
                window.location.href = 'payment-methods.php';
            }
        });
        return;
    }
    
    // All checks passed - open withdrawal modal
    $('#newWithdrawalModal').modal('show');
}
</script>
</body>
</html>