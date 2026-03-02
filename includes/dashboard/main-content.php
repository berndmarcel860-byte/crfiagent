        <!-- AI INSIGHT CARD -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm border-0 h-100" aria-labelledby="aiInsightsHeading">
                    <div class="card-body">
                        <h5 id="aiInsightsHeading" class="mb-3" style="color: #2c3e50; font-weight: 600;">
                            <i class="anticon anticon-robot text-primary mr-2"></i> AI Insights
                        </h5>
                        <ul class="list-unstyled mb-0" style="line-height: 2;">
                            <li class="d-flex align-items-start mb-2">
                                <i class="anticon anticon-check-circle text-success mr-2 mt-1"></i>
                                <span style="font-size: 14px;">Continuous monitoring for suspicious activity</span>
                            </li>
                            <li class="d-flex align-items-start mb-2">
                                <i class="anticon anticon-clock-circle text-info mr-2 mt-1"></i>
                                <span style="font-size: 14px;">Next scan: <strong id="nextScan"><?= date('M d, H:i', strtotime('+1 hour')) ?></strong></span>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="anticon anticon-<?= $passwordChangeRequired ? 'exclamation-circle text-warning' : 'shield text-success' ?> mr-2 mt-1"></i>
                                <?php if ($passwordChangeRequired): ?>
                                    <span class="text-danger" style="font-size: 14px; font-weight: 500;">Action required: Change password</span>
                                <?php else: ?>
                                    <span class="text-success" style="font-size: 14px;">Security status: Excellent</span>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- KYC/AML -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm border-0 h-100" aria-labelledby="complianceHeading">
                    <div class="card-body">
                        <h5 id="complianceHeading" class="mb-3" style="color: #2c3e50; font-weight: 600;">
                            <i class="anticon anticon-safety-certificate text-success mr-2"></i> Compliance
                        </h5>
                        <div class="mb-3">
                            <label class="text-muted mb-1" style="font-size: 13px;">KYC Verification Status</label>
                            <div>
                            <?php
                            $kycStatus = $kyc_status;
                            $kycBadge = "secondary";
                            $kycIcon = "question-circle";
                            if ($kycStatus === 'approved') {
                                $kycBadge = "success";
                                $kycStatus = "verified";
                                $kycIcon = "check-circle";
                            } elseif ($kycStatus === 'rejected') {
                                $kycBadge = "danger";
                                $kycIcon = "close-circle";
                            } elseif ($kycStatus === 'pending') {
                                $kycBadge = "warning";
                                $kycIcon = "clock-circle";
                            }
                            ?>
                                <span class="badge badge-<?= htmlspecialchars($kycBadge, ENT_QUOTES) ?> px-3 py-2">
                                    <i class="anticon anticon-<?= htmlspecialchars($kycIcon, ENT_QUOTES) ?> mr-1"></i>
                                    <?= htmlspecialchars(ucfirst($kycStatus), ENT_QUOTES) ?>
                                </span>
                            </div>
                        </div>
                        <p class="text-muted mb-3" style="font-size: 13px; line-height: 1.6;">
                            KYC verification is required for withdrawals and advanced recovery tools to ensure secure operations.
                        </p>
                        <?php if ($kycStatus == "pending"): ?>
                            <a href="kyc.php" class="btn btn-primary btn-sm btn-block" role="button">
                                <i class="anticon anticon-safety-certificate mr-1"></i> Complete Verification
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Security -->
            <div class="col-md-12 col-lg-4 mb-3">
                <div class="card shadow-sm border-0 h-100" aria-labelledby="securityHeading">
                    <div class="card-body">
                        <h5 id="securityHeading" class="mb-3" style="color: #2c3e50; font-weight: 600;">
                            <i class="anticon anticon-lock text-primary mr-2"></i> Security
                        </h5>
                        <div class="mb-3">
                            <label class="text-muted mb-1" style="font-size: 13px;">Last Login</label>
                            <p class="mb-0 font-weight-500" style="font-size: 14px;">
                                <i class="anticon anticon-calendar mr-1"></i>
                                <?= htmlspecialchars($currentUser['last_login'] ?? $currentDateTimeFormatted, ENT_QUOTES) ?>
                            </p>
                        </div>

                        <div class="alert alert-warning mb-0 py-2 px-3" style="font-size: 13px; border-radius: 8px;">
                            <i class="anticon anticon-info-circle mr-1"></i>
                            Suspicious activity? <a href="support.php" class="alert-link font-weight-600">Contact support</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center py-3">
                        <div class="mb-2 mb-md-0">
                            <h5 class="card-title mb-1" style="color: #2c3e50; font-weight: 600;">
                                <i class="anticon anticon-thunderbolt text-warning mr-2"></i>Quick Actions
                            </h5>
                            <p class="card-text small text-muted mb-0" style="font-size: 13px;">Perform common transactions quickly and securely</p>
                        </div>
                        <div class="d-flex flex-wrap" role="group" aria-label="Quick actions">
                            <button class="btn btn-primary mr-2 mb-2" data-toggle="modal" data-target="#newDepositModal" title="Add funds to your account">
                                <i class="anticon anticon-plus-circle mr-1"></i> New Deposit
                            </button>
                            <button class="btn btn-success mr-2 mb-2" onclick="checkWithdrawalEligibility(event)" title="Request withdrawal">
                                <i class="anticon anticon-download mr-1"></i> New Withdrawal
                            </button>
                            <a href="transactions.php" class="btn btn-info mb-2" title="View all transactions">
                                <i class="anticon anticon-history mr-1"></i> Transactions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Row -->
        <div class="row mb-3">
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-icon avatar-lg avatar-blue mr-3" aria-hidden="true">
                                <i class="anticon anticon-file-text"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2 class="mb-1 font-weight-bold count" data-value="<?= htmlspecialchars($stats['total_cases'], ENT_QUOTES) ?>" style="color: #2c3e50;">
                                    <?= htmlspecialchars($stats['total_cases'], ENT_QUOTES) ?>
                                </h2>
                                <p class="mb-1 text-muted font-weight-500" style="font-size: 14px;">Total Cases</p>
                                <?php if ($stats['last_case_date']): ?>
                                <small class="text-muted" style="font-size: 12px;">
                                    <i class="anticon anticon-calendar mr-1"></i><?= htmlspecialchars(date('M d, Y', strtotime($stats['last_case_date'])), ENT_QUOTES) ?>
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-icon avatar-lg avatar-cyan mr-3" aria-hidden="true">
                                <i class="anticon anticon-line-chart"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2 class="mb-1 font-weight-bold count percent" data-value="<?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>" style="color: #2c3e50;">
                                    <?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>%
                                </h2>
                                <p class="mb-1 text-muted font-weight-500" style="font-size: 14px;">Recovery Rate</p>
                                <small class="badge badge-<?= $recoveryPercentage >= 50 ? 'success' : 'warning' ?>" style="font-size: 11px;">
                                    <i class="anticon anticon-<?= $recoveryPercentage >= 50 ? 'arrow-up' : 'arrow-down' ?> mr-1"></i>
                                    <?= $recoveryPercentage >= 50 ? 'Above average' : 'Below average' ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-icon avatar-lg avatar-gold mr-3" aria-hidden="true">
                                <i class="anticon anticon-exclamation-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2 class="mb-1 font-weight-bold count money" data-value="<?= htmlspecialchars($stats['total_reported'], ENT_QUOTES) ?>" style="color: #2c3e50;">
                                    $<?= number_format($stats['total_reported'], 2) ?>
                                </h2>
                                <p class="mb-1 text-muted font-weight-500" style="font-size: 14px;">Reported Loss</p>
                                <?php if ($outstandingAmount > 0): ?>
                                <small class="badge badge-danger" style="font-size: 11px;">
                                    <i class="anticon anticon-warning mr-1"></i>$<?= number_format($outstandingAmount, 2) ?> outstanding
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-icon avatar-lg avatar-purple mr-3" aria-hidden="true">
                                <i class="anticon anticon-check-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2 class="mb-1 font-weight-bold count money" data-value="<?= htmlspecialchars($stats['total_recovered'], ENT_QUOTES) ?>" style="color: #2c3e50;">
                                    $<?= number_format($stats['total_recovered'], 2) ?>
                                </h2>
                                <p class="mb-1 text-muted font-weight-500" style="font-size: 14px;">Amount Recovered</p>
                                <?php if ($stats['total_recovered'] > 0): ?>
                                <small class="badge badge-success pulse" style="font-size: 11px;">
                                    <i class="anticon anticon-rise mr-1"></i><?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>% recovered
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recovery / Workflow -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <h5 class="mb-2 mb-md-0" style="color: #2c3e50; font-weight: 600;">
                                <i class="anticon anticon-sync mr-2" style="color: var(--brand);"></i>Recovery Status
                            </h5>
                            <div>
                                <span class="badge badge-pill px-3 py-2 badge-<?= $recoveryPercentage > 70 ? 'success' : ($recoveryPercentage > 30 ? 'warning' : 'danger') ?>" style="font-size: 13px;">
                                    <i class="anticon anticon-<?= $recoveryPercentage > 70 ? 'check-circle' : ($recoveryPercentage > 30 ? 'clock-circle' : 'exclamation-circle') ?> mr-1"></i>
                                    <?= $recoveryPercentage > 70 ? 'Excellent Progress' : ($recoveryPercentage > 30 ? 'Good Progress' : 'Needs Attention') ?>
                                </span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="algorithm-animation">
                                <div class="algorithm-steps" aria-hidden="true">
                                    <div class="step <?= $recoveryPercentage > 0 ? 'active' : '' ?>">
                                        <div class="step-icon">
                                            <i class="anticon anticon-search"></i>
                                        </div>
                                        <div class="step-label">Trace Funds</div>
                                    </div>
                                    <div class="step <?= $recoveryPercentage > 20 ? 'active' : '' ?>">
                                        <div class="step-icon">
                                            <i class="anticon anticon-lock"></i>
                                        </div>
                                        <div class="step-label">Freeze Assets</div>
                                    </div>
                                    <div class="step <?= $recoveryPercentage > 40 ? 'active' : '' ?>">
                                        <div class="step-icon">
                                            <i class="anticon anticon-solution"></i>
                                        </div>
                                        <div class="step-label">Legal Process</div>
                                    </div>
                                    <div class="step <?= $recoveryPercentage > 60 ? 'active' : '' ?>">
                                        <div class="step-icon">
                                            <i class="anticon anticon-sync"></i>
                                        </div>
                                        <div class="step-label">Recovery</div>
                                    </div>
                                    <div class="step <?= $recoveryPercentage > 80 ? 'active' : '' ?>">
                                        <div class="step-icon">
                                            <i class="anticon anticon-check-circle"></i>
                                        </div>
                                        <div class="step-label">Complete</div>
                                    </div>
                                </div>
                                <div class="algorithm-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>">
                                    <div class="progress-bar" style="width: <?= $recoveryPercentage ?>%"></div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="d-flex flex-wrap justify-content-between align-items-center">
                                    <div class="text-left">
                                        <p class="m-b-5"><strong>Total Cases:</strong> <?= htmlspecialchars($stats['total_cases'], ENT_QUOTES) ?></p>
                                        <p class="m-b-5"><strong>Active Cases:</strong> <?= array_sum($statusCounts) ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="m-b-5"><strong>Recovered:</strong> $<?= number_format($stats['total_recovered'], 2) ?></p>
                                        <p class="m-b-5"><strong>Outstanding:</strong> $<?= number_format($outstandingAmount, 2) ?></p>
                                    </div>
                                </div>
                                <button class="btn btn-outline-primary btn-sm" id="refresh-algorithm" aria-live="polite">
                                    <i class="anticon anticon-sync"></i> Refresh Status
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Cases + Side column -->
        <div class="row mt-3">
            <div class="col-md-12 col-lg-8">
                <!-- Recent Cases -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <h5 class="mb-2 mb-md-0" style="color: #2c3e50; font-weight: 600;">
                                <i class="anticon anticon-folder-open mr-2" style="color: var(--brand);"></i>Recent Cases
                            </h5>
                            <div class="d-flex">
                                <a href="cases.php" class="btn btn-sm btn-outline-primary mr-2">
                                    <i class="anticon anticon-eye mr-1"></i>View All
                                </a>
                                <a href="new-case.php" class="btn btn-sm btn-primary">
                                    <i class="anticon anticon-plus-circle mr-1"></i>New Case
                                </a>
                            </div>
                        </div>
                        
                        <?php if (empty($cases)): ?>
                            <div class="alert alert-info mt-3 d-flex align-items-center" style="border-radius: 10px;">
                                <i class="anticon anticon-info-circle mr-2" style="font-size: 20px;"></i>
                                <div>No cases found. <a href="new-case.php" class="alert-link font-weight-600">File your first case</a></div>
                            </div>
                        <?php else: ?>
                            <div class="mt-3">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Case #</th>
                                                <th>Platform</th>
                                                <th>Reported</th>
                                                <th>Recovered</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($cases as $case): 
                                                $reported = (float)($case['reported_amount'] ?? 0);
                                                $recovered = (float)($case['recovered_amount'] ?? 0);
                                                $status = $case['status'] ?? 'open';
                                                
                                                $progress = ($reported > 0) ? round(($recovered / $reported) * 100, 2) : 0;
                                                
                                                $statusClass = [
                                                    'open' => 'warning',
                                                    'documents_required' => 'secondary',
                                                    'under_review' => 'info',
                                                    'refund_approved' => 'success',
                                                    'refund_rejected' => 'danger',
                                                    'closed' => 'dark'
                                                ][$status] ?? 'light';
                                            ?>
                                            <tr>
                                                <td>
                                                    <a href="case-details.php?id=<?= htmlspecialchars($case['id'], ENT_QUOTES) ?>">
                                                        <?= htmlspecialchars($case['case_number'], ENT_QUOTES) ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="media align-items-center">
                                                        <?php if (!empty($case['platform_logo'])): ?>
                                                        <div class="avatar avatar-image" style="width: 34px; height: 34px">
                                                            <img src="<?= htmlspecialchars($case['platform_logo'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($case['platform_name'], ENT_QUOTES) ?>">
                                                        </div>
                                                        <?php endif; ?>
                                                        <div class="m-l-10">
                                                            <?= htmlspecialchars($case['platform_name'], ENT_QUOTES) ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>$<?= number_format($reported, 2) ?></td>
                                                <td style="min-width:180px">
                                                    <div>
                                                        <strong style="font-size:14px;color:#2c3e50;">$<?= number_format($recovered, 2) ?></strong>
                                                    </div>
                                                    <div class="mt-1">
                                                        <div class="progress" style="height:6px;border-radius:3px;">
                                                            <div class="progress-bar" 
                                                                 style="width:<?= htmlspecialchars($progress, ENT_QUOTES) ?>%;background:linear-gradient(90deg,#2950a8,#2da9e3);"
                                                                 role="progressbar" 
                                                                 aria-valuenow="<?= htmlspecialchars($progress, ENT_QUOTES) ?>" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-1">
                                                        <small class="text-muted" style="font-size:11px;"><?= htmlspecialchars($progress, ENT_QUOTES) ?>% of $<?= number_format($reported, 2) ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-pill badge-<?= htmlspecialchars($statusClass, ENT_QUOTES) ?>">
                                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status)), ENT_QUOTES) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary view-case-btn" 
                                                            data-case-id="<?= htmlspecialchars($case['id'], ENT_QUOTES) ?>" 
                                                            title="View case details">
                                                        <i class="anticon anticon-eye"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Active Recovery Operations -->
                <div class="card mt-3 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <h5 class="mb-2 mb-md-0" style="color: #2c3e50; font-weight: 600;">
                                <i class="anticon anticon-sync mr-2" style="color: var(--brand);"></i>Active Recovery Operations
                            </h5>
                            <span class="badge badge-info px-3 py-2" style="font-size: 13px;">
                                <i class="anticon anticon-file-text mr-1"></i><?= count($ongoingRecoveries) ?> active cases
                            </span>
                        </div>
                        <div class="mt-3">
                            <?php if (empty($ongoingRecoveries)): ?>
                                <div class="alert alert-info d-flex align-items-center" style="border-radius: 10px;">
                                    <i class="anticon anticon-info-circle mr-2" style="font-size: 20px;"></i>
                                    <span>No active recovery operations</span>
                                </div>
                            <?php else: ?>
                                <?php foreach ($ongoingRecoveries as $recovery): 
                                    $reported = (float)($recovery['reported_amount'] ?? 0);
                                    $recovered = (float)($recovery['recovered_amount'] ?? 0);
                                    $status = $recovery['status'] ?? 'open';
                                    
                                    $progress = ($reported > 0) ? round(($recovered / $reported) * 100, 2) : 0;
                                    
                                    $statusClass = 'info';
                                    $statusText = 'In progress';
                                    
                                    if ($status === 'documents_required') {
                                        $statusClass = 'danger';
                                        $statusText = 'Needs attention';
                                    } elseif ($progress > 70) {
                                        $statusClass = 'success';
                                        $statusText = 'On track';
                                    } elseif ($progress > 30) {
                                        $statusClass = 'warning';
                                        $statusText = 'In progress';
                                    }
                                ?>
                                <div class="m-b-25">
                                    <div class="d-flex justify-content-between m-b-5">
                                        <div>
                                            <button class="btn btn-link p-0 view-case-btn" 
                                                    data-case-id="<?= htmlspecialchars($recovery['id'], ENT_QUOTES) ?>" 
                                                    style="color: var(--brand); text-decoration: none; font-weight: 600;">
                                                <?= htmlspecialchars($recovery['case_number'], ENT_QUOTES) ?>
                                            </button>
                                        </div>
                                        <div class="text-right">
                                            <span><?= htmlspecialchars($progress, ENT_QUOTES) ?>%</span>
                                            <div class="text-<?= htmlspecialchars($statusClass, ENT_QUOTES) ?>">
                                                <?= htmlspecialchars($statusText, ENT_QUOTES) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-<?= htmlspecialchars($statusClass, ENT_QUOTES) ?>" 
                                             style="width: <?= htmlspecialchars($progress, ENT_QUOTES) ?>%" 
                                             aria-valuenow="<?= htmlspecialchars($progress, ENT_QUOTES) ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between m-t-10">
                                        <small class="text-muted">
                                            Reported: $<?= number_format($reported, 2) ?>
                                        </small>
                                        <small class="text-muted">
                                            Recovered: $<?= number_format($recovered, 2) ?>
                                        </small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                
                                <div class="text-center m-t-20">
                                    <a href="cases.php" class="btn btn-sm btn-outline-primary">
                                        <i class="anticon anticon-eye"></i> View All Cases
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right column -->
            <div class="col-md-12 col-lg-4">

                <div class="card shadow-sm border-0 mt-3">
                    <div class="card-body">
                        <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                            <i class="anticon anticon-transaction mr-2" style="color: var(--brand);"></i>Recent Transactions
                        </h5>
                        <div style="min-height: 300px">
                            <?php if (empty($transactions)): ?>
                                <div class="alert alert-info d-flex align-items-center mt-3" style="border-radius: 10px;">
                                    <i class="anticon anticon-info-circle mr-2" style="font-size: 20px;"></i>
                                    <span>No transactions yet</span>
                                </div>
                            <?php else: ?>
                                <div class="scrollable" style="height: 280px">
                                    <?php foreach ($transactions as $transaction): ?>
                                    <div class="m-b-20">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <?php
                                                $iconConfig = [
                                                    'refund' => ['icon' => 'arrow-up', 'color' => 'success'],
                                                    'deposit' => ['icon' => 'arrow-down', 'color' => 'primary'],
                                                    'withdrawal' => ['icon' => 'arrow-up', 'color' => 'danger'],
                                                    'fee' => ['icon' => 'minus', 'color' => 'warning']
                                                ];
                                                $config = $iconConfig[$transaction['type']] ?? ['icon' => 'swap', 'color' => 'info'];
                                                ?>
                                                <div class="avatar avatar-icon avatar-<?= htmlspecialchars($config['color'], ENT_QUOTES) ?>" aria-hidden="true">
                                                    <i class="anticon anticon-<?= htmlspecialchars($config['icon'], ENT_QUOTES) ?>"></i>
                                                </div>
                                                <div class="m-l-15">
                                                    <h6 class="m-b-0"><?= ucfirst(htmlspecialchars($transaction['type'], ENT_QUOTES)) ?></h6>
                                                    <p class="m-b-0 text-muted">
                                                        <?= htmlspecialchars($transaction['reference_name'], ENT_QUOTES) ?>
                                                        <br>
                                                        <small><?= date('M d, Y', strtotime($transaction['created_at'])) ?></small>
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="text-<?= in_array($transaction['type'], ['refund', 'deposit']) ? 'success' : 'danger' ?> font-weight-semibold">
                                                $<?= number_format($transaction['amount'], 2) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <h5 class="m-b-20">Case Status Summary</h5>
                        <div class="m-t-20">
                            <?php if (empty($statusCounts)): ?>
                                <div class="alert alert-info">No cases found</div>
                            <?php else: ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <canvas id="statusChart" height="200" aria-label="Case status chart"></canvas>
                                    </div>
                                </div>
                                <div class="m-t-20">
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($statusCounts as $status => $count): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center p-l-0 p-r-0">
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status)), ENT_QUOTES) ?>
                                            <span class="badge badge-pill badge-<?= htmlspecialchars([
                                                'open' => 'warning',
                                                'documents_required' => 'secondary',
                                                'under_review' => 'info',
                                                'refund_approved' => 'success',
                                                'refund_rejected' => 'danger',
                                                'closed' => 'dark'
                                            ][$status] ?? 'light', ENT_QUOTES) ?>"><?= htmlspecialchars($count, ENT_QUOTES) ?></span>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Recovery Progress -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 style="color: #2c3e50; font-weight: 600;">
                                <i class="anticon anticon-line-chart mr-2" style="color: var(--brand);"></i>Recovery Progress
                            </h5>
                            <div>
                                <span class="badge badge-<?= $recoveryPercentage >= 50 ? 'success' : 'warning' ?> px-3 py-2">
                                    <i class="anticon anticon-<?= $recoveryPercentage >= 50 ? 'check-circle' : 'clock-circle' ?> mr-1"></i>
                                    <?= $recoveryPercentage >= 50 ? 'Good progress' : 'Needs attention' ?>
                                </span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="font-weight-semibold">
                                    Overall Recovery: <span class="count" data-value="<?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>"><?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>%</span>
                                    (<?= htmlspecialchars($stats['total_cases'], ENT_QUOTES) ?> cases)
                                </span>
                                <span>
                                    $<?= number_format($stats['total_recovered'], 2) ?> of $<?= number_format($stats['total_reported'], 2) ?>
                                </span>
                            </div>
                            <div class="progress" style="height: 12px; border-radius: 10px;" aria-hidden="false" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= htmlspecialchars($recoveryPercentage, ENT_QUOTES) ?>">
                                <div class="progress-bar bg-success" style="width: <?= $recoveryPercentage ?>%; background: linear-gradient(90deg, #28a745, #20c997);"></div>
                            </div>
                            <div class="mt-2 d-flex justify-content-between">
                                <small class="text-muted">0%</small>
                                <small class="text-muted">100%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Professional Case Details Modal -->
<div class="modal fade" id="caseDetailsModal" tabindex="-1" role="dialog" aria-labelledby="caseDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #2950a8 0%, #2da9e3 100%); color: #fff; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold" id="caseDetailsModalLabel">
                    <i class="anticon anticon-file-text mr-2"></i>Case Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="caseModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading case details...</p>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light" style="border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="anticon anticon-close mr-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<?php
// include footer safely
if (file_exists(__DIR__ . '/footer.php')) {
    include __DIR__ . '/footer.php';
} else {
    echo "<!-- footer.php missing; page ended -->\n";
}
?>

