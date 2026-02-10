import os

# Define the directory structure
js_dir = "assets/js"
if not os.path.exists(js_dir):
    os.makedirs(js_dir)

# Define the JavaScript files with their content
js_files = {
    "config.js": """// Global Configuration and Session Management
$(document).ready(function() {
    // Configure Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        preventDuplicates: true,
        showDuration: 300,
        hideDuration: 1000,
        timeOut: 5000,
        extendedTimeOut: 1000
    };

    // Global AJAX setup with session handling
    $.ajaxSetup({
        xhrFields: {
            withCredentials: true
        },
        error: function(xhr, status, error) {
            if (xhr.status === 401) {
                toastr.error('Your session has expired. Redirecting to login...');
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 2000);
            } else if (xhr.status === 500) {
                toastr.error('Server error occurred. Please try again later.');
            }
        }
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover',
        placement: 'top'
    });
});""",

    "sidebar.js": """// Sidebar and Navigation
$(document).ready(function() {
    $('.side-nav .dropdown-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $parent = $(this).closest('.nav-item.dropdown');
        var wasOpen = $parent.hasClass('open');
        
        $('.side-nav .nav-item.dropdown').removeClass('open');
        if (!wasOpen) $parent.addClass('open');
    });

    $('#toggle-mobile-sidebar').on('click', function(e) {
        e.preventDefault();
        $('body').toggleClass('side-nav-visible');
        $('.side-nav .nav-item.dropdown').removeClass('open');
    });

    $('#toggle-sidebar').on('click', function(e) {
        e.preventDefault();
        $('.side-nav').toggleClass('desktop-collapsed');
        $('.side-nav .nav-item.dropdown').removeClass('open');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.side-nav').length) {
            $('.side-nav .nav-item.dropdown').removeClass('open');
        }
        
        if (!$(e.target).closest('#toggle-mobile-sidebar').length && 
            !$(e.target).closest('.side-nav').length) {
            $('body').removeClass('side-nav-visible');
        }
    });

    $('.side-nav .dropdown-menu').on('click', function(e) {
        e.stopPropagation();
    });
});""",

    "charts.js": """// Dashboard Charts
$(document).ready(function() {
    if ($('#recoveryChart').length) {
        const ctx = document.getElementById('recoveryChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Recovered Amount',
                    data: [12500, 19000, 15000, 21000, 17500, 23000, 20000, 18500, 22000, 19500, 24000, 21000],
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});""",

    "documents.js": """// Documents Table Implementation
$(document).ready(function() {
    if ($('#documentsTable').length) {
        // Initialize documents table
        const initDocumentsTable = function() {
            return $('#documentsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "ajax/documents.php",
                    type: "POST",
                    data: function(d) {
                        d.csrf_token = $('meta[name="csrf-token"]').attr('content');
                    },
                    error: function(xhr, error, thrown) {
                        console.error('AJAX Error:', xhr.responseText);
                        $('#documentError').text('Failed to load documents. Please try again.').removeClass('d-none');
                    }
                },
                columns: [
                    { 
                        data: "document_name",
                        render: function(data, type, row) {
                            return data || row.document_type + ' Document';
                        }
                    },
                    { 
                        data: "document_type",
                        render: function(data) {
                            return data || 'N/A';
                        }
                    },
                    { 
                        data: "status",
                        render: function(data, type, row) {
                            return `<span class="badge badge-${row.status_class}">${data}</span>`;
                        }
                    },
                    { 
                        data: "uploaded_at",
                        render: function(data) {
                            return data ? new Date(data).toLocaleString() : '';
                        }
                    },
                    {
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-primary view-document" data-id="${data}" data-path="${row.file_path}">
                                        <i class="anticon anticon-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-document" data-id="${data}">
                                        <i class="anticon anticon-delete"></i> Delete
                                    </button>
                                </div>
                            `;
                        },
                        orderable: false
                    }
                ],
                responsive: true,
                order: [[3, 'desc']],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
                    emptyTable: "No documents found",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    lengthMenu: "Show _MENU_ entries"
                }
            });
        };

        let documentsTable = initDocumentsTable();

        // Refresh button
        $('#refreshDocuments').click(function() {
            $('#documentError').addClass('d-none');
            documentsTable.ajax.reload(null, false);
            toastr.info('Documents refreshed');
        });

        // View document
        $('#documentsTable').on('click', '.view-document', function() {
            const docId = $(this).data('id');
            const filePath = $(this).data('path');
            const $modal = $('#documentPreviewModal');
            const $previewContent = $('#previewContent');
            const $downloadBtn = $('#downloadDocumentBtn');
            const $previewTitle = $('#previewTitle');

            $previewContent.html(`
                <div class="text-center p-4">
                    <i class="anticon anticon-loading anticon-spin" style="font-size:24px"></i>
                    <p>Loading document preview...</p>
                </div>
            `);

            // Set download link
            $downloadBtn.attr('href', 'uploads/' + filePath);
            
            // Determine file type and display accordingly
            const fileExt = filePath.split('.').pop().toLowerCase();
            
            $.get('ajax/get-document.php', { id: docId })
                .done(function(response) {
                    if (response.success) {
                        $previewTitle.text(response.document.document_name || response.document.document_type);
                        
                        if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                            $previewContent.html(`
                                <img src="uploads/${filePath}" class="img-fluid" alt="Document Preview" style="max-height: 70vh;">
                                ${response.document.description ? `<div class="mt-3 text-left"><strong>Description:</strong> ${response.document.description}</div>` : ''}
                            `);
                        } else if (fileExt === 'pdf') {
                            $previewContent.html(`
                                <embed src="uploads/${filePath}#toolbar=0&navpanes=0" type="application/pdf" width="100%" height="600px">
                                ${response.document.description ? `<div class="mt-3 text-left"><strong>Description:</strong> ${response.document.description}</div>` : ''}
                            `);
                        } else {
                            $previewContent.html(`
                                <div class="alert alert-info">
                                    <i class="anticon anticon-info-circle"></i> Preview not available for this file type. Please download to view.
                                </div>
                                ${response.document.description ? `<div class="mt-3"><strong>Description:</strong> ${response.document.description}</div>` : ''}
                            `);
                        }
                    } else {
                        $previewContent.html(`
                            <div class="alert alert-danger">
                                ${response.message || 'Failed to load document details'}
                            </div>
                        `);
                    }
                })
                .fail(function(xhr) {
                    $previewContent.html(`
                        <div class="alert alert-danger">
                            ${xhr.status === 401 ? 'Session expired. Please login again.' : 'Failed to load document details. Please try again.'}
                        </div>
                    `);
                });

            $modal.modal('show');
        });

        // Delete document
        $('#documentsTable').on('click', '.delete-document', function() {
            const docId = $(this).data('id');
            
            if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
                $.ajax({
                    url: 'ajax/delete-document.php',
                    type: 'POST',
                    data: { id: docId },
                    beforeSend: function() {
                        $(this).prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin"></i> Deleting...');
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            documentsTable.ajax.reload(null, false);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.status === 401 ? 'Session expired. Please login again.' : 'Failed to delete document.');
                    }
                });
            }
        });

        // File upload form handling
        $('#documentFile').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
            
            // Validate file size (client-side)
            const file = this.files[0];
            if (file && file.size > 10 * 1024 * 1024) { // 10MB limit
                toastr.error('File size exceeds 10MB limit');
                $(this).val('');
                $(this).next('.custom-file-label').html('Choose file (Max 10MB)');
            }
        });

        $('#documentForm').submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const $submitBtn = $(this).find('button[type="submit"]');
            
            $submitBtn.prop('disabled', true)
                .html('<i class="anticon anticon-loading anticon-spin"></i> Uploading...');
            
            $.ajax({
                url: 'ajax/upload-document.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else if (response.success) {
                        toastr.success(response.message);
                        $('#uploadDocumentModal').modal('hide');
                        documentsTable.ajax.reload();
                        $('#documentForm')[0].reset();
                        $('.custom-file-label').html('Choose file (Max 10MB)');
                    } else {
                        toastr.error(response.message);
                    }
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).html('<i class="anticon anticon-upload"></i> Upload Document');
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        window.location.href = 'login.php';
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        toastr.error(xhr.responseJSON.error);
                    } else {
                        toastr.error('Failed to upload document. Please try again.');
                    }
                }
            });
        });
    }
});""",

    "payment-methods.js": """// Payment Methods
$(document).ready(function() {
    if ($('#paymentMethodForm').length) {
        $('#paymentMethodForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            
            $.ajax({
                url: 'ajax/update-payment-method.php',
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    $('#paymentMethodForm button[type="submit"]').prop('disabled', true)
                        .html('<i class="anticon anticon-loading anticon-spin"></i> Saving...');
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                complete: function() {
                    $('#paymentMethodForm button[type="submit"]').prop('disabled', false)
                        .html('Save Changes');
                }
            });
        });
    }
});""",

    "transactions.js": """// Transactions Table
$(document).ready(function() {
    if ($('#transactionsTable').length) {
        // Initialize transactions table
        const initTransactionsTable = function() {
            return $('#transactionsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "ajax/transactions.php",
                    type: "POST",
                    data: function(d) {
                        d.csrf_token = $('meta[name="csrf-token"]').attr('content');
                    },
                    error: function(xhr, error, thrown) {
                        console.error('AJAX Error:', xhr.responseText);
                        $('#transactionError').text('Failed to load transactions. Please try again.').removeClass('d-none');
                    }
                },
                columns: [
                    { 
                        data: "type",
                        render: function(data) {
                            return data ? data.charAt(0).toUpperCase() + data.slice(1) : '';
                        }
                    },
                    { 
                        data: "amount",
                        render: function(data) {
                            return data ? '$' + parseFloat(data).toFixed(2) : '$0.00';
                        }
                    },
                    { 
                        data: "method",
                        render: function(data) {
                            return data || 'N/A';
                        }
                    },
                    { 
                        data: "status",
                        render: function(data) {
                            if (!data) return '';
                            const statusClass = {
                                'pending': 'warning',
                                'completed': 'success',
                                'failed': 'danger',
                                'cancelled': 'secondary'
                            }[data.toLowerCase()] || 'info';
                            return `<span class="badge badge-${statusClass}">${data}</span>`;
                        }
                    },
                    { 
                        data: "created_at",
                        render: function(data) {
                            return data ? new Date(data).toLocaleString() : '';
                        }
                    }
                ],
                responsive: true,
                order: [[4, 'desc']],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
                    emptyTable: "No transactions found",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    lengthMenu: "Show _MENU_ entries"
                }
            });
        };

        let transactionsTable = initTransactionsTable();

        // Refresh button
        $('#refreshTransactions').click(function() {
            $('#transactionError').addClass('d-none');
            transactionsTable.ajax.reload(null, false);
            toastr.info('Transactions refreshed');
        });
    }
});""",

    "withdrawals.js": """// Withdrawals Table Implementation
$(document).ready(function() {
    if ($('#withdrawalsTable').length) {
        // Initialize withdrawals table
        const initWithdrawalsTable = function() {
            return $('#withdrawalsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "ajax/withdrawal.php",
                    type: "POST",
                    data: function(d) {
                        d.csrf_token = $('meta[name="csrf-token"]').attr('content');
                    },
                    error: function(xhr, error, thrown) {
                        console.error('AJAX Error:', xhr.responseText);
                        $('#withdrawalError').text('Failed to load withdrawals. Please try again.').removeClass('d-none');
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.database_error) {
                                console.error('Database Error:', response.database_error);
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                    }
                },
                columns: [
                    { data: "id" },
                    { 
                        data: "amount",
                        render: function(data) {
                            return data ? '$' + parseFloat(data).toFixed(2) : '$0.00';
                        }
                    },
                    { 
                        data: "method",
                        render: function(data) {
                            return data || 'N/A';
                        }
                    },
                    { 
                        data: "status",
                        render: function(data) {
                            if (!data) return '';
                            const statusClass = {
                                'pending': 'warning',
                                'completed': 'success',
                                'failed': 'danger',
                                'cancelled': 'secondary',
                                'processing': 'info'
                            }[data.toLowerCase()] || 'light';
                            return `<span class="badge badge-${statusClass}">${data}</span>`;
                        }
                    },
                    { 
                        data: "created_at",
                        render: function(data) {
                            return data ? new Date(data).toLocaleString() : '';
                        }
                    },
                    {
                        data: "id",
                        render: function(data, type, row) {
                            let buttons = '';
                            if (row.status.toLowerCase() === 'pending') {
                                buttons += `<button class="btn btn-sm btn-danger cancel-withdrawal mr-1" data-id="${data}">
                                          <i class="anticon anticon-close"></i> Cancel
                                          </button>`;
                            }
                            buttons += `<button class="btn btn-sm btn-primary view-withdrawal" data-id="${data}">
                                      <i class="anticon anticon-eye"></i> View
                                      </button>`;
                            return buttons;
                        },
                        orderable: false
                    }
                ],
                responsive: true,
                order: [[4, 'desc']],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
                    emptyTable: "No withdrawal requests found",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    lengthMenu: "Show _MENU_ entries"
                }
            });
        };

        let withdrawalsTable = initWithdrawalsTable();

        // Refresh button
        $('#refreshWithdrawals').click(function() {
            $('#withdrawalError').addClass('d-none');
            withdrawalsTable.ajax.reload(null, false);
            toastr.info('Withdrawals refreshed');
        });

        // Withdrawal form submission
        $('#withdrawalForm').submit(function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const $submitBtn = $(this).find('button[type="submit"]');
            
            $submitBtn.prop('disabled', true)
                .html('<i class="anticon anticon-loading anticon-spin"></i> Processing...');
            
            $.ajax({
                url: 'ajax/process-withdrawal.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else if (response.success) {
                        toastr.success(response.message);
                        $('#newWithdrawalModal').modal('hide');
                        withdrawalsTable.ajax.reload();
                        $('#withdrawalForm')[0].reset();
                    } else {
                        toastr.error(response.message);
                    }
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).html('Submit Request');
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to process withdrawal.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                    } catch (e) {
                        console.error('Error parsing error response:', e);
                    }
                    toastr.error(xhr.status === 401 ? 'Session expired. Please login again.' : errorMsg);
                }
            });
        });

        // Cancel withdrawal
        $('#withdrawalsTable').on('click', '.cancel-withdrawal', function() {
            const withdrawalId = $(this).data('id');
            const $button = $(this);
            
            if (confirm('Are you sure you want to cancel this withdrawal request?')) {
                $button.prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin"></i> Processing...');
                
                $.ajax({
                    url: 'ajax/cancel-withdrawal.php',
                    type: 'POST',
                    data: { 
                        id: withdrawalId,
                        csrf_token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            withdrawalsTable.ajax.reload();
                        } else {
                            toastr.error(response.message);
                            $button.prop('disabled', false).html('<i class="anticon anticon-close"></i> Cancel');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.status === 401 ? 'Session expired. Please login again.' : 'Failed to cancel withdrawal.');
                        $button.prop('disabled', false).html('<i class="anticon anticon-close"></i> Cancel');
                    }
                });
            }
        });

        // View withdrawal details
        $('#withdrawalsTable').on('click', '.view-withdrawal', function() {
            const withdrawalId = $(this).data('id');
            const $modal = $('#withdrawalDetailsModal');
            const $modalContent = $('#withdrawalDetailsContent');
            
            $modalContent.html(`
                <div class="text-center p-4">
                    <i class="anticon anticon-loading anticon-spin" style="font-size:24px"></i>
                    <p>Loading withdrawal details...</p>
                </div>
            `);
            
            $modal.modal('show');
            
            $.get('ajax/get-withdrawal.php', { id: withdrawalId })
                .done(function(response) {
                    if (response.success) {
                        const withdrawal = response.withdrawal;
                        $modalContent.html(`
                            <div class="modal-header">
                                <h5 class="modal-title">Withdrawal #${withdrawal.id}</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <i class="anticon anticon-close"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>Amount:</strong> $${parseFloat(withdrawal.amount).toFixed(2)}</p>
                                        <p><strong>Method:</strong> ${withdrawal.method}</p>
                                        <p><strong>Status:</strong> <span class="badge badge-${withdrawal.status === 'pending' ? 'warning' : 
                                            withdrawal.status === 'completed' ? 'success' : 
                                            withdrawal.status === 'failed' ? 'danger' : 'info'}">${withdrawal.status}</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Reference:</strong> ${withdrawal.reference}</p>
                                        <p><strong>Request Date:</strong> ${new Date(withdrawal.created_at).toLocaleString()}</p>
                                        ${withdrawal.updated_at ? `<p><strong>Last Update:</strong> ${new Date(withdrawal.updated_at).toLocaleString()}</p>` : ''}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><strong>Payment Details:</strong></label>
                                    <textarea class="form-control" rows="3" readonly>${withdrawal.payment_details}</textarea>
                                </div>
                                ${withdrawal.admin_notes ? `
                                <div class="form-group">
                                    <label><strong>Admin Notes:</strong></label>
                                    <textarea class="form-control" rows="2" readonly>${withdrawal.admin_notes}</textarea>
                                </div>
                                ` : ''}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        `);
                    } else {
                        $modalContent.html(`
                            <div class="alert alert-danger">
                                ${response.message || 'Failed to load withdrawal details'}
                            </div>
                        `);
                    }
                })
                .fail(function(xhr) {
                    $modalContent.html(`
                        <div class="alert alert-danger">
                            ${xhr.status === 401 ? 'Session expired. Please login again.' : 'Failed to load withdrawal details. Please try again.'}
                        </div>
                    `);
                });
        });
    }
});""",

    "kyc.js": """// KYC Verification System
$(document).ready(function() {
    if ($('#kycForm').length) {
        // Enhanced document type handler with animation
        $('#documentType').change(function() {
            const isPassport = $(this).val() === 'passport';
            $('#backDocumentGroup').stop(true, true)[isPassport ? 'slideUp' : 'slideDown'](300, function() {
                $('#documentBack').prop('required', !isPassport);
            });
        }).trigger('change'); // Initialize on load

        // Modern file input handler with preview
        $('.custom-file-input').on('change', function() {
            const $input = $(this);
            const $label = $input.next('.custom-file-label');
            const file = this.files[0];
            
            if (!file) {
                $label.html('Choose file');
                return;
            }

            // Client-side validation
            if (file.size > 10 * 1024 * 1024) { // 10MB limit
                toastr.error('File size exceeds 10MB limit');
                $input.val('');
                $label.html('Choose file (Max 10MB)');
                return;
            }

            $label.html(file.name);
            
            // Show preview for images
            if (file.type.match('image.*') && $(this).data('preview-target')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $($input.data('preview-target')).attr('src', e.target.result).show();
                }
                reader.readAsDataURL(file);
            }
        });

        // Modern form submission with progress tracking
        $('#kycForm').submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const $submitBtn = $('#submitKycBtn');
            const $progress = $('<div class="progress mt-2"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div></div>');
            
            $submitBtn.after($progress).prop('disabled', true)
                     .html('<i class="anticon anticon-loading anticon-spin"></i> Uploading...');

            // Add CSRF token if available
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            if (csrfToken) formData.append('csrf_token', csrfToken);

            $.ajax({
                url: 'kyc.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            $progress.find('.progress-bar').css('width', percent + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else if (response.success) {
                        toastr.success(response.message || 'KYC submitted successfully!');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        handleSubmissionError(response.message || 'Submission failed. Please try again.');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to submit KYC documents.';
                    try {
                        const response = xhr.responseJSON || JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                        
                        // Handle specific error cases
                        if (xhr.status === 413) errorMsg = 'File size too large. Maximum 10MB allowed.';
                        if (xhr.status === 401) {
                            errorMsg = 'Session expired. Redirecting to login...';
                            setTimeout(() => window.location.href = 'login.php', 2000);
                        }
                    } catch (e) {
                        console.error('Error parsing error response:', e);
                    }
                    handleSubmissionError(errorMsg);
                },
                complete: function() {
                    $progress.remove();
                }
            });

            function handleSubmissionError(message) {
                toastr.error(message);
                $submitBtn.prop('disabled', false)
                         .html('<i class="anticon anticon-upload"></i> Submit for Verification');
            }
        });
    }

    // Modern KYC details viewer with caching
    const kycDetailCache = {};
    $(document).on('click', '.view-kyc', function() {
        const kycId = $(this).data('id');
        const $modal = $('#kycDetailsModal');
        const $content = $('#kycDetailsContent');
        
        // Use cached content if available
        if (kycDetailCache[kycId]) {
            showKycDetails(kycDetailCache[kycId]);
            $modal.modal('show');
            return;
        }

        // Set loading state with modern spinner
        $content.html(`
            <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);
        
        $modal.modal('show');

        // Fetch KYC details with timeout
        const fetchTimer = setTimeout(() => {
            $content.find('.spinner-border').after(
                '<div class="text-muted mt-2">Taking longer than expected...</div>'
            );
        }, 3000);

        $.ajax({
            url: 'ajax/get-kyc.php',
            type: 'GET',
            data: { id: kycId },
            dataType: 'json',
            success: function(response) {
                clearTimeout(fetchTimer);
                if (response.success) {
                    kycDetailCache[kycId] = response; // Cache the response
                    showKycDetails(response);
                } else {
                    showError(response.message || 'Failed to load KYC details');
                }
            },
            error: function(xhr) {
                clearTimeout(fetchTimer);
                showError(
                    xhr.status === 401 ? 'Session expired. Please login again.' :
                    'Failed to load KYC details. Please try again.'
                );
            }
        });

        function showKycDetails(response) {
            const kyc = response.kyc;
            const statusClass = {
                'approved': 'success',
                'rejected': 'danger',
                'pending': 'warning'
            }[kyc.status] || 'secondary';
            
            let content = `
                <div class="modal-header">
                    <h5 class="modal-title">KYC Verification #${kyc.id}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="kyc-info-item">
                                <span class="info-label">Document Type:</span>
                                <span class="info-value">${kyc.document_type.replace(/_/g, ' ')}</span>
                            </div>
                            <div class="kyc-info-item">
                                <span class="info-label">Status:</span>
                                <span class="badge badge-${statusClass}">
                                    ${kyc.status.charAt(0).toUpperCase() + kyc.status.slice(1)}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="kyc-info-item">
                                <span class="info-label">Submitted:</span>
                                <span class="info-value">${new Date(kyc.created_at).toLocaleString()}</span>
                            </div>
                            ${kyc.verified_at ? `
                            <div class="kyc-info-item">
                                <span class="info-label">Verified:</span>
                                <span class="info-value">${new Date(kyc.verified_at).toLocaleString()}</span>
                            </div>` : ''}
                        </div>
                    </div>
                    
                    ${kyc.rejection_reason ? `
                    <div class="alert alert-danger">
                        <strong>Rejection Reason:</strong> ${kyc.rejection_reason}
                    </div>` : ''}
                    
                    <div class="documents-grid">
            `;
            
            // Document viewer component
            const addDocument = (title, path) => {
                if (!path) return '';
                const ext = path.split('.').pop().toLowerCase();
                const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(ext);
                
                return `
                    <div class="document-card">
                        <h6>${title}</h6>
                        <div class="document-preview">
                            ${isImage ? 
                                `<img src="${path}" class="img-fluid" alt="${title}" data-zoomable>` : 
                                `<div class="document-icon">
                                    <i class="far fa-file-${ext === 'pdf' ? 'pdf' : 'alt'}"></i>
                                </div>`
                            }
                        </div>
                        <div class="document-actions">
                            <a href="${path}" target="_blank" class="btn btn-sm btn-primary" download>
                                <i class="fas fa-download"></i> Download
                            </a>
                            ${isImage ? `
                            <button class="btn btn-sm btn-secondary zoom-btn" data-img="${path}">
                                <i class="fas fa-search-plus"></i> Zoom
                            </button>` : ''}
                        </div>
                    </div>
                `;
            };
            
            content += addDocument('Document Front', kyc.document_front);
            content += addDocument('Document Back', kyc.document_back);
            content += addDocument('Selfie with Document', kyc.selfie_with_id);
            content += addDocument('Proof of Address', kyc.address_proof);
            
            content += `
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            `;
            
            $content.html(content);
            initDocumentViewer();
        }
        
        function showError(message) {
            $content.html(`
                <div class="alert alert-danger">
                    ${message}
                    <button type="button" class="btn btn-link retry-btn">Retry</button>
                </div>
            `).find('.retry-btn').click(function() {
                $(this).closest('.view-kyc').trigger('click');
            });
        }
        
        function initDocumentViewer() {
            // Initialize zoom functionality
            $('[data-zoomable]').click(function() {
                const src = $(this).attr('src');
                $('#imageZoomModal img').attr('src', src);
                $('#imageZoomModal').modal('show');
            });
            
            $('.zoom-btn').click(function() {
                const imgSrc = $(this).data('img');
                $('#imageZoomModal img').attr('src', imgSrc);
                $('#imageZoomModal').modal('show');
            });
        }
    });

    // Modern KYC table with server-side processing
    if ($('#kycTable').length) {
        const kycTable = $('#kycTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: 'ajax/kyc-list.php',
                type: 'POST',
                data: function(d) {
                    d.csrf_token = $('meta[name="csrf-token"]').attr('content');
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        toastr.error('Session expired. Redirecting to login...');
                        setTimeout(() => window.location.href = 'login.php', 1500);
                    } else {
                        toastr.error('Failed to load KYC records');
                    }
                }
            },
            columns: [
                { 
                    data: 'created_at',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString() : '-';
                    }
                },
                { 
                    data: 'document_type',
                    render: function(data) {
                        return data ? data.replace(/_/g, ' ') : '-';
                    }
                },
                { 
                    data: 'status',
                    render: function(data, type, row) {
                        const statusClass = {
                            'approved': 'success',
                            'rejected': 'danger',
                            'pending': 'warning'
                        }[data] || 'secondary';
                        
                        return `<span class="badge badge-${statusClass}">
                            ${data.charAt(0).toUpperCase() + data.slice(1)}
                        </span>`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-outline-primary view-kyc" data-id="${row.id}">
                                <i class="anticon anticon-eye"></i> Details
                            </button>
                        `;
                    },
                    orderable: false
                }
            ],
            order: [[0, 'desc']],
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
                emptyTable: "No KYC records found",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                lengthMenu: "Show _MENU_ entries"
            },
            dom: '<"top"<"row"<"col-md-6"f><"col-md-6"B>>>rt<"bottom"<"row"<"col-md-6"i><"col-md-6"p>>><"clear">',
            buttons: [
                {
                    extend: 'refresh',
                    text: '<i class="anticon anticon-reload"></i> Refresh',
                    className: 'btn btn-primary',
                    action: function(e, dt, node, config) {
                        dt.ajax.reload(null, false);
                        toastr.info('KYC records refreshed');
                    }
                },
                {
                    extend: 'excel',
                    text: '<i class="anticon anticon-file-excel"></i> Export',
                    className: 'btn btn-success',
                    title: 'KYC_Records'
                }
            ],
            initComplete: function() {
                // Add modern filter controls
                this.api().columns([1]).every(function() {
                    const column = this;
                    const select = $('<select class="form-control form-control-sm"><option value="">All Document Types</option></select>')
                        .appendTo($(column.header()).empty())
                        .on('change', function() {
                            column.search($(this).val()).draw();
                        });
                    
                    column.data().unique().sort().each(function(d) {
                        select.append(`<option value="${d}">${d.replace(/_/g, ' ')}</option>`);
                    });
                });
            }
        });

        // Add modern search delay
        let searchTimeout;
        $('.dataTables_filter input').unbind().bind('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                kycTable.search($(this).val()).draw();
            }, 500);
        });
    }
});""",

    "deposits.js": """// Deposits Table Implementation
$(document).ready(function() {
    if ($('#depositsTable').length) {
        // Initialize deposits table
        const initDepositsTable = function() {
            return $('#depositsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "ajax/deposit.php",
                    type: "POST",
                    data: function(d) {
                        d.csrf_token = $('meta[name="csrf-token"]').attr('content');
                    },
                    error: function(xhr, error, thrown) {
                        console.error('AJAX Error:', xhr.responseText);
                        toastr.error('Failed to load deposits. Please try again.');
                    }
                },
                columns: [
                    { data: "type" },
                    { 
                        data: "amount",
                        render: function(data) {
                            return data ? '$' + parseFloat(data).toFixed(2) : '$0.00';
                        }
                    },
                    { 
                        data: "method",
                        render: function(data) {
                            return data || 'N/A';
                        }
                    },
                    { 
                        data: "status",
                        render: function(data) {
                            if (!data) return '';
                            const statusClass = {
                                'pending': 'warning',
                                'completed': 'success',
                                'failed': 'danger'
                            }[data.toLowerCase()] || 'info';
                            return `<span class="badge badge-${statusClass}">${data}</span>`;
                        }
                    },
                    { 
                        data: "created_at",
                        render: function(data) {
                            return data ? new Date(data).toLocaleString() : '';
                        }
                    }
                ],
                responsive: true,
                order: [[4, 'desc']],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
                    emptyTable: "No deposits found",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    lengthMenu: "Show _MENU_ entries"
                }
            });
        };

        let depositsTable = initDepositsTable();

        // Payment method details display
        $('#paymentMethod').change(function() {
            const details = $(this).find(':selected').data('details');
            $('#paymentDetails').html(details || '<p class="text-muted">Select a payment method to view details</p>');
        });

        // Deposit form submission
        $('#depositForm').submit(function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const $submitBtn = $(this).find('button[type="submit"]');
            
            $submitBtn.prop('disabled', true)
                .html('<i class="anticon anticon-loading anticon-spin"></i> Processing...');
            
            $.ajax({
                url: 'ajax/process-deposit.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else if (response.success) {
                        toastr.success(response.message);
                        $('#newDepositModal').modal('hide');
                        depositsTable.ajax.reload();
                        $('#depositForm')[0].reset();
                        $('#paymentDetails').html('');
                    } else {
                        toastr.error(response.message);
                    }
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).html('Submit Deposit');
                },
                error: function(xhr) {
                    toastr.error(xhr.status === 401 ? 'Session expired. Please login again.' : 'Failed to process deposit.');
                }
            });
        });
    }
});""",

    "cases.js": """// Cases Table Implementation
$(document).ready(function() {
    if ($('#casesTable').length) {
        const statusMap = {
            'open': { class: 'info', text: 'Open' },
            'documents_required': { class: 'warning', text: 'Documents Required' },
            'under_review': { class: 'primary', text: 'Under Review' },
            'in_progress': { class: 'primary', text: 'In Progress' },
            'resolved': { class: 'success', text: 'Resolved' },
            'completed': { class: 'success', text: 'Completed' },
            'closed': { class: 'secondary', text: 'Closed' },
            'rejected': { class: 'danger', text: 'Rejected' },
            'pending': { class: 'warning', text: 'Pending' },
            'failed': { class: 'danger', text: 'Failed' }
        };

        const casesTable = $('#casesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "ajax/cases.php",
                type: "POST",
                dataSrc: function(json) {
                    if (json.redirect) {
                        window.location.href = json.redirect;
                        return [];
                    }
                    return json.data || [];
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        toastr.error('Session expired. Redirecting...');
                        setTimeout(() => window.location.href = 'login.php', 1500);
                    }
                }
            },
            columns: [
                { data: "id" },
                { 
                    data: "reported_amount",
                    render: function(data) {
                        return data ? '$' + parseFloat(data).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') : '$0.00';
                    }
                },
                { 
                    data: "recovered_amount",
                    render: function(data) {
                        return data ? '$' + parseFloat(data).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') : '-';
                    }
                },
                { 
                    data: "status",
                    render: function(data) {
                        const status = (data || '').toLowerCase();
                        const statusInfo = statusMap[status] || { class: 'light', text: status };
                        return `<span class="badge badge-${statusInfo.class}">${statusInfo.text}</span>`;
                    }
                },
                { 
                    data: "created_at",
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('en-US') : '-';
                    }
                },
                { 
                    data: "updated_at",
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('en-US') : '-';
                    }
                },
                {
                    data: "id",
                    render: function(data) {
                        return `<button class="btn btn-sm btn-primary view-case" data-id="${data}">
                               <i class="anticon anticon-eye"></i> View
                               </button>`;
                    },
                    orderable: false
                }
            ],
            responsive: true,
            order: [[4, 'desc']],
            language: {
                emptyTable: "No cases found",
                processing: "<i class='fa fa-spinner fa-spin'></i> Loading cases...",
                search: "_INPUT_",
                searchPlaceholder: "Search cases...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });

        $('#casesTable').on('click', '.view-case', function() {
            const caseId = $(this).data('id');
            const $modal = $('#caseModal');
            const $modalContent = $('#caseModalContent');
            
            $modalContent.html(`<div class="text-center p-4">
                <i class="anticon anticon-loading anticon-spin" style="font-size:24px"></i>
                <p>Loading case details...</p>
            </div>`);
            
            $modal.modal('show');
            
            $.get('ajax/get-case.php', { id: caseId })
                .done(function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else if (response.success) {
                        const caseData = response.case;
                        $modalContent.html(`
                            <div class="modal-header">
                                <h5 class="modal-title">Case #${caseData.case_number}</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <i class="anticon anticon-close"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>Status:</strong> <span class="badge badge-${getStatusClass(caseData.status)}">${caseData.status_display}</span></p>
                                        <p><strong>Reported Amount:</strong> ${caseData.reported_amount}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Recovered Amount:</strong> ${caseData.recovered_amount}</p>
                                        <p><strong>Created:</strong> ${caseData.created_at}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        `);
                    } else {
                        $modalContent.html(`<div class="alert alert-danger">${response.message || 'Failed to load case details'}</div>`);
                    }
                })
                .fail(function(xhr) {
                    const errorMsg = xhr.status === 401 
                        ? 'Session expired. Please <a href="login.php">login</a> again.' 
                        : 'Failed to load case details. Please try again.';
                    $modalContent.html(`<div class="alert alert-danger">${errorMsg}</div>`);
                });
        });

        $('.refresh-btn').click(function() {
            casesTable.ajax.reload(null, false);
            toastr.info('Cases table refreshed');
        });

        function getStatusClass(status) {
            const statusLower = (status || '').toLowerCase().replace(' ', '_');
            return statusMap[statusLower]?.class || 'secondary';
        }
    }
});"""
}

# Create the JavaScript files
for filename, content in js_files.items():
    with open(os.path.join(js_dir, filename), 'w') as f:
        f.write(content)

# Generate the new footer.php content
new_footer_content = """<!-- Footer START -->
<footer class="footer">
    <div class="footer-content">
        <p class="m-b-0">Copyright © <?php echo date('Y'); ?> Scam Recovery. All rights reserved.</p>
        <span>
            <a href="terms.php" class="text-gray m-r-15">Term &amp; Conditions</a>
            <a href="privacy.php" class="text-gray">Privacy &amp; Policy</a>
        </span>
    </div>
</footer>
<!-- Footer END -->

</div>
<!-- Page Container END -->

<!-- Core Vendors JS -->
<script src="assets/js/vendors.min.js"></script>

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.11.3/datatables.min.js"></script>

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Page JS -->
<script src="assets/js/pages/dashboard-default.js"></script>

<!-- Core JS -->
<script src="assets/js/app.min.js"></script>

<!-- Custom JS Files -->
<script src="assets/js/config.js"></script>
<script src="assets/js/sidebar.js"></script>
<script src="assets/js/charts.js"></script>
<script src="assets/js/documents.js"></script>
<script src="assets/js/payment-methods.js"></script>
<script src="assets/js/transactions.js"></script>
<script src="assets/js/withdrawals.js"></script>
<script src="assets/js/kyc.js"></script>
<script src="assets/js/deposits.js"></script>
<script src="assets/js/cases.js"></script>

</body>
</html>"""

# Write the new footer.php
with open('footer.php', 'w') as f:
    f.write(new_footer_content)

print("JavaScript files have been created in assets/js/ and footer.php has been updated.")