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
            <label>Subject</label>
            <input type="text" class="form-control" name="subject" id="send_mail_subject" placeholder="Enter email subject" required>
          </div>
          <div class="form-group">
            <label>Message</label>
            <textarea class="form-control" name="message" id="send_mail_content" rows="8" placeholder="Enter your message here. It will be wrapped in a professional HTML template automatically." required></textarea>
            <small class="form-text text-muted">
              <strong>Variables available:</strong> {first_name}, {last_name}, {email}, {user_id}, {balance}, {status}, {site_url}, {site_name}, {contact_email}
            </small>
          </div>
          <div class="alert alert-info">
            <i class="anticon anticon-info-circle"></i> Your message will be automatically wrapped in the professional KryptoX HTML email template with gradient header, signature, and footer.
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

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="close" data-dismiss="modal"><i class="anticon anticon-close"></i></button>
      </div>
      <form id="editUserForm">
        <div class="modal-body">
          <input type="hidden" name="id" id="edit_user_id">
          <div class="form-group">
            <label>First Name</label>
            <input type="text" class="form-control" name="first_name" id="edit_first_name" required>
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input type="text" class="form-control" name="last_name" id="edit_last_name" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" id="edit_email" required>
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input type="text" class="form-control" name="phone" id="edit_phone">
          </div>
          <div class="form-group">
            <label>Country</label>
            <input type="text" class="form-control" name="country" id="edit_country">
          </div>
          <div class="form-group">
            <label>Balance</label>
            <input type="number" class="form-control" name="balance" id="edit_balance" step="0.01">
          </div>
          <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status" id="edit_status">
              <option value="active">Active</option>
              <option value="suspended">Suspended</option>
              <option value="banned">Banned</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update User</button>
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

    // ✏️ Edit User
    $('#usersTable').on('click', '.edit-user', function() {
        const userId = $(this).data('id');
        
        // Fetch user data
        $.ajax({
            url: 'admin_ajax/get_user.php',
            method: 'GET',
            data: { id: userId },
            dataType: 'json',
            success: function(res) {
                if (res.success && res.user) {
                    const user = res.user;
                    $('#edit_user_id').val(user.id);
                    $('#edit_first_name').val(user.first_name);
                    $('#edit_last_name').val(user.last_name);
                    $('#edit_email').val(user.email);
                    $('#edit_phone').val(user.phone || '');
                    $('#edit_country').val(user.country || '');
                    $('#edit_balance').val(user.balance || '0');
                    $('#edit_status').val(user.status);
                    
                    $('#editUserModal').modal('show');
                } else {
                    toastr.error('Failed to load user data');
                }
            },
            error: function() {
                toastr.error('Failed to load user data');
            }
        });
    });
    
    // Submit Edit User Form
    $('#editUserForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'admin_ajax/update_user.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                $('#editUserForm button[type="submit"]').prop('disabled', true)
                    .html('<i class="anticon anticon-loading anticon-spin"></i> Updating...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#editUserModal').modal('hide');
                    usersTable.ajax.reload();
                } else {
                    toastr.error(response.message || 'Failed to update user');
                }
            },
            error: function() {
                toastr.error('Failed to update user');
            },
            complete: function() {
                $('#editUserForm button[type="submit"]').prop('disabled', false)
                    .html('Update User');
            }
        });
    });
    
    // 🗑️ Delete User (Suspend)
    $('#usersTable').on('click', '.delete-user', function() {
        const userId = $(this).data('id');
        
        if (!confirm('Are you sure you want to suspend this user? (Note: User will be hidden from list but not deleted from database)')) {
            return;
        }
        
        $.ajax({
            url: 'admin_ajax/delete_user.php',
            type: 'POST',
            data: { id: userId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    usersTable.ajax.reload();
                } else {
                    toastr.error(response.message || 'Failed to suspend user');
                }
            },
            error: function() {
                toastr.error('Failed to suspend user');
            }
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
        
        $('#sendMailModal').modal('show');
    });
    
    // Send Mail Form Submission - Uses Universal Email Sender
    $('#sendMailForm').submit(function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to send this email?')) {
            return;
        }
        
        $.ajax({
            url: 'admin_ajax/send_universal_email.php',
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

