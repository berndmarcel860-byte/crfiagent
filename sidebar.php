<!-- Side Nav START -->
<div class="side-nav">
    <div class="side-nav-inner">
        <div class="side-nav-scroll-container">
            <ul class="side-nav-menu">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="index.php">
                        <span class="icon-holder">
                            <i class="anticon anticon-dashboard"></i>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <!-- My Cases -->
                <li class="nav-item">
                    <a href="cases.php">
                        <span class="icon-holder">
                            <i class="anticon anticon-file"></i>
                        </span>
                        <span class="title">My Cases</span>
                    </a>
                </li>

                <!-- Transactions -->
                <li class="nav-item">
                    <a href="transactions.php">
                        <span class="icon-holder">
                            <i class="anticon anticon-transaction"></i>
                        </span>
                        <span class="title">Transactions</span>
                    </a>
                </li>

                <!-- KYC Verification -->
                <li class="nav-item">
                    <a href="kyc.php">
                        <span class="icon-holder">
                            <i class="anticon anticon-safety"></i>
                        </span>
                        <span class="title">KYC Verification</span>
                        <?php 
                        $stmt = $pdo->prepare("SELECT status FROM kyc_verification_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
                        $stmt->execute([$_SESSION['user_id']]);
                        $kycStatus = $stmt->fetch();
                        if ($kycStatus && $kycStatus['status'] === 'pending'): ?>
                            <span class="badge badge-warning float-right">Pending</span>
                        <?php endif; ?>
                    </a>
                </li>

                <!-- Account -->
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle" href="javascript:void(0);">
                        <span class="icon-holder">
                            <i class="anticon anticon-user"></i>
                        </span>
                        <span class="title">Account</span>
                        <span class="arrow">
                            <i class="arrow-icon"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="profile.php">
                                <i class="anticon anticon-profile m-r-10"></i>
                                My Profile
                            </a>
                        </li>
                        <li>
                            <a href="settings.php">
                                <i class="anticon anticon-setting m-r-10"></i>
                                Settings
                            </a>
                        </li>
                        <li>
                            <a href="logout.php">
                                <i class="anticon anticon-logout m-r-10"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Support -->
                <li class="nav-item">
                    <a href="support.php">
                        <span class="icon-holder">
                            <i class="anticon anticon-customer-service"></i>
                        </span>
                        <span class="title">Support</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
<!-- Side Nav END -->