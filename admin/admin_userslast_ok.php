<?php
// admin_users.php
// === ENABLE PHP ERRORS (TEMPORARILY) ===
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'admin_header.php';
?>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">User Management</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="admin_dashboard.php" class="breadcrumb-item"><i class="anticon anticon-home"></i> Dashboard</a>
                <span class="breadcrumb-item active">Users</span>
            </nav>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5>User List</h5>
                <button class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
                    <i class="anticon anticon-plus"></i> Add User
                </button>
            </div>
            
            <div class="m-t-25">
                <table id="usersTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Balance</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 🔹 User Details Modal (Tabs for each related section) -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" role="dialog" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="anticon anticon-user"></i> User Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" id="userDetailsTabs" role="tablist">
          <li class="nav-item"><a class="nav-link active" id="tab-basic" data-toggle="tab" href="#basicInfo" role="tab">Basic Info</a></li>
          <li class="nav-item"><a class="nav-link" id="tab-onboarding" data-toggle="tab" href="#onboarding" role="tab">Onboarding</a></li>
          <li class="nav-item"><a class="nav-link" id="tab-kyc" data-toggle="tab" href="#kyc" role="tab">KYC</a></li>
          <li class="nav-item"><a class="nav-link" id="tab-payments" data-toggle="tab" href="#payments" role="tab">Payments</a></li>
          <li class="nav-item"><a class="nav-link" id="tab-transactions" data-toggle="tab" href="#transactions" role="tab">Transactions</a></li>
          <li class="nav-item"><a class="nav-link" id="tab-cases" data-toggle="tab" href="#cases" role="tab">Cases</a></li>
          <li class="nav-item"><a class="nav-link" id="tab-tickets" data-toggle="tab" href="#tickets" role="tab">Tickets</a></li>
        </ul>

        <div class="tab-content mt-3" id="userDetailsContent">
          <div class="tab-pane fade show active" id="basicInfo" role="tabpanel"><div class="text-center p-3 text-muted">Loading...</div></div>
          <div class="tab-pane fade" id="onboarding" role="tabpanel"><div class="text-center p-3 text-muted">Loading...</div></div>
          <div class="tab-pane fade" id="kyc" role="tabpanel"><div class="text-center p-3 text-muted">Loading...</div></div>
          <div class="tab-pane fade" id="payments" role="tabpanel"><div class="text-center p-3 text-muted">Loading...</div></div>
          <div class="tab-pane fade" id="transactions" role="tabpanel"><div class="text-center p-3 text-muted">Loading...</div></div>
          <div class="tab-pane fade" id="cases" role="tabpanel"><div class="text-center p-3 text-muted">Loading...</div></div>
          <div class="tab-pane fade" id="tickets" role="tabpanel"><div class="text-center p-3 text-muted">Loading...</div></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New User</h5>
        <button type="button" class="close" data-dismiss="modal"><i class="anticon anticon-close"></i></button>
      </div>
      <form id="addUserForm">
        <div class="modal-body">
          <div class="form-group"><label>First Name</label><input type="text" class="form-control" name="first_name" required></div>
          <div class="form-group"><label>Last Name</label><input type="text" class="form-control" name="last_name" required></div>
          <div class="form-group"><label>Email</label><input type="email" class="form-control" name="email" required></div>
          <div class="form-group"><label>Password</label><input type="text" class="form-control" name="password" value="ceM8fFXV" required></div>
          <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status">
              <option value="active">Active</option>
              <option value="suspended">Suspended</option>
              <option value="banned">Banned</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Add User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Send Email Modal -->
<div class="modal fade" id="sendMailModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Send Email to User</h5>
        <button type="button" class="close" data-dismiss="modal"><i class="anticon anticon-close"></i></button>
      </div>
      <form id="sendMailForm">
        <div class="modal-body">
          <input type="hidden" name="user_id" id="send_mail_user_id">
          <div class="form-group">
            <label>Recipient</label>
            <input type="text" class="form-control" id="send_mail_recipient" readonly>
          </div>
          <div class="form-group">
            <label>Email Template</label>
            <select class="form-control" name="template_id" id="send_mail_template_select">
              <option value="">Custom Email (No Template)</option>
            </select>
            <small class="form-text text-muted">Select a template or compose a custom email</small>
          </div>
          <div class="form-group">
            <label>Subject</label>
            <input type="text" class="form-control" name="subject" id="send_mail_subject" required>
          </div>
          <div class="form-group">
            <label>Message</label>
            <textarea class="form-control" name="content" id="send_mail_content" rows="8" required></textarea>
            <small class="form-text text-muted">Write your message here. HTML formatting will be applied automatically. You can use variables: {first_name}, {last_name}, {email}, {user_id}, {balance}, {site_url}, etc.</small>
          </div>
          <input type="hidden" name="use_html_wrapper" id="use_html_wrapper" value="1">
          <div id="send_mail_variables" class="alert alert-info" style="display:none;">
            <strong>Available Variables:</strong> <span id="variable_list"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">
            <i class="anticon anticon-send"></i> Send Email
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once 'admin_footer.php'; ?>

<script>
// Utility functions
window.escapeHtml = function(str) {
    return String(str).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]);
};

window.decodeHtml = function(html) {
    const txt = document.createElement('textarea');
    txt.innerHTML = html;
    return txt.value;
};

$(document).ready(function() {

    // Initialize DataTable
    const usersTable = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: 'admin_ajax/get_users.php', type: 'POST' },
order: [[0,'desc']],
        columns: [

            { data: 'id' },
            { data: null, render: data => data.first_name + ' ' + data.last_name },
            { data: 'email' },
            { 
                data: 'status',
                render: data => {
                    const cls = {active:'success', suspended:'warning', banned:'danger'}[data];
                    return `<span class="badge badge-${cls}">${data}</span>`;
                }
            },
            { data: 'balance', render: d => '$' + parseFloat(d).toFixed(2) },
            { data: 'created_at', render: d => new Date(d).toLocaleDateString() },
            {
                data: null,
                render: function(data, type, row) {
                    const email = window.escapeHtml(data.email);
                    const name = window.escapeHtml(data.first_name + ' ' + data.last_name);
                    return `
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info view-user" data-id="${data.id}" title="View Details">
                            <i class="anticon anticon-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-primary edit-user" data-id="${data.id}" title="Edit User">
                            <i class="anticon anticon-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-success send-mail-user" data-id="${data.id}" data-email="${email}" data-name="${name}" title="Send Email">
                            <i class="anticon anticon-mail"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-user" data-id="${data.id}" title="Delete User">
                            <i class="anticon anticon-delete"></i>
                        </button>
                    </div>`;
                }
            }
        ]
    });

    // 🧠 View User Details
    $('#usersTable').on('click', '.view-user', function() {
        const userId = $(this).data('id');
        $('#userDetailsModal').modal('show');
        // clear & show loading placeholders
        $('#userDetailsContent .tab-pane').html('<div class="text-center p-3 text-muted"><i class="anticon anticon-loading anticon-spin"></i> Loading...</div>');

        $.ajax({
            url: 'admin_ajax/get_user.php',
            method: 'GET',
            data: { id: userId },
            dataType: 'json',
            success: function(res) {
                console.log('Modal data:', res);

                if (!res.success) {
                    $('#basicInfo').html(`<div class="alert alert-warning">${res.message || 'No data found'}</div>`);
                    return;
                }

                // Delay render slightly to ensure modal DOM is ready
                setTimeout(() => {
                    $('#basicInfo').html(res.html.basic);
                    $('#onboarding').html(res.html.onboarding);
                    $('#kyc').html(res.html.kyc);
                    $('#payments').html(res.html.payments);
                    $('#transactions').html(res.html.transactions);
                    $('#cases').html(res.html.cases);
                    $('#tickets').html(res.html.tickets);
                }, 100);
            },
            error: function(xhr) {
                console.error('Error response:', xhr.responseText);
                $('#basicInfo').html('<div class="alert alert-danger">Error loading user details.</div>');
            }
        });
    });

    // 🟢 Add User
    $('#addUserForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: 'admin_ajax/add_user.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend:()=>$('#addUserForm button[type="submit"]').prop('disabled',true).html('<i class="anticon anticon-loading anticon-spin"></i> Adding...'),
            success: res=>{
                if(res.success){
                    toastr.success(res.message);
                    $('#addUserModal').modal('hide');
                    usersTable.ajax.reload();
                } else {
                    toastr.error(res.message);
                }
            },
            complete:()=>$('#addUserForm button[type="submit"]').prop('disabled',false).html('Add User')
        });
    });

    // 📧 Send Mail to User
    $('#usersTable').on('click', '.send-mail-user', function() {
        const userId = $(this).data('id');
        const userEmail = $(this).data('email');
        const userName = $(this).data('name');
        
        $('#send_mail_user_id').val(userId);
        $('#send_mail_recipient').val(`${window.decodeHtml(userName)} <${window.decodeHtml(userEmail)}>`);
        $('#send_mail_subject').val('');
        $('#send_mail_content').val('');
        $('#send_mail_variables').hide();
        
        // Load email templates if not already loaded
        if ($('#send_mail_template_select option').length === 1) {
            $.ajax({
                url: 'admin_ajax/get_email_templates.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        let options = '<option value="">Custom Email (No Template)</option>';
                        response.data.forEach(template => {
                            const escapedKey = window.escapeHtml(template.template_key);
                            const escapedSubject = window.escapeHtml(template.subject);
                            // Store template data in a separate object instead of data attributes to avoid escaping issues
                            options += `<option value="${template.id}">${escapedKey} - ${escapedSubject}</option>`;
                        });
                        $('#send_mail_template_select').html(options);
                        // Store templates in a global variable for later access
                        window.emailTemplatesData = response.data;
                    }
                }
            });
        }
        
        $('#sendMailModal').modal('show');
    });
    
    // Template selection handler
    $('#send_mail_template_select').change(function() {
        const templateId = $(this).val();
        
        if (templateId && window.emailTemplatesData) {
            // Find the template in the stored data
            const template = window.emailTemplatesData.find(t => t.id == templateId);
            
            if (template) {
                $('#send_mail_subject').val(template.subject);
                $('#send_mail_content').val(template.content);
                $('#use_html_wrapper').val('0'); // Don't wrap template content
                
                if (template.variables) {
                    try {
                        // Safely parse JSON with validation
                        const varList = typeof template.variables === 'string' && template.variables.trim().startsWith('[') 
                            ? JSON.parse(template.variables) 
                            : template.variables;
                        const varText = Array.isArray(varList) 
                            ? varList.map(v => `{${v}}`).join(', ') 
                            : template.variables;
                        $('#variable_list').text(varText);
                        $('#send_mail_variables').show();
                    } catch(e) {
                        console.warn('Failed to parse template variables:', e);
                        $('#send_mail_variables').hide();
                    }
                } else {
                    $('#send_mail_variables').hide();
                }
            }
        } else {
            // Custom email selected - just show empty message field
            // HTML wrapper will be applied automatically on the backend
            $('#send_mail_subject').val('');
            $('#send_mail_content').val('');
            $('#use_html_wrapper').val('1');
            $('#send_mail_variables').show();
            $('#variable_list').text('{first_name}, {last_name}, {email}, {user_id}, {balance}, {status}, {site_url}, {site_name}, {contact_email}');
        }
    });
    
    // Send Mail Form Submission
    $('#sendMailForm').submit(function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to send this email?')) {
            return;
        }
        
        $.ajax({
            url: 'admin_ajax/send_user_email.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                $('#sendMailForm button[type="submit"]').prop('disabled', true)
                    .html('<i class="anticon anticon-loading anticon-spin"></i> Sending...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#sendMailModal').modal('hide');
                    $('#sendMailForm')[0].reset();
                } else {
                    toastr.error(response.message || 'Failed to send email');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                toastr.error('Failed to send email. Please check console for details.');
            },
            complete: function() {
                $('#sendMailForm button[type="submit"]').prop('disabled', false)
                    .html('<i class="anticon anticon-send"></i> Send Email');
            }
        });
    });
});
</script>

