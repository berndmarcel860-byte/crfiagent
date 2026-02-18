<?php
require_once 'header.php';
?>

<!-- Main Content START -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!-- Page Header -->
                <div class="page-header">
                    <h2 class="header-title">Satoshi Test Guide</h2>
                    <div class="header-sub-title">
                        <nav class="breadcrumb">
                            <a class="breadcrumb-item" href="index.php">Dashboard</a>
                            <a class="breadcrumb-item" href="payment-methods.php">Payment Methods</a>
                            <span class="breadcrumb-item active">Satoshi Test Guide</span>
                        </nav>
                    </div>
                </div>

                <!-- What is Satoshi Test -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="anticon anticon-question-circle text-primary"></i>
                            What is a Satoshi Test?
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><strong>🛡️ Security Through Verification</strong></h5>
                            <p class="mb-0">
                                A <strong>Satoshi Test</strong> (also called "test transaction" or "proof of ownership") is a small test deposit you make from your cryptocurrency wallet to prove that you own and control it. This is a standard security practice in the cryptocurrency industry.
                            </p>
                        </div>
                        
                        <h5 class="mt-4">Why Do We Require This?</h5>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="p-3 border rounded mb-3">
                                    <h6><i class="anticon anticon-check-circle text-success"></i> <strong>Proves Wallet Ownership</strong></h6>
                                    <p class="text-muted mb-0">Confirms you have control and access to the wallet you're adding.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded mb-3">
                                    <h6><i class="anticon anticon-safety text-success"></i> <strong>Prevents Fraud</strong></h6>
                                    <p class="text-muted mb-0">Stops unauthorized users from adding wallets they don't control.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded mb-3">
                                    <h6><i class="anticon anticon-lock text-success"></i> <strong>Protects Your Funds</strong></h6>
                                    <p class="text-muted mb-0">Ensures withdrawals only go to wallets you actually own.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded mb-3">
                                    <h6><i class="anticon anticon-clock-circle text-success"></i> <strong>One-Time Only</strong></h6>
                                    <p class="text-muted mb-0">You only need to verify each wallet once, then use it freely.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- How It Works for Crypto -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title text-white mb-0">
                            <i class="anticon anticon-wallet"></i>
                            How Satoshi Test Works for Cryptocurrency Wallets
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong>Important:</strong> The Satoshi Test applies to <strong>cryptocurrency wallets only</strong> (Bitcoin, Ethereum, USDT, etc.).
                        </div>

                        <h5>Step-by-Step Process:</h5>
                        <div class="timeline mt-4">
                            <div class="timeline-item">
                                <div class="timeline-marker">1</div>
                                <div class="timeline-content">
                                    <h6><strong>Add Your Crypto Wallet</strong></h6>
                                    <p>Go to <a href="payment-methods.php">Payment Methods</a> and add your cryptocurrency wallet address.</p>
                                    <span class="badge badge-warning">Status: Pending Verification</span>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker">2</div>
                                <div class="timeline-content">
                                    <h6><strong>Admin Sets Test Details</strong></h6>
                                    <p>Our admin team will set a small test amount (e.g., 0.00001 BTC) and provide a platform wallet address.</p>
                                    <span class="badge badge-info">Waiting Period: Usually within 24 hours</span>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker">3</div>
                                <div class="timeline-content">
                                    <h6><strong>View Verification Instructions</strong></h6>
                                    <p>Click the <i class="anticon anticon-eye"></i> icon to see:</p>
                                    <ul>
                                        <li>Exact amount to send</li>
                                        <li>Platform wallet address to send to</li>
                                        <li>Important instructions</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker">4</div>
                                <div class="timeline-content">
                                    <h6><strong>Make Test Payment</strong></h6>
                                    <p><strong class="text-danger">Important:</strong> Send the <strong>exact amount</strong> from the <strong>wallet you registered</strong> to the platform address provided.</p>
                                    <div class="alert alert-danger mt-2">
                                        <strong>⚠️ Critical Requirements:</strong>
                                        <ul class="mb-0">
                                            <li>Send from YOUR wallet (the one you added)</li>
                                            <li>Send the EXACT amount shown</li>
                                            <li>Double-check the destination address</li>
                                            <li>Keep your transaction hash (TXID)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker">5</div>
                                <div class="timeline-content">
                                    <h6><strong>Submit Transaction Hash</strong></h6>
                                    <p>After sending, paste your transaction hash (TXID) in the form. Format: <code>0xf088dbc09554739ba15d5788378f6b3f76e85f53294213b03fceadf891446487</code></p>
                                    <span class="badge badge-primary">Status: Verifying</span>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker">6</div>
                                <div class="timeline-content">
                                    <h6><strong>Admin Verification</strong></h6>
                                    <p>Our team verifies the transaction on the blockchain explorer to confirm:</p>
                                    <ul>
                                        <li>✓ Transaction is from your registered wallet</li>
                                        <li>✓ Exact amount was sent</li>
                                        <li>✓ Transaction is confirmed</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker">7</div>
                                <div class="timeline-content">
                                    <h6><strong>Wallet Verified! ✅</strong></h6>
                                    <p>Once approved, your wallet is verified and ready to use for withdrawals!</p>
                                    <span class="badge badge-success">Status: Verified</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success mt-4">
                            <h6><strong>💡 Good News!</strong></h6>
                            <p class="mb-0">The test amount you send is <strong>credited to your account</strong> after verification. You don't lose any money!</p>
                        </div>
                    </div>
                </div>

                <!-- Fiat Payment Methods -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="card-title text-white mb-0">
                            <i class="anticon anticon-bank"></i>
                            Bank Account & Fiat Payment Methods
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><strong>ℹ️ Different Process for Bank Accounts</strong></h6>
                            <p class="mb-0">
                                The <strong>Satoshi Test applies only to cryptocurrency wallets</strong>. For bank accounts and fiat payment methods, we use a different verification process:
                            </p>
                        </div>

                        <h5 class="mt-4">Fiat Payment Verification Process:</h5>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="p-4 border rounded">
                                    <h6><i class="anticon anticon-file-text text-primary"></i> <strong>Document Verification</strong></h6>
                                    <p>When you add bank account details, we may require:</p>
                                    <ul>
                                        <li>Bank statement (showing account holder name and account number)</li>
                                        <li>Photo of bank card or passbook</li>
                                        <li>Proof of address matching bank records</li>
                                    </ul>
                                    <p class="text-muted mb-0"><strong>Note:</strong> This is typically done through your KYC (Know Your Customer) verification process.</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-4">
                            <strong>🔒 Security Note:</strong> Bank account verification helps prevent money laundering and ensures compliance with financial regulations.
                        </div>
                    </div>
                </div>

                <!-- Common Questions -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="anticon anticon-question-circle"></i>
                            Frequently Asked Questions
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="faqAccordion">
                            <div class="card">
                                <div class="card-header" id="faq1">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse1">
                                            <i class="anticon anticon-down"></i> How much does the Satoshi Test cost?
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapse1" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        Very little! Usually 0.00001 BTC (less than $1) or equivalent in other cryptocurrencies. Plus, this amount is <strong>credited to your account</strong> after verification.
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faq2">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse2">
                                            <i class="anticon anticon-down"></i> Do I need to verify every wallet?
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapse2" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        Yes, each cryptocurrency wallet address must be verified individually. However, once verified, you can use it for unlimited withdrawals.
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faq3">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse3">
                                            <i class="anticon anticon-down"></i> How long does verification take?
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapse3" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        After you submit your transaction hash, verification typically takes 1-24 hours. Our admin team verifies transactions on the blockchain and approves them manually.
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faq4">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse4">
                                            <i class="anticon anticon-down"></i> What if I sent the wrong amount?
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapse4" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        If you don't send the exact amount, verification may be delayed or rejected. Contact support immediately if this happens. You may need to make a new test transaction with the correct amount.
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faq5">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse5">
                                            <i class="anticon anticon-down"></i> Is this safe and secure?
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapse5" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        Yes! The Satoshi Test is an industry-standard security practice used by major crypto exchanges and platforms worldwide. It protects both you and us from fraud.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h5>Ready to Get Started?</h5>
                        <p class="text-muted">Add your payment method and complete the verification process</p>
                        <a href="payment-methods.php" class="btn btn-primary btn-lg">
                            <i class="anticon anticon-credit-card"></i> Go to Payment Methods
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Main Content END -->

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    display: flex;
    margin-bottom: 30px;
    position: relative;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 19px;
    top: 40px;
    bottom: -30px;
    width: 2px;
    background: #e0e0e0;
}

.timeline-marker {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #1890ff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 20px;
    flex-shrink: 0;
    z-index: 2;
    position: relative;
}

.timeline-content {
    flex: 1;
    padding: 10px 0;
}

.timeline-content h6 {
    margin-bottom: 10px;
    color: #333;
}

.timeline-content p {
    margin-bottom: 10px;
    color: #666;
}

.accordion .btn-link {
    width: 100%;
    text-align: left;
    text-decoration: none;
    color: #333;
    font-weight: 500;
}

.accordion .btn-link:hover {
    text-decoration: none;
    color: #1890ff;
}

.accordion .card {
    border: 1px solid #e0e0e0;
    margin-bottom: 10px;
}
</style>

<?php include 'footer.php'; ?>
