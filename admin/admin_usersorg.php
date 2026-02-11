<?php
// admin_users.php
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
                    <tbody>
                        <!-- AJAX will populate this -->
                    </tbody>
                </table>
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
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="text" value="ceM8fFXV" class="form-control" name="password" required>
                    </div>
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

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <form id="editUserForm">
                <input type="hidden" name="user_id" id="editUserId">
                <div class="modal-body">
                    <!-- Form fields populated by AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    const usersTable = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'admin_ajax/get_users.php',
            type: 'POST'
        },
        columns: [
            { data: 'id' },
            { 
                data: null,
                render: function(data) {
                    return data.first_name + ' ' + data.last_name;
                }
            },
            { data: 'email' },
            { 
                data: 'status',
                render: function(data) {
                    const statusClass = {
                        active: 'success',
                        suspended: 'warning',
                        banned: 'danger'
                    }[data];
                    return `<span class="badge badge-${statusClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            { 
                data: 'balance',
                render: function(data) {
                    return '$' + parseFloat(data).toFixed(2);
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
                render: function(data) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary edit-user" data-id="${data}">
                                <i class="anticon anticon-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-user" data-id="${data}">
                                <i class="anticon anticon-delete"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // Add User Form Submission
    $('#addUserForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: 'admin_ajax/add_user.php',
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#addUserForm button[type="submit"]').prop('disabled', true)
                    .html('<i class="anticon anticon-loading anticon-spin"></i> Adding...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#addUserModal').modal('hide');
                    usersTable.ajax.reload();
                    $('#addUserForm')[0].reset();
                } else {
                    toastr.error(response.message);
                }
            },
            complete: function() {
                $('#addUserForm button[type="submit"]').prop('disabled', false).html('Add User');
            }
        });
    });

// Edit User - Load Data
$('#usersTable').on('click', '.edit-user', function() {
    const userId = $(this).data('id');
    $.ajax({
        url: 'admin_ajax/get_user.php',
        type: 'GET',
        data: { id: userId },
        success: function(response) {
            if (response.success) {
                const user = response.user.basic; // Note the change here to access .basic
                $('#editUserId').val(user.id);
                
                // Build form HTML
                const formHtml = `
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="first_name" value="${user.first_name}" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="last_name" value="${user.last_name}" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="${user.email}" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="active" ${user.status === 'active' ? 'selected' : ''}>Active</option>
                            <option value="suspended" ${user.status === 'suspended' ? 'selected' : ''}>Suspended</option>
                            <option value="banned" ${user.status === 'banned' ? 'selected' : ''}>Banned</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Balance</label>
                        <input type="number" step="0.01" class="form-control" name="balance" value="${user.balance}" required>
                    </div>
                `;
                
                $('#editUserForm .modal-body').html(formHtml);
                $('#editUserModal').modal('show');
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            toastr.error("Failed to load user data");
        }
    });
});

    // Edit User Form Submission
    $('#editUserForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: 'admin_ajax/update_user.php',
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#editUserForm button[type="submit"]').prop('disabled', true)
                    .html('<i class="anticon anticon-loading anticon-spin"></i> Saving...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#editUserModal').modal('hide');
                    usersTable.ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            complete: function() {
                $('#editUserForm button[type="submit"]').prop('disabled', false).html('Save Changes');
            }
        });
    });

    // Delete User
    $('#usersTable').on('click', '.delete-user', function() {
        const userId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            $.ajax({
                url: 'admin_ajax/delete_user.php',
                type: 'POST',
                data: { id: userId },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        usersTable.ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });
});
</script>