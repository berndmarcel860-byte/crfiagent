<?php
require_once 'admin_header.php';

// Get all users and platforms for dropdowns
$users = $pdo->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM users")->fetchAll();
$platforms = $pdo->query("SELECT id, name FROM scam_platforms")->fetchAll();
$admins = $pdo->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM admins")->fetchAll();
?>

<div class="main-content">
    <div class="page-header">
        <h2>Case Management</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="admin_dashboard.php" class="breadcrumb-item"><i class="anticon anticon-home"></i> Dashboard</a>
                <span class="breadcrumb-item active">Cases</span>
            </nav>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Case List</h5>
                <button class="btn btn-primary" data-toggle="modal" data-target="#addCaseModal">
                    <i class="anticon anticon-plus"></i> Add Case
                </button>
            </div>
            
            <div class="m-t-25">
                <table id="casesTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Case #</th>
                            <th>User</th>
                            <th>Platform</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Case Modal -->
<div class="modal fade" id="addCaseModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Case</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <form id="addCaseForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>User</label>
                                <select class="form-control" name="user_id" required>
                                    <option value="">Select User</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Platform</label>
                                <select class="form-control" name="platform_id" required>
                                    <option value="">Select Platform</option>
                                    <?php foreach ($platforms as $platform): ?>
                                        <option value="<?= $platform['id'] ?>"><?= htmlspecialchars($platform['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Reported Amount</label>
                                <input type="number" step="0.01" class="form-control" name="reported_amount" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Assigned To</label>
                                <select class="form-control" name="admin_id">
                                    <option value="">Select Admin</option>
                                    <?php foreach ($admins as $admin): ?>
                                        <option value="<?= $admin['id'] ?>"><?= htmlspecialchars($admin['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Case</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Case Modal -->
<div class="modal fade" id="editCaseModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Case <span id="editCaseNumber"></span></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <form id="editCaseForm">
                <input type="hidden" name="case_id" id="editCaseId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>User</label>
                                <p class="form-control-static" id="editCaseUser"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Platform</label>
                                <p class="form-control-static" id="editCasePlatform"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Reported Amount</label>
                                <p class="form-control-static" id="editCaseAmount"></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status" id="editCaseStatus">
                                    <option value="open">Open</option>
                                    <option value="documents_required">Documents Required</option>
                                    <option value="under_review">Under Review</option>
                                    <option value="refund_approved">Refund Approved</option>
                                    <option value="refund_rejected">Refund Rejected</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Assigned To</label>
                                <select class="form-control" name="admin_id" id="editCaseAdmin">
                                    <option value="">Unassigned</option>
                                    <?php foreach ($admins as $admin): ?>
                                        <option value="<?= $admin['id'] ?>"><?= htmlspecialchars($admin['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Admin Notes</label>
                        <textarea class="form-control" name="admin_notes" id="editCaseNotes" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status Change Notes</label>
                        <textarea class="form-control" name="status_notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Case Details Modal -->
<div class="modal fade" id="caseDetailsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Case Details <span id="caseDetailsNumber"></span></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>User Information</h6>
                        <p id="caseDetailsUser"></p>
                        <p id="caseDetailsUserEmail"></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Case Information</h6>
                        <p><strong>Platform:</strong> <span id="caseDetailsPlatform"></span></p>
                        <p><strong>Amount:</strong> <span id="caseDetailsAmount"></span></p>
                        <p><strong>Status:</strong> <span id="caseDetailsStatus"></span></p>
                    </div>
                </div>
                
                <h6>Description</h6>
                <div class="card mb-4">
                    <div class="card-body" id="caseDetailsDescription"></div>
                </div>
                
                <h6>Admin Notes</h6>
                <div class="card mb-4">
                    <div class="card-body" id="caseDetailsAdminNotes"></div>
                </div>
                
                <h6>Status History</h6>
                <div class="table-responsive">
                    <table class="table" id="caseDetailsHistory">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Changed By</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                
                <h6 class="mt-4">Documents</h6>
                <div id="caseDetailsDocuments">
                    <p>No documents uploaded</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    const casesTable = $('#casesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'admin_ajax/get_cases.php',
            type: 'POST'
        },
        columns: [
            { data: 'case_number' },
            { 
                data: null,
                render: function(data) {
                    return data.user_first_name + ' ' + data.user_last_name;
                }
            },
            { 
                data: null,
                render: function(data) {
                    return data.platform_name || 'N/A';
                }
            },
            { 
                data: 'reported_amount',
                render: function(data) {
                    return '$' + parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    const statusClass = {
                        open: 'secondary',
                        documents_required: 'warning',
                        under_review: 'info',
                        refund_approved: 'success',
                        refund_rejected: 'danger',
                        closed: 'dark'
                    }[data];
                    return `<span class="badge badge-${statusClass}">${data.replace(/_/g, ' ')}</span>`;
                }
            },
            { 
                data: null,
                render: function(data) {
                    return data.admin_first_name 
                        ? data.admin_first_name + ' ' + data.admin_last_name 
                        : 'Unassigned';
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
                            <button class="btn btn-sm btn-info view-case" data-id="${data}" title="View">
                                <i class="anticon anticon-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary edit-case" data-id="${data}" title="Edit">
                                <i class="anticon anticon-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-case" data-id="${data}" title="Delete">
                                <i class="anticon anticon-delete"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // Add Case Form Submission
    $('#addCaseForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serializeArray();
        const postData = {};
        $.each(formData, function(i, field) {
            postData[field.name] = field.value;
        });

        $.ajax({
            url: 'admin_ajax/add_case.php',
            type: 'POST',
            data: JSON.stringify(postData),
            contentType: 'application/json',
            beforeSend: function() {
                $('#addCaseForm button[type="submit"]').prop('disabled', true)
                    .html('<i class="anticon anticon-loading anticon-spin"></i> Adding...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#addCaseModal').modal('hide');
                    casesTable.ajax.reload();
                    $('#addCaseForm')[0].reset();
                } else {
                    toastr.error(response.message);
                    if (response.error) {
                        console.error("Error:", response.error);
                    }
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Failed to add case");
                console.error("AJAX Error:", status, error);
            },
            complete: function() {
                $('#addCaseForm button[type="submit"]').prop('disabled', false).html('Add Case');
            }
        });
    });

    // View Case Details
    $('#casesTable').on('click', '.view-case', function() {
        const caseId = $(this).data('id');
        
        $.get('admin_ajax/get_case.php?id=' + caseId, function(response) {
            if (response.success) {
                const caseData = response.case;
                
                // Set basic info
                $('#caseDetailsNumber').text(caseData.case_number);
                $('#caseDetailsUser').text(caseData.user_first_name + ' ' + caseData.user_last_name);
                $('#caseDetailsUserEmail').text(caseData.user_email);
                $('#caseDetailsPlatform').text(caseData.platform_name);
                $('#caseDetailsAmount').text('$' + parseFloat(caseData.reported_amount).toFixed(2));
                $('#caseDetailsStatus').html(`
                    <span class="badge badge-${getStatusClass(caseData.status)}">
                        ${caseData.status.replace(/_/g, ' ')}
                    </span>
                `);
                $('#caseDetailsDescription').text(caseData.description);
                $('#caseDetailsAdminNotes').text(caseData.admin_notes || 'No notes available');
                
                // Populate history
                const historyTable = $('#caseDetailsHistory tbody');
                historyTable.empty();
                
                if (response.history.length > 0) {
                    response.history.forEach(history => {
                        historyTable.append(`
                            <tr>
                                <td>${new Date(history.created_at).toLocaleString()}</td>
                                <td>${history.first_name} ${history.last_name}</td>
                                <td>${history.old_status ? history.old_status.replace(/_/g, ' ') : 'N/A'}</td>
                                <td>${history.new_status.replace(/_/g, ' ')}</td>
                                <td>${history.notes || ''}</td>
                            </tr>
                        `);
                    });
                } else {
                    historyTable.append('<tr><td colspan="5">No history available</td></tr>');
                }
                
                // Populate documents
                const documentsContainer = $('#caseDetailsDocuments');
                documentsContainer.empty();
                
                if (response.documents.length > 0) {
                    const docList = $('<div class="list-group"></div>');
                    response.documents.forEach(doc => {
                        docList.append(`
                            <a href="${doc.file_path}" target="_blank" class="list-group-item list-group-item-action">
                                <i class="anticon anticon-file"></i> ${doc.document_type}
                            </a>
                        `);
                    });
                    documentsContainer.append(docList);
                } else {
                    documentsContainer.append('<p>No documents uploaded</p>');
                }
                
                $('#caseDetailsModal').modal('show');
            } else {
                toastr.error(response.message);
            }
        });
    });

    // Edit Case - Load Data
    $('#casesTable').on('click', '.edit-case', function() {
        const caseId = $(this).data('id');
        
        $.get('admin_ajax/get_case.php?id=' + caseId, function(response) {
            if (response.success) {
                const caseData = response.case;
                
                $('#editCaseId').val(caseData.id);
                $('#editCaseNumber').text(caseData.case_number);
                $('#editCaseUser').text(caseData.user_first_name + ' ' + caseData.user_last_name);
                $('#editCasePlatform').text(caseData.platform_name);
                $('#editCaseAmount').text('$' + parseFloat(caseData.reported_amount).toFixed(2));
                $('#editCaseStatus').val(caseData.status);
                $('#editCaseAdmin').val(caseData.admin_id || '');
                $('#editCaseNotes').val(caseData.admin_notes || '');
                
                $('#editCaseModal').modal('show');
            } else {
                toastr.error(response.message);
            }
        });
    });

    // Edit Case Form Submission
    $('#editCaseForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serializeArray();
        const postData = {};
        $.each(formData, function(i, field) {
            postData[field.name] = field.value;
        });

        $.ajax({
            url: 'admin_ajax/update_case.php',
            type: 'POST',
            data: JSON.stringify(postData),
            contentType: 'application/json',
            beforeSend: function() {
                $('#editCaseForm button[type="submit"]').prop('disabled', true)
                    .html('<i class="anticon anticon-loading anticon-spin"></i> Saving...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#editCaseModal').modal('hide');
                    casesTable.ajax.reload();
                } else {
                    toastr.error(response.message);
                    if (response.error) {
                        console.error("Error:", response.error);
                    }
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Failed to update case");
                console.error("AJAX Error:", status, error);
            },
            complete: function() {
                $('#editCaseForm button[type="submit"]').prop('disabled', false).html('Save Changes');
            }
        });
    });

    // Delete Case
    $('#casesTable').on('click', '.delete-case', function() {
        const caseId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this case? This action cannot be undone.')) {
            $.ajax({
                url: 'admin_ajax/delete_case.php',
                type: 'POST',
                data: { case_id: caseId },
                beforeSend: function() {
                    $(this).prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        casesTable.ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("Failed to delete case");
                    console.error("AJAX Error:", status, error);
                },
                complete: function() {
                    $(this).prop('disabled', false);
                }
            });
        }
    });

    // Helper function for status badges
    function getStatusClass(status) {
        const statusClasses = {
            open: 'secondary',
            documents_required: 'warning',
            under_review: 'info',
            refund_approved: 'success',
            refund_rejected: 'danger',
            closed: 'dark'
        };
        return statusClasses[status] || 'secondary';
    }
});
</script>