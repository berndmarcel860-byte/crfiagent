<?php
require_once 'admin_header.php';
?>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">KYC Verification</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="admin_dashboard.php" class="breadcrumb-item"><i class="anticon anticon-home"></i> Dashboard</a>
                <span class="breadcrumb-item active">KYC</span>
            </nav>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5>KYC Requests</h5>
                <div class="btn-group">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#filterKYCModal">
                        <i class="anticon anticon-filter"></i> Filter
                    </button>
                </div>
            </div>
            
            <div class="m-t-25">
                <table id="kycTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Document Type</th>
                            <th>Status</th>
                            <th>Submitted</th>
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
<div class="modal fade" id="filterKYCModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter KYC Requests</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <form id="filterKYCForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Document Type</label>
                        <select class="form-control" name="document_type">
                            <option value="">All Types</option>
                            <option value="passport">Passport</option>
                            <option value="id_card">ID Card</option>
                            <option value="driving_license">Driving License</option>
                            <option value="other">Other</option>
                        </select>
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

<!-- KYC Details Modal -->
<div class="modal fade" id="kycDetailsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">KYC Verification Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <div class="modal-body" id="kycDetailsContent">
                <!-- AJAX will populate this -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success approve-kyc">Approve</button>
                <button type="button" class="btn btn-danger reject-kyc">Reject</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    const kycTable = $('#kycTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'admin_ajax/get_kyc_requests.php',
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
                data: 'document_type',
                render: function(data) {
                    return data.charAt(0).toUpperCase() + data.slice(1).replace('_', ' ');
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    const statusClass = {
                        pending: 'warning',
                        approved: 'success',
                        rejected: 'danger'
                    }[data];
                    return `<span class="badge badge-${statusClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            {
                data: 'id',
                render: function(data, type, row) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary view-kyc" data-id="${data}">
                                <i class="anticon anticon-eye"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // View KYC Details
    let currentKycId = null;
    $('#kycTable').on('click', '.view-kyc', function() {
        currentKycId = $(this).data('id');
        
        $.ajax({
            url: 'admin_ajax/get_kyc_request.php',
            type: 'GET',
            data: { id: currentKycId },
            success: function(response) {
                if (response.success) {
                    const kyc = response.kyc;
                    
                    let documentFront = kyc.document_front ? 
                        `<a href="${kyc.document_front}" target="_blank"><img src="../${kyc.document_front}" class="img-fluid mb-2" style="max-height: 200px;"></a>` : 
                        'N/A';
                    
                    let documentBack = kyc.document_back ? 
                        `<a href="${kyc.document_back}" target="_blank"><img src="../${kyc.document_back}" class="img-fluid mb-2" style="max-height: 200px;"></a>` : 
                        'N/A';
                    
                    let selfie = kyc.selfie_with_id ? 
                        `<a href="${kyc.selfie_with_id}" target="_blank"><img src="../${kyc.selfie_with_id}" class="img-fluid mb-2" style="max-height: 200px;"></a>` : 
                        'N/A';
                    
                    let addressProof = kyc.address_proof ? 
                        `<a href="${kyc.address_proof}" target="_blank"><img src="../${kyc.address_proof}" class="img-fluid mb-2" style="max-height: 200px;"></a>` : 
                        'N/A';
                    
                    $('#kycDetailsContent').html(`
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>User</label>
                                    <p>${kyc.user_first_name} ${kyc.user_last_name}</p>
                                </div>
                                <div class="form-group">
                                    <label>Document Type</label>
                                    <p>${kyc.document_type.charAt(0).toUpperCase() + kyc.document_type.slice(1).replace('_', ' ')}</p>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <p>${kyc.status.charAt(0).toUpperCase() + kyc.status.slice(1)}</p>
                                </div>
                                <div class="form-group">
                                    <label>Submitted Date</label>
                                    <p>${new Date(kyc.created_at).toLocaleString()}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Document Front</label>
                                    <div>${documentFront}</div>
                                </div>
                                <div class="form-group">
                                    <label>Document Back</label>
                                    <div>${documentBack}</div>
                                </div>
                                <div class="form-group">
                                    <label>Selfie with ID</label>
                                    <div>${selfie}</div>
                                </div>
                                <div class="form-group">
                                    <label>Address Proof</label>
                                    <div>${addressProof}</div>
                                </div>
                            </div>
                        </div>
                    `);
                    
                    // Show/hide buttons based on status
                    if (kyc.status === 'pending') {
                        $('.approve-kyc, .reject-kyc').show();
                    } else {
                        $('.approve-kyc, .reject-kyc').hide();
                    }
                    
                    $('#kycDetailsModal').modal('show');
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Approve KYC
    $('.approve-kyc').click(function() {
        if (confirm('Are you sure you want to approve this KYC verification?')) {
            $.ajax({
                url: 'admin_ajax/approve_kyc.php',
                type: 'POST',
                data: { id: currentKycId },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        kycTable.ajax.reload();
                        $('#kycDetailsModal').modal('hide');
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });

    // Reject KYC
    $('.reject-kyc').click(function() {
        const reason = prompt('Please enter the rejection reason:');
        if (reason !== null) {
            $.ajax({
                url: 'admin_ajax/reject_kyc.php',
                type: 'POST',
                data: { 
                    id: currentKycId,
                    reason: reason
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        kycTable.ajax.reload();
                        $('#kycDetailsModal').modal('hide');
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });

    // Apply Filters
    $('#filterKYCForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        kycTable.ajax.url('admin_ajax/get_kyc_requests.php?' + formData).load();
        $('#filterKYCModal').modal('hide');
    });
});
</script>