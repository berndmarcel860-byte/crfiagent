<?php include 'header.php'; ?>

<!-- Content Wrapper START -->
<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="page-title">My Cases</h2>
                    <p class="m-b-10">View and manage all your reported cases</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="new-case.php" class="btn btn-primary btn-lg">
                        <i class="anticon anticon-plus"></i> Report New Case
                    </a>
                </div>
            </div>
        </div>

        <!-- Cases Table Card -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title d-flex align-items-center m-0">
                            <i class="anticon anticon-folder-open me-2"></i>
                            My Reported Cases
                        </h4>
                        <button class="btn btn-light refresh-btn" title="Refresh Table">
                            <i class="anticon anticon-reload"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="casesTable" class="table table-borderless align-middle nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Case ID</th>
                                        <th>Platform</th>
                                        <th>Reported Amount</th>
                                        <th>Recovered Amount</th>
                                        <th>Status</th>
                                        <th>Date Created</th>
                                        <th>Last Updated</th>
                                        <th class="text-center">Actions</th>
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

<!-- Case Details Modal -->
<div class="modal fade" id="caseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="anticon anticon-file-text me-2"></i>
                    Case #<span id="caseNumber"></span> Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="caseModalContent">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Case Information</h6>
                        <p><strong>Platform:</strong> <span id="casePlatform"></span></p>
                        <p><strong>Reported Amount:</strong> <span id="caseAmount"></span></p>
                        <p><strong>Recovered Amount:</strong> <span id="caseRecovered"></span></p>
                        <p><strong>Status:</strong> <span id="caseStatus"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Recovery Progress</h6>
                        <div class="progress" style="height: 20px;">
                            <div id="caseProgress" class="progress-bar" role="progressbar"></div>
                        </div>
                        <small id="caseProgressText" class="form-text text-muted"></small>
                    </div>
                </div>

                <div class="mt-4">
                    <h6>Case Description</h6>
                    <div class="card">
                        <div class="card-body" id="caseDescription"></div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6>Your Notes</h6>
                    <div class="card">
                        <div class="card-body" id="caseUserNotes"></div>
                    </div>
                </div>

                <div class="mt-4" id="documentsSection">
                    <h6>Documents</h6>
                    <div id="caseDocuments">
                        <p>No documents uploaded</p>
                    </div>
                </div>

                <div class="mt-4" id="statusHistorySection">
                    <h6>Status History</h6>
                    <div class="table-responsive">
                        <table class="table" id="caseHistory">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="anticon anticon-close me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Document Upload Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="anticon anticon-upload me-2"></i>
                    Upload Documents for Case #<span id="documentCaseNumber"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="documentUploadForm" enctype="multipart/form-data">
                <input type="hidden" id="documentCaseId" name="case_id">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="anticon anticon-info-circle"></i>
                        Please upload all required documents to proceed with your case.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Document Type</label>
                        <select class="form-control" name="document_type" required>
                            <option value="">Select Document Type</option>
                            <option value="proof_of_payment">Proof of Payment</option>
                            <option value="identity_verification">Identity Verification</option>
                            <option value="communication_records">Communication Records</option>
                            <option value="bank_statement">Bank Statement</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Document File</label>
                        <input type="file" class="form-control" id="documentFile" name="document_file" required>
                        <small class="form-text text-muted">Max file size: 5MB (PDF, JPG, PNG)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" name="document_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="anticon anticon-upload me-1"></i> Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- DataTables Bootstrap 5 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    const casesTable = $('#casesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 10,
        order: [[5, 'desc']],
        ajax: {
            url: 'user_ajax/get_my_cases.php',
            type: 'POST'
        },
        columns: [
            { data: 'case_number' },
            { data: null, render: d => d.platform_name || 'N/A' },
            { data: 'reported_amount', render: d => '$' + parseFloat(d).toFixed(2) },
            { data: 'recovered_amount', render: d => '$' + parseFloat(d || 0).toFixed(2) },
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
                    return `<span class="badge bg-${statusClass}">${data.replace(/_/g, ' ')}</span>`;
                }
            },
            { data: 'created_at', render: d => new Date(d).toLocaleDateString() },
            { data: 'updated_at', render: d => new Date(d).toLocaleDateString() },
            {
                data: 'id',
                render: function(data, type, row) {
                    let buttons = `
                        <button class="btn btn-sm btn-info view-case" data-id="${data}" title="View Details">
                            <i class="anticon anticon-eye"></i>
                        </button>`;
                    if (row.status === 'documents_required') {
                        buttons += `
                            <button class="btn btn-sm btn-warning upload-docs" 
                                    data-id="${data}" data-case-number="${row.case_number}" 
                                    title="Upload Documents">
                                <i class="anticon anticon-upload"></i>
                            </button>`;
                    }
                    return `<div class="btn-group">${buttons}</div>`;
                }
            }
        ]
    });

    // View Case Details
    $('#casesTable').on('click', '.view-case', function() {
        const caseId = $(this).data('id');
        loadCaseDetails(caseId);
    });

    // Open document upload modal
    $('#casesTable').on('click', '.upload-docs', function() {
        $('#documentCaseId').val($(this).data('id'));
        $('#documentCaseNumber').text($(this).data('case-number'));
        new bootstrap.Modal(document.getElementById('documentModal')).show();
    });

    // File input label update
    $('#documentFile').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('label').html(fileName || 'Choose file');
    });

    // Document upload form
    $('#documentUploadForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: 'user_ajax/upload_document.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#documentUploadForm button[type="submit"]').prop('disabled', true)
                    .html('<i class="anticon anticon-loading anticon-spin"></i> Uploading...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    bootstrap.Modal.getInstance(document.getElementById('documentModal')).hide();
                    $('#documentUploadForm')[0].reset();
                    $('#documentFile').next('label').html('Choose file');

                    if ($('#caseModal').is(':visible')) {
                        loadCaseDetails($('#documentCaseId').val());
                    }
                    casesTable.ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error("Failed to upload document");
            },
            complete: function() {
                $('#documentUploadForm button[type="submit"]').prop('disabled', false)
                    .html('<i class="anticon anticon-upload me-1"></i> Upload Document');
            }
        });
    });

    function loadCaseDetails(caseId) {
        $.get('user_ajax/get_case_details.php?id=' + caseId, function(response) {
            if (response.success) {
                const caseData = response.case;
                const recovered = parseFloat(caseData.recovered_amount || 0);
                const reported = parseFloat(caseData.reported_amount || 1);
                const percentage = (recovered / reported * 100).toFixed(1);

                $('#caseNumber').text(caseData.case_number);
                $('#casePlatform').text(caseData.platform_name);
                $('#caseAmount').text('$' + reported.toFixed(2));
                $('#caseRecovered').text('$' + recovered.toFixed(2));
                $('#caseStatus').html(`<span class="badge bg-${getStatusClass(caseData.status)}">${caseData.status.replace(/_/g, ' ')}</span>`);
                $('#caseDescription').text(caseData.description);
                $('#caseUserNotes').text(caseData.user_notes || 'No notes added');

                $('#caseProgress').css('width', percentage + '%').attr('aria-valuenow', percentage).text(`${percentage}%`);
                $('#caseProgressText').text(`Recovered $${recovered.toFixed(2)} of $${reported.toFixed(2)} (${percentage}%)`);

                new bootstrap.Modal(document.getElementById('caseModal')).show();
            } else {
                toastr.error(response.message);
            }
        });
    }

    function getStatusClass(status) {
        return {
            open: 'secondary',
            documents_required: 'warning',
            under_review: 'info',
            refund_approved: 'success',
            refund_rejected: 'danger',
            closed: 'dark'
        }[status] || 'secondary';
    }

    // Refresh button
    $('.refresh-btn').click(function() {
        casesTable.ajax.reload();
        toastr.success('Cases refreshed');
    });
});
</script>
