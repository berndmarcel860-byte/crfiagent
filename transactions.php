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

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#transactionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'ajax/transactions.php',
            type: 'POST',
            data: function(d) {
                return JSON.stringify(d);
            },
            contentType: 'application/json',
            error: function(xhr, error, thrown) {
                console.error('DataTable Ajax error:', error, thrown);
                $('#transactionError').removeClass('d-none').text('Error loading transactions. Please refresh the page.');
            }
        },
        columns: [
            { 
                data: 'type',
                render: function(data, type, row) {
                    const typeLabels = {
                        'deposit': '<span class="badge badge-info">Deposit</span>',
                        'withdrawal': '<span class="badge badge-warning">Withdrawal</span>',
                        'refund': '<span class="badge badge-success">Refund</span>'
                    };
                    return typeLabels[data] || data;
                }
            },
            { 
                data: 'amount',
                render: function(data, type, row) {
                    return '€' + parseFloat(data).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                }
            },
            { 
                data: 'method',
                render: function(data, type, row) {
                    return data || 'N/A';
                }
            },
            { 
                data: 'status',
                render: function(data, type, row) {
                    const statusBadges = {
                        'pending': '<span class="badge badge-warning">Pending</span>',
                        'completed': '<span class="badge badge-success">Completed</span>',
                        'approved': '<span class="badge badge-success">Approved</span>',
                        'rejected': '<span class="badge badge-danger">Rejected</span>',
                        'processing': '<span class="badge badge-info">Processing</span>',
                        'failed': '<span class="badge badge-danger">Failed</span>'
                    };
                    return statusBadges[data.toLowerCase()] || '<span class="badge badge-secondary">' + data + '</span>';
                }
            },
            { 
                data: 'reference',
                render: function(data, type, row) {
                    return '<code style="font-size: 11px;">' + (data || 'N/A') + '</code>';
                }
            },
            { 
                data: 'created_at',
                render: function(data, type, row) {
                    if (!data) return 'N/A';
                    const date = new Date(data);
                    return date.toLocaleDateString('de-DE', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    if (row.type === 'withdrawal' && row.withdrawal_id) {
                        return '<button class="btn btn-sm btn-primary view-details" data-id="' + row.withdrawal_id + '" data-row=\'' + JSON.stringify(row) + '\'><i class="anticon anticon-eye"></i> Details</button>';
                    }
                    return '<span class="text-muted">N/A</span>';
                }
            }
        ],
        order: [[5, 'desc']], // Order by date descending
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            emptyTable: "No transactions found",
            info: "Showing _START_ to _END_ of _TOTAL_ transactions",
            infoEmpty: "Showing 0 to 0 of 0 transactions",
            infoFiltered: "(filtered from _MAX_ total transactions)",
            lengthMenu: "Show _MENU_ transactions",
            loadingRecords: "Loading...",
            processing: "Processing...",
            search: "Search:",
            zeroRecords: "No matching transactions found"
        }
    });

    // Refresh button
    $('#refreshTransactions').on('click', function() {
        table.ajax.reload(null, false);
    });

    // View details button click handler
    $('#transactionsTable').on('click', '.view-details', function() {
        const rowData = JSON.parse($(this).attr('data-row'));
        
        // Populate modal with withdrawal data
        $('#detail-reference').text(rowData.reference || 'N/A');
        $('#detail-amount').html('<strong>€' + parseFloat(rowData.amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '</strong>');
        $('#detail-method').text(rowData.method || 'N/A');
        $('#detail-payment-details').text(rowData.details || 'No details available');
        $('#detail-created').text(formatDate(rowData.created_at));
        $('#detail-otp').html(rowData.otp_verified == 1 ? '<span class="badge badge-success">✓ Verified</span>' : '<span class="badge badge-warning">Not Verified</span>');
        
        // Status with color
        const statusBadges = {
            'pending': '<span class="badge badge-warning">Pending</span>',
            'approved': '<span class="badge badge-success">Approved</span>',
            'rejected': '<span class="badge badge-danger">Rejected</span>',
            'processing': '<span class="badge badge-info">Processing</span>',
            'completed': '<span class="badge badge-success">Completed</span>'
        };
        $('#detail-status').html(statusBadges[rowData.status.toLowerCase()] || rowData.status);
        
        // Conditional fields - use processed_at for approved date if status is completed
        if (rowData.status && rowData.status.toLowerCase() === 'completed' && rowData.processed_at) {
            $('#approved-date-group').show();
            $('#detail-approved').text(formatDate(rowData.processed_at));
        } else {
            $('#approved-date-group').hide();
        }
        
        // Use updated_at for rejected/failed date if status is failed
        if ((rowData.status && (rowData.status.toLowerCase() === 'failed' || rowData.status.toLowerCase() === 'cancelled')) && rowData.updated_at) {
            $('#rejected-date-group').show();
            $('#detail-rejected').text(formatDate(rowData.updated_at));
        } else {
            $('#rejected-date-group').hide();
        }
        
        if (rowData.admin_notes) {
            $('#admin-notes-group').show();
            $('#detail-admin-notes').text(rowData.admin_notes);
        } else {
            $('#admin-notes-group').hide();
        }
        
        // Note: rejected_reason field doesn't exist in database, showing admin_notes instead
        $('#rejected-reason-group').hide();
        
        // Show modal
        $('#withdrawalDetailsModal').modal('show');
    });
    
    // Helper function to format dates
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('de-DE', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
});
</script>

<?php include 'footer.php'; ?>