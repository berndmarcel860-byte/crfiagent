<?php include 'header.php'; ?>

<!-- Content Wrapper START -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Transaction History</h4>
                        <div class="float-right">
                            <button class="btn btn-primary" id="refreshTransactions">
                                <i class="anticon anticon-reload"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger d-none" id="transactionError"></div>
                        <div class="table-responsive">
                            <table id="transactionsTable" class="table table-bordered nowrap" style="width:100%">
                                <!-- Update the table headers to match what the DataTable expects -->
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Reference</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Content Wrapper END -->

<!-- Withdrawal Details Modal -->
<div class="modal fade" id="withdrawalDetailsModal" tabindex="-1" role="dialog" aria-labelledby="withdrawalDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="withdrawalDetailsModalLabel">
                    <i class="anticon anticon-info-circle"></i> Withdrawal Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-group">
                            <label class="detail-label">Reference Number:</label>
                            <div class="detail-value" id="detail-reference"></div>
                        </div>
                        <div class="detail-group">
                            <label class="detail-label">Amount:</label>
                            <div class="detail-value" id="detail-amount"></div>
                        </div>
                        <div class="detail-group">
                            <label class="detail-label">Status:</label>
                            <div class="detail-value" id="detail-status"></div>
                        </div>
                        <div class="detail-group">
                            <label class="detail-label">Payment Method:</label>
                            <div class="detail-value" id="detail-method"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-group">
                            <label class="detail-label">Request Date:</label>
                            <div class="detail-value" id="detail-created"></div>
                        </div>
                        <div class="detail-group" id="approved-date-group" style="display:none;">
                            <label class="detail-label">Approved Date:</label>
                            <div class="detail-value" id="detail-approved"></div>
                        </div>
                        <div class="detail-group" id="rejected-date-group" style="display:none;">
                            <label class="detail-label">Rejected Date:</label>
                            <div class="detail-value" id="detail-rejected"></div>
                        </div>
                        <div class="detail-group">
                            <label class="detail-label">OTP Verified:</label>
                            <div class="detail-value" id="detail-otp"></div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="detail-group">
                            <label class="detail-label">Payment Details:</label>
                            <div class="detail-value detail-box" id="detail-payment-details"></div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3" id="admin-notes-group" style="display:none;">
                    <div class="col-md-12">
                        <div class="detail-group">
                            <label class="detail-label">Admin Notes:</label>
                            <div class="detail-value detail-box" id="detail-admin-notes"></div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3" id="rejected-reason-group" style="display:none;">
                    <div class="col-md-12">
                        <div class="detail-group">
                            <label class="detail-label">Rejection Reason:</label>
                            <div class="detail-value detail-box alert alert-danger" id="detail-rejected-reason"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.detail-group {
    margin-bottom: 15px;
}
.detail-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 12px;
    text-transform: uppercase;
    margin-bottom: 5px;
}
.detail-value {
    font-size: 14px;
    color: #333;
    font-weight: 500;
}
.detail-box {
    background-color: #f8f9fa;
    padding: 12px;
    border-radius: 4px;
    border-left: 3px solid #007bff;
    word-break: break-all;
}
</style>

<?php include 'footer.php'; ?>