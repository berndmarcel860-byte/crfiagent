<?php
require_once 'admin_header.php';

// Verify admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get admin data
$stmt = $pdo->prepare("SELECT first_name, last_name, role FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

// Get stats for dashboard
$stats = [
    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'active_cases' => $pdo->query("SELECT COUNT(*) FROM cases WHERE status NOT IN ('closed', 'refund_rejected')")->fetchColumn(),
    'pending_withdrawals' => $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status = 'pending'")->fetchColumn(),
    'total_recovered' => $pdo->query("SELECT SUM(recovered_amount) FROM cases")->fetchColumn()
];

// Get recent activities from audit_logs
$activities = $pdo->query("
    SELECT 
        al.id,
        al.action,
        al.entity_type,
        al.entity_id,
        al.old_value,
        al.new_value,
        al.created_at,
        al.ip_address,
        CONCAT(a.first_name, ' ', a.last_name) as admin_name
    FROM audit_logs al
    LEFT JOIN admins a ON al.admin_id = a.id
    ORDER BY al.created_at DESC
    LIMIT 5
")->fetchAll();

// Get recent cases
$recentCases = $pdo->query("
    SELECT c.*, u.first_name, u.last_name, p.name as platform_name
    FROM cases c
    JOIN users u ON c.user_id = u.id
    JOIN scam_platforms p ON c.platform_id = p.id
    ORDER BY c.created_at DESC
    LIMIT 5
")->fetchAll();
?>

                <!-- Content Wrapper START -->
                <div class="main-content">
                    <!-- Welcome Message -->
                    <div class="page-header">
                        <h2 class="header-title">Welcome back, <?= htmlspecialchars($admin['first_name']) ?></h2>
                        <p class="header-sub-title">Here's what's happening with your platform today</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row">
                        <div class="col-md-6 col-lg-3">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-icon avatar-lg avatar-blue">
                                            <i class="anticon anticon-user card-icon"></i>
                                        </div>
                                        <div class="m-l-15">
                                            <h2 class="m-b-0"><?= number_format($stats['total_users']) ?></h2>
                                            <p class="m-b-0 text-muted">Total Users</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-icon avatar-lg avatar-cyan">
                                            <i class="anticon anticon-file card-icon"></i>
                                        </div>
                                        <div class="m-l-15">
                                            <h2 class="m-b-0"><?= number_format($stats['active_cases']) ?></h2>
                                            <p class="m-b-0 text-muted">Active Cases</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-icon avatar-lg avatar-gold">
                                            <i class="anticon anticon-transaction card-icon"></i>
                                        </div>
                                        <div class="m-l-15">
                                            <h2 class="m-b-0"><?= number_format($stats['pending_withdrawals']) ?></h2>
                                            <p class="m-b-0 text-muted">Pending Withdrawals</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-icon avatar-lg avatar-purple">
                                            <i class="anticon anticon-dollar card-icon"></i>
                                        </div>
                                        <div class="m-l-15">
                                            <h2 class="m-b-0">$<?= number_format($stats['total_recovered'], 2) ?></h2>
                                            <p class="m-b-0 text-muted">Total Recovered</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Row -->
                    <div class="row">
                        <!-- Recent Activities and Cases -->
                        <div class="col-md-12 col-lg-8">
                            <!-- Recent Cases -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5>Recent Cases</h5>
                                        <a href="admin_cases.php" class="btn btn-sm btn-default">View All</a>
                                    </div>
                                    <div class="m-t-25">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Case #</th>
                                                        <th>User</th>
                                                        <th>Platform</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recentCases as $case): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($case['case_number']) ?></td>
                                                        <td><?= htmlspecialchars($case['first_name'] . ' ' . $case['last_name']) ?></td>
                                                        <td><?= htmlspecialchars($case['platform_name']) ?></td>
                                                        <td>$<?= number_format($case['reported_amount'], 2) ?></td>
                                                        <td>
                                                            <?php
                                                            $statusClass = [
                                                                'open' => 'warning',
                                                                'documents_required' => 'info',
                                                                'under_review' => 'primary',
                                                                'refund_approved' => 'success',
                                                                'refund_rejected' => 'danger',
                                                                'closed' => 'secondary'
                                                            ][$case['status']] ?? 'light';
                                                            ?>
                                                            <span class="badge case-status-badge badge-<?= $statusClass ?>">
                                                                <?= ucwords(str_replace('_', ' ', $case['status'])) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Activities -->
                            <div class="card m-t-20">
                                <div class="card-body">
                                    <h5>Recent Activities</h5>
                                    <div class="m-t-20">
                                        <?php foreach ($activities as $activity): ?>
                                        <div class="activity-item">
                                            <div class="d-flex justify-content-between">
                                                <p class="m-b-5">
                                                    <?php if ($activity['admin_name']): ?>
                                                    <strong><?= htmlspecialchars($activity['admin_name']) ?></strong>
                                                    <?php else: ?>
                                                    <strong>System</strong>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($activity['action']) ?>
                                                    <?php if ($activity['entity_type']): ?>
                                                    on <?= htmlspecialchars($activity['entity_type']) ?>
                                                    <?php endif; ?>
                                                    <?php if ($activity['entity_id']): ?>
                                                    #<?= htmlspecialchars($activity['entity_id']) ?>
                                                    <?php endif; ?>
                                                </p>
                                                <span class="activity-time">
                                                    <?= date('M j, H:i', strtotime($activity['created_at'])) ?>
                                                </span>
                                            </div>
                                            <?php if ($activity['old_value'] || $activity['new_value']): ?>
                                            <p class="text-muted m-b-0">
                                                <?php if ($activity['old_value']): ?>
                                                <span class="old-value"><?= htmlspecialchars($activity['old_value']) ?></span>
                                                <?php endif; ?>
                                                <?php if ($activity['old_value'] && $activity['new_value']): ?>
                                                <i class="anticon anticon-arrow-right"></i>
                                                <?php endif; ?>
                                                <?php if ($activity['new_value']): ?>
                                                <span class="new-value"><?= htmlspecialchars($activity['new_value']) ?></span>
                                                <?php endif; ?>
                                            </p>
                                            <?php endif; ?>
                                            <?php if ($activity['ip_address']): ?>
                                            <small class="ip-address">IP: <?= htmlspecialchars($activity['ip_address']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recovery Stats and Quick Actions -->
                        <div class="col-md-12 col-lg-4">
                            <!-- Recovery Progress -->
                            <div class="card">
                                <div class="card-body">
                                    <h5>Recovery Progress</h5>
                                    <div class="m-t-20">
                                        <canvas id="recoveryChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="card m-t-20">
                                <div class="card-body">
                                    <h5>Quick Actions</h5>
                                    <div class="m-t-20">
                                        <a href="admin_new_case.php" class="btn btn-block btn-primary m-b-10">
                                            <i class="anticon anticon-plus"></i> Create New Case
                                        </a>
                                        <a href="admin_users.php" class="btn btn-block btn-default m-b-10">
                                            <i class="anticon anticon-user"></i> Manage Users
                                        </a>
                                        <a href="admin_withdrawals.php" class="btn btn-block btn-default m-b-10">
                                            <i class="anticon anticon-transaction"></i> Process Withdrawals
                                        </a>
                                        <a href="admin_settings.php" class="btn btn-block btn-default">
                                            <i class="anticon anticon-setting"></i> System Settings
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Content Wrapper END -->

                <?php require_once 'admin_footer.php'; ?>
            </div>
            <!-- Page Container END -->

    
    <!-- Page JS -->
    <script>
    $(document).ready(function() {
        // Initialize recovery chart
        const ctx = document.getElementById('recoveryChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Recovered', 'Pending', 'Lost'],
                datasets: [{
                    data: [<?= $stats['total_recovered'] ?>, 50000, 25000], // Example data
                    backgroundColor: [
                        '#28a745',
                        '#ffc107',
                        '#dc3545'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                },
                cutoutPercentage: 70
            }
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
    </script>
</body>
</html>