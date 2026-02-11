<?php
require_once 'admin_header.php';
?>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Withdrawal Management</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="admin_dashboard.php" class="breadcrumb-item"><i class="anticon anticon-home"></i> Dashboard</a>
                <span class="breadcrumb-item active">Withdrawals</span>
            </nav>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Withdrawal Requests</h5>
                <div class="btn-group">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#filterWithdrawalsModal">
                        <i class="anticon anticon-filter"></i> Filter
                    </button>
                </div>
            </div>
            
            <div class="m-t-25">
                <table id="withdrawalsTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- AJAX will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterWithdrawalsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Withdrawals</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <form id="filterWithdrawalsForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select class="form-control" name="method_code">
                            <option value="">All Methods</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="paypal">PayPal</option>
                            <option value="bitcoin">Bitcoin</option>
                            <option value="ethereum">Ethereum</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date Range</label>
                        <div class="input-daterange input-group" data-provide="datepicker">
                            <input type="text" class="form-control" name="start_date">
                            <span class="input-group-addon">to</span>
                            <input type="text" class="form-control" name="end_date">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Withdrawal Details Modal -->
<div class="modal fade" id="withdrawalDetailsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Withdrawal Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <div class="modal-body" id="withdrawalDetailsContent">
                <!-- AJAX will populate this -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success approve-withdrawal">Approve</button>
                <button type="button" class="btn btn-danger reject-withdrawal">Reject</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    const withdrawalsTable = $('#withdrawalsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'admin_ajax/get_withdrawals.php',
            type: 'POST'
        },
        columns: [
            { data: 'id' },
            { 
                data: null,
                render: function(data) {
                    return data.user_first_name + ' ' + data.user_last_name;
                }
            },
            { 
                data: 'amount',
                render: function(data) {
                    return '$' + parseFloat(data).toFixed(2);
                }
            },
            { data: 'method_name' },
            { 
                data: 'status',
                render: function(data) {
                    const statusClass = {
                        pending: 'warning',
                        processing: 'info',
                        completed: 'success',
                        failed: 'danger',
                        cancelled: 'secondary'
                    }[data];
                    return `<span class="badge badge-${statusClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleString();
                }
            },
            {
                data: 'id',
                render: function(data, type, row) {
                    let buttons = `
                        <button class="btn btn-sm btn-primary view-withdrawal" data-id="${data}">
                            <i class="anticon anticon-eye"></i>
                        </button>`;
                    
                    if (row.status === 'pending' || row.status === 'processing') {
                        buttons += `
                            <button class="btn btn-sm btn-success approve-withdrawal" data-id="${data}">
                                <i class="anticon anticon-check"></i>
                            </button>
                            <button class="btn btn-sm btn-danger reject-withdrawal" data-id="${data}">
                                <i class="anticon anticon-close"></i>
                            </button>`;
                    }
                    
                    return `<div class="btn-group">${buttons}</div>`;
                }
            }
        ]
    });

    // View Withdrawal Details
    let currentWithdrawalId = null;
    $('#withdrawalsTable').on('click', '.view-withdrawal', function() {
        currentWithdrawalId = $(this).data('id');
        
        $.ajax({
            url: 'admin_ajax/get_withdrawal.php',
            type: 'GET',
            data: { id: currentWithdrawalId },
            success: function(response) {
                if (response.success) {
                    const withdrawal = response.withdrawal;
                    
                    $('#withdrawalDetailsContent').html(`
                        <div class="form-group">
                            <label>Transaction ID</label>
                            <p>${withdrawal.id}</p>
                        </div>
                        <div class="form-group">
                            <label>User</label>
                            <p>${withdrawal.user_first_name} ${withdrawal.user_last_name}</p>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <p>$${parseFloat(withdrawal.amount).toFixed(2)}</p>
                        </div>
                        <div class="form-group">
                            <label>Payment Method</label>
                            <p>${withdrawal.method_name}</p>
                        </div>
                        <div class="form-group">
                            <label>Payment Details</label>
                            <p>${withdrawal.payment_details || 'N/A'}</p>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <p>${withdrawal.status.charAt(0).toUpperCase() + withdrawal.status.slice(1)}</p>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <p>${new Date(withdrawal.created_at).toLocaleString()}</p>
                        </div>
                        <div class="form-group">
                            <label>Reference</label>
                            <p>${withdrawal.reference || 'N/A'}</p>
                        </div>
                        <div class="form-group">
                            <label>Admin Notes</label>
                            <p>${withdrawal.admin_notes || 'N/A'}</p>
                        </div>
                    `);
                    
                    // Show/hide buttons based on status
                    if (withdrawal.status === 'pending' || withdrawal.status === 'processing') {
                        $('.approve-withdrawal, .reject-withdrawal').show();
                    } else {
                        $('.approve-withdrawal, .reject-withdrawal').hide();
                    }
                    
                    $('#withdrawalDetailsModal').modal('show');
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Approve Withdrawal
    $('.approve-withdrawal').click(function() {
        if (confirm('Are you sure you want to approve this withdrawal?')) {
            $.ajax({
                url: 'admin_ajax/approve_withdrawal.php',
                type: 'POST',
                data: { id: currentWithdrawalId },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        withdrawalsTable.ajax.reload();
                        $('#withdrawalDetailsModal').modal('hide');
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });

    // Reject Withdrawal
    $('.reject-withdrawal').click(function() {
        const reason = prompt('Please enter the rejection reason:');
        if (reason !== null) {
            $.ajax({
                url: 'admin_ajax/reject_withdrawal.php',
                type: 'POST',
                data: { 
                    id: currentWithdrawalId,
                    reason: reason
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        withdrawalsTable.ajax.reload();
                        $('#withdrawalDetailsModal').modal('hide');
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });

    // Apply Filters
    $('#filterWithdrawalsForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        withdrawalsTable.ajax.url('admin_ajax/get_withdrawals.php?' + formData).load();
        $('#filterWithdrawalsModal').modal('hide');
    });
});
</script>