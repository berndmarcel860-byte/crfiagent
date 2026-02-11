#!/usr/bin/env python3
"""
Admin Panel File Generator for Scam Recovery System
Creates missing admin panel files based on sidebar configuration and database schema

Generated on: 2025-08-20 21:04:12
Current User: iload1731a
"""

import os
import re
from datetime import datetime

# Current UTC timestamp
CURRENT_TIMESTAMP = "2025-08-20 21:04:12"
CURRENT_USER = "iload1731a"

# Current files you have
existing_files = [
    'admin_audit_logs.php',
    'admin_cases.php', 
    'admin_dashboard.php',
    'admin_deposits.php',
    'admin_email_logs.php',
    'admin_emails.php',
    'admin_index.php',
    'admin_kyc.php',
    'admin_online_users.php',
    'admin_platforms.php',
    'admin_settings.php',
    'admin_transactions.php',
    'admin_user_activity.php',
    'admin_users.php',
    'admin_withdrawals.php'
]

# Files referenced in the sidebar with their database mappings
file_configs = {
    'admin_case_assignments.php': {
        'title': 'Case Assignments',
        'table': 'cases',
        'type': 'assignments',
        'primary_key': 'id',
        'columns': ['id', 'case_number', 'assigned_to', 'status', 'created_at']
    },
    'admin_email_templates.php': {
        'title': 'Email Templates', 
        'table': 'email_templates',
        'type': 'basic',
        'primary_key': 'id',
        'columns': ['id', 'template_key', 'subject', 'created_at', 'updated_at']
    },
    'admin_notifications.php': {
        'title': 'Admin Notifications',
        'table': 'admin_notifications', 
        'type': 'basic',
        'primary_key': 'id',
        'columns': ['id', 'title', 'message', 'type', 'is_read', 'created_at']
    },
    'admin_smtp_settings.php': {
        'title': 'SMTP Settings',
        'table': 'smtp_settings',
        'type': 'settings',
        'settings_type': 'smtp',
        'primary_key': 'id',
        'columns': ['id', 'host', 'port', 'encryption', 'username', 'from_email', 'is_active']
    },
    'admin_support_tickets.php': {
        'title': 'Support Tickets',
        'table': 'support_tickets',
        'type': 'tickets',
        'primary_key': 'id',
        'columns': ['id', 'ticket_number', 'subject', 'status', 'priority', 'created_at']
    },
    'admin_admins.php': {
        'title': 'Manage Admins',
        'table': 'admins',
        'type': 'basic',
        'primary_key': 'id',
        'columns': ['id', 'email', 'first_name', 'last_name', 'role', 'status', 'created_at']
    },
    'admin_login_logs.php': {
        'title': 'Admin Login Logs',
        'table': 'admin_login_logs',
        'type': 'logs',
        'primary_key': 'id',
        'columns': ['id', 'email', 'ip_address', 'success', 'attempted_at']
    },
    'admin_payment_methods.php': {
        'title': 'Payment Methods',
        'table': 'payment_methods',
        'type': 'basic',
        'primary_key': 'id',
        'columns': ['id', 'method_code', 'method_name', 'is_active', 'allows_deposit', 'allows_withdrawal']
    },
    'admin_documents.php': {
        'title': 'User Documents',
        'table': 'documents',
        'type': 'documents',
        'primary_key': 'id',
        'columns': ['id', 'document_name', 'document_type', 'file_path', 'uploaded_at']
    },
    'admin_system_info.php': {
        'title': 'System Information',
        'type': 'info'
    },
    'admin_backup.php': {
        'title': 'Database Backup & Restore',
        'type': 'backup'
    },
    'admin_reports.php': {
        'title': 'System Reports',
        'type': 'reports'
    },
    'admin_analytics.php': {
        'title': 'Analytics Dashboard',
        'type': 'analytics'
    },
    'admin_statistics.php': {
        'title': 'System Statistics',
        'type': 'statistics'
    }
}

def get_basic_admin_template(config, page_name):
    """Generate basic admin page template with proper database integration"""
    
    title = config['title']
    table = config.get('table', '')
    columns = config.get('columns', ['id', 'name', 'status', 'created_at'])
    
    # Generate DataTable columns configuration
    dt_columns = []
    for i, col in enumerate(columns):
        if col == 'id':
            dt_columns.append("{ data: 'id' }")
        elif 'name' in col or col == 'template_key' or col == 'method_name':
            dt_columns.append("{ data: '" + col + "' }")
        elif col == 'status' or col == 'is_active':
            dt_columns.append('''{{ 
                data: '{0}',
                render: function(data) {{
                    const statusClass = (data == 'active' || data == '1') ? 'success' : 'secondary';
                    const statusText = (data == '1' || data == 'active') ? 'Active' : 'Inactive';
                    return `<span class="badge badge-${{statusClass}}">${{statusText}}</span>`;
                }}
            }}'''.format(col))
        elif 'date' in col or 'at' in col:
            dt_columns.append('''{{ 
                data: '{0}',
                render: function(data) {{
                    return data ? new Date(data).toLocaleDateString() : 'N/A';
                }}
            }}'''.format(col))
        else:
            dt_columns.append("{{ data: '{0}' }}".format(col))
    
    # Add actions column
    dt_columns.append('''{ 
        data: 'id',
        render: function(data, type, row) {
            return `
                <div class="btn-group">
                    <button class="btn btn-sm btn-info view-item" 
                            data-id="${data}" 
                            title="View Details">
                        <i class="anticon anticon-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary edit-item" 
                            data-id="${data}" 
                            title="Edit">
                        <i class="anticon anticon-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-item" 
                            data-id="${data}" 
                            title="Delete">
                        <i class="anticon anticon-delete"></i>
                    </button>
                </div>
            `;
        }
    }''')
    
    dt_columns_str = ',\n            '.join(dt_columns)
    
    # Generate table headers
    headers = []
    for col in columns:
        if col == 'id':
            headers.append('<th>ID</th>')
        elif col in ['first_name', 'last_name']:
            headers.append('<th>Name</th>' if col == 'first_name' else '')
        elif col == 'template_key':
            headers.append('<th>Template</th>')
        elif col == 'method_name':
            headers.append('<th>Method Name</th>')
        elif col == 'method_code':
            headers.append('<th>Code</th>')
        elif col == 'is_active':
            headers.append('<th>Status</th>')
        elif 'created_at' in col:
            headers.append('<th>Created</th>')
        else:
            headers.append('<th>{0}</th>'.format(col.replace("_", " ").title()))
    
    headers.append('<th>Actions</th>')
    headers_str = '\n                            '.join([h for h in headers if h])

    template = '''<?php
require_once 'admin_header.php';

// Generated on: {timestamp} UTC by {user}
// Get additional data for dropdowns if needed
$additional_data = [];
?>

<div class="main-content">
    <div class="page-header">
        <h2>{title}</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="admin_dashboard.php" class="breadcrumb-item"><i class="anticon anticon-home"></i> Dashboard</a>
                <span class="breadcrumb-item active">{title}</span>
            </nav>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>{title}</h5>
                <div class="d-flex">
                    <button class="btn btn-info mr-2" id="refresh{page_name_title}">
                        <i class="anticon anticon-reload"></i> Refresh
                    </button>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#add{page_name_title}Modal">
                        <i class="anticon anticon-plus"></i> Add New
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="{page_name}Table" class="table table-hover">
                    <thead>
                        <tr>
                            {headers_str}
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="add{page_name_title}Modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New {title}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <form id="add{page_name_title}Form">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="edit{page_name_title}Modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit {title}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <form id="edit{page_name_title}Form">
                <input type="hidden" name="id" id="edit{page_name_title}Id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" id="edit{page_name_title}Name" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" id="edit{page_name_title}Status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

<script>
$(document).ready(function() {{
    // Initialize DataTable
    const {page_name}Table = $('#{page_name}Table').DataTable({{
        processing: true,
        serverSide: true,
        ajax: {{
            url: 'admin_ajax/get_{page_name}.php',
            type: 'POST'
        }},
        order: [[0, 'desc']],
        columns: [
            {dt_columns_str}
        ]
    }});
    
    // Add new item
    $('#add{page_name_title}Form').submit(function(e) {{
        e.preventDefault();
        const formData = $(this).serializeArray();
        const postData = {{}};
        $.each(formData, function(i, field) {{
            postData[field.name] = field.value;
        }});

        $.ajax({{
            url: 'admin_ajax/add_{page_name}.php',
            type: 'POST',
            data: JSON.stringify(postData),
            contentType: 'application/json',
            beforeSend: function() {{
                $('#add{page_name_title}Form button[type="submit"]').prop('disabled', true)
                    .html('<i class="anticon anticon-loading anticon-spin"></i> Saving...');
            }},
            success: function(response) {{
                if (response.success) {{
                    toastr.success(response.message);
                    $('#add{page_name_title}Modal').modal('hide');
                    {page_name}Table.ajax.reload();
                    $('#add{page_name_title}Form')[0].reset();
                }} else {{
                    toastr.error(response.message);
                }}
            }},
            complete: function() {{
                $('#add{page_name_title}Form button[type="submit"]').prop('disabled', false).html('Save');
            }}
        }});
    }});
    
    // Edit item
    $('#{page_name}Table').on('click', '.edit-item', function() {{
        const id = $(this).data('id');
        
        $.get('admin_ajax/get_{page_name}.php?id=' + id, function(response) {{
            if (response.success) {{
                const item = response.data;
                $('#edit{page_name_title}Id').val(item.id);
                $('#edit{page_name_title}Name').val(item.name);
                $('#edit{page_name_title}Status').val(item.status);
                $('#edit{page_name_title}Modal').modal('show');
            }} else {{
                toastr.error(response.message);
            }}
        }});
    }});
    
    // Update item
    $('#edit{page_name_title}Form').submit(function(e) {{
        e.preventDefault();
        const formData = $(this).serializeArray();
        const postData = {{}};
        $.each(formData, function(i, field) {{
            postData[field.name] = field.value;
        }});

        $.ajax({{
            url: 'admin_ajax/update_{page_name}.php',
            type: 'POST',
            data: JSON.stringify(postData),
            contentType: 'application/json',
            success: function(response) {{
                if (response.success) {{
                    toastr.success(response.message);
                    $('#edit{page_name_title}Modal').modal('hide');
                    {page_name}Table.ajax.reload();
                }} else {{
                    toastr.error(response.message);
                }}
            }}
        }});
    }});
    
    // Delete item
    $('#{page_name}Table').on('click', '.delete-item', function() {{
        const id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this item?')) {{
            $.post('admin_ajax/delete_{page_name}.php', {{ id: id }}, function(response) {{
                if (response.success) {{
                    toastr.success(response.message);
                    {page_name}Table.ajax.reload();
                }} else {{
                    toastr.error(response.message);
                }}
            }});
        }}
    }});
    
    // Refresh
    $('#refresh{page_name_title}').click(function() {{
        {page_name}Table.ajax.reload();
    }});
}});
</script>
'''
    
    return template.format(
        timestamp=CURRENT_TIMESTAMP,
        user=CURRENT_USER,
        title=title,
        page_name=page_name,
        page_name_title=page_name.title(),
        headers_str=headers_str,
        dt_columns_str=dt_columns_str
    )

def get_ajax_template(config, page_name):
    """Generate AJAX endpoint template with proper database queries"""
    
    table = config.get('table', 'your_table')
    columns = config.get('columns', ['id', 'name', 'status', 'created_at'])
    
    # Build SELECT clause
    select_columns = ', '.join(columns)
    
    # Build search conditions
    searchable_cols = [col for col in columns if col not in ['id', 'created_at', 'updated_at']]
    search_conditions = []
    for col in searchable_cols:
        search_conditions.append("{0} LIKE ?".format(col))
    
    search_where = " OR ".join(search_conditions)
    search_params = ["$searchTerm" for _ in searchable_cols]
    
    template = '''<?php
require_once '../admin_session.php';
// Generated on: {timestamp} UTC by {user}

header('Content-Type: application/json');

try {{
    // For DataTables server-side processing
    $draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 1;
    $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
    $length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
    $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
    
    // Base query for {table} table
    $query = "
        SELECT {select_columns}
        FROM {table}
        WHERE 1=1
    ";
    
    $params = [];
    
    // Add search filter
    if ($search) {{
        $query .= " AND ({search_where})";
        $searchTerm = "%$search%";
        $params = array_merge($params, [{search_params}]);
    }}
    
    // Get total records count
    $countQuery = "SELECT COUNT(*) as total FROM {table} WHERE 1=1";
    if ($search) {{
        $countQuery .= " AND ({search_where})";
    }}
    
    $stmt = $pdo->prepare($countQuery);
    $stmt->execute($search ? [{search_params}] : []);
    $totalRecords = $stmt->fetchColumn();
    
    // Add sorting
    $orderColumn = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 0;
    $orderDirection = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'desc';
    
    $columns = ['{columns_list}'];
    
    if (isset($columns[$orderColumn])) {{
        $query .= " ORDER BY {{$columns[$orderColumn]}} $orderDirection";
    }} else {{
        $query .= " ORDER BY {first_column} DESC";
    }}
    
    $query .= " LIMIT ?, ?";
    $params[] = $start;
    $params[] = $length;
    
    // Get filtered data
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Prepare response for DataTables
    $response = [
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalRecords,
        'data' => $data
    ];
    
    echo json_encode($response);
    
}} catch (PDOException $e) {{
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}}
?>'''
    
    return template.format(
        timestamp=CURRENT_TIMESTAMP,
        user=CURRENT_USER,
        table=table,
        select_columns=select_columns,
        search_where=search_where,
        search_params=', '.join(search_params),
        columns_list="', '".join(columns),
        first_column=columns[0] if columns else 'id'
    )

def get_add_ajax_template(config, page_name):
    """Generate add AJAX endpoint"""
    
    table = config.get('table', 'your_table')
    
    template = '''<?php
require_once '../admin_session.php';
// Generated on: {timestamp} UTC by {user}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {{
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {{
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}}

// Basic validation
if (empty($input['name'])) {{
    echo json_encode(['success' => false, 'message' => 'Name is required']);
    exit();
}}

$admin_id = $_SESSION['admin_id'];

try {{
    $pdo->beginTransaction();
    
    // Insert record - MODIFY FIELDS AS NEEDED FOR {table}
    $stmt = $pdo->prepare("
        INSERT INTO {table} (name, status, created_at) 
        VALUES (?, ?, NOW())
    ");
    $stmt->execute([
        $input['name'],
        $input['status'] ?? 1
    ]);
    
    $new_id = $pdo->lastInsertId();
    
    // Log the action
    $stmt = $pdo->prepare("
        INSERT INTO audit_logs 
        (admin_id, action, entity_type, entity_id, new_value, ip_address, user_agent, created_at)
        VALUES (?, 'create', '{table}', ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $admin_id,
        $new_id,
        json_encode($input),
        $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Record added successfully!',
        'id' => $new_id
    ]);
    
}} catch (Exception $e) {{
    $pdo->rollBack();
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}}
?>'''
    
    return template.format(
        timestamp=CURRENT_TIMESTAMP,
        user=CURRENT_USER,
        table=table
    )

def get_update_ajax_template(config, page_name):
    """Generate update AJAX endpoint"""
    
    table = config.get('table', 'your_table')
    
    template = '''<?php
require_once '../admin_session.php';
// Generated on: {timestamp} UTC by {user}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {{
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id'])) {{
    echo json_encode(['success' => false, 'message' => 'Invalid input or missing ID']);
    exit();
}}

$id = (int)$input['id'];
$admin_id = $_SESSION['admin_id'];

try {{
    $pdo->beginTransaction();
    
    // Get old values for audit
    $stmt = $pdo->prepare("SELECT * FROM {table} WHERE id = ?");
    $stmt->execute([$id]);
    $old_values = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$old_values) {{
        echo json_encode(['success' => false, 'message' => 'Record not found']);
        exit();
    }}
    
    // Update record - MODIFY FIELDS AS NEEDED FOR {table}
    $stmt = $pdo->prepare("
        UPDATE {table} 
        SET name = ?, status = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([
        $input['name'] ?? $old_values['name'],
        $input['status'] ?? $old_values['status'],
        $id
    ]);
    
    // Log the action
    $stmt = $pdo->prepare("
        INSERT INTO audit_logs 
        (admin_id, action, entity_type, entity_id, old_value, new_value, ip_address, user_agent, created_at)
        VALUES (?, 'update', '{table}', ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $admin_id,
        $id,
        json_encode($old_values),
        json_encode($input),
        $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Record updated successfully!'
    ]);
    
}} catch (Exception $e) {{
    $pdo->rollBack();
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}}
?>'''
    
    return template.format(
        timestamp=CURRENT_TIMESTAMP,
        user=CURRENT_USER,
        table=table
    )

def get_delete_ajax_template(config, page_name):
    """Generate delete AJAX endpoint"""
    
    table = config.get('table', 'your_table')
    
    template = '''<?php
require_once '../admin_session.php';
// Generated on: {timestamp} UTC by {user}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {{
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {{
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit();
}}

$id = (int)$_POST['id'];
$admin_id = $_SESSION['admin_id'];

try {{
    $pdo->beginTransaction();
    
    // Get record for audit
    $stmt = $pdo->prepare("SELECT * FROM {table} WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$record) {{
        echo json_encode(['success' => false, 'message' => 'Record not found']);
        exit();
    }}
    
    // Delete record
    $stmt = $pdo->prepare("DELETE FROM {table} WHERE id = ?");
    $stmt->execute([$id]);
    
    // Log the action
    $stmt = $pdo->prepare("
        INSERT INTO audit_logs 
        (admin_id, action, entity_type, entity_id, old_value, ip_address, user_agent, created_at)
        VALUES (?, 'delete', '{table}', ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $admin_id,
        $id,
        json_encode($record),
        $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Record deleted successfully!'
    ]);
    
}} catch (Exception $e) {{
    $pdo->rollBack();
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}}
?>'''
    
    return template.format(
        timestamp=CURRENT_TIMESTAMP,
        user=CURRENT_USER,
        table=table
    )

def get_system_info_template():
    """Generate system information page"""
    
    template = '''<?php
require_once 'admin_header.php';
// Generated on: {timestamp} UTC by {user}

// Get system information
$system_info = [
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'mysql_version' => '',
    'disk_space' => '',
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size')
];

// Get MySQL version
try {{
    $stmt = $pdo->query("SELECT VERSION() as version");
    $mysql = $stmt->fetch();
    $system_info['mysql_version'] = $mysql['version'];
}} catch (Exception $e) {{
    $system_info['mysql_version'] = 'Unable to determine';
}}

// Get disk space
if (function_exists('disk_free_space')) {{
    $free_space = disk_free_space('.');
    $total_space = disk_total_space('.');
    $system_info['disk_space'] = [
        'free' => formatBytes($free_space),
        'total' => formatBytes($total_space),
        'used' => formatBytes($total_space - $free_space)
    ];
}}

// Get database statistics
$db_stats = [];
try {{
    $stmt = $pdo->query("
        SELECT 
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM cases) as total_cases,
            (SELECT COUNT(*) FROM transactions) as total_transactions,
            (SELECT COUNT(*) FROM admin_logs) as total_logs
    ");
    $db_stats = $stmt->fetch(PDO::FETCH_ASSOC);
}} catch (Exception $e) {{
    $db_stats = ['error' => $e->getMessage()];
}}

function formatBytes($bytes, $precision = 2) {{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024; $i++) {{
        $bytes /= 1024;
    }}
    
    return round($bytes, $precision) . ' ' . $units[$i];
}}
?>

<div class="main-content">
    <div class="page-header">
        <h2>System Information</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="admin_dashboard.php" class="breadcrumb-item"><i class="anticon anticon-home"></i> Dashboard</a>
                <span class="breadcrumb-item active">System Information</span>
            </nav>
        </div>
    </div>
    
    <!-- Generated Info Banner -->
    <div class="alert alert-info mb-3">
        <i class="anticon anticon-info-circle"></i>
        <strong>Generated:</strong> {timestamp} UTC by {user}
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5>Server Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>PHP Version:</strong></td>
                            <td><?= $system_info['php_version'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Server Software:</strong></td>
                            <td><?= $system_info['server_software'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>MySQL Version:</strong></td>
                            <td><?= $system_info['mysql_version'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Memory Limit:</strong></td>
                            <td><?= $system_info['memory_limit'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Max Execution Time:</strong></td>
                            <td><?= $system_info['max_execution_time'] ?> seconds</td>
                        </tr>
                        <tr>
                            <td><strong>Upload Max Filesize:</strong></td>
                            <td><?= $system_info['upload_max_filesize'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Post Max Size:</strong></td>
                            <td><?= $system_info['post_max_size'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5>Database Statistics</h5>
                    <?php if (isset($db_stats['error'])): ?>
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?= $db_stats['error'] ?>
                        </div>
                    <?php else: ?>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Total Users:</strong></td>
                                <td><?= number_format($db_stats['total_users']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Cases:</strong></td>
                                <td><?= number_format($db_stats['total_cases']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Transactions:</strong></td>
                                <td><?= number_format($db_stats['total_transactions']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Logs:</strong></td>
                                <td><?= number_format($db_stats['total_logs']) ?></td>
                            </tr>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (isset($system_info['disk_space'])): ?>
            <div class="card mt-3">
                <div class="card-body">
                    <h5>Disk Space</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Total Space:</strong></td>
                            <td><?= $system_info['disk_space']['total'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Used Space:</strong></td>
                            <td><?= $system_info['disk_space']['used'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Free Space:</strong></td>
                            <td><?= $system_info['disk_space']['free'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
'''
    
    return template.format(
        timestamp=CURRENT_TIMESTAMP,
        user=CURRENT_USER
    )

def get_backup_template():
    """Generate database backup page"""
    
    template = '''<?php
require_once 'admin_header.php';
// Generated on: {timestamp} UTC by {user}

// Get existing backups
$backup_dir = '../backups/';
$backups = [];

if (is_dir($backup_dir)) {{
    $files = scandir($backup_dir);
    foreach ($files as $file) {{
        if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'sql') {{
            $backups[] = [
                'name' => $file,
                'size' => filesize($backup_dir . $file),
                'date' => filemtime($backup_dir . $file)
            ];
        }}
    }}
    
    // Sort by date descending
    usort($backups, function($a, $b) {{
        return $b['date'] - $a['date'];
    }});
}}

function formatBytes($bytes) {{
    $units = ['B', 'KB', 'MB', 'GB'];
    for ($i = 0; $bytes > 1024; $i++) {{
        $bytes /= 1024;
    }}
    return round($bytes, 2) . ' ' . $units[$i];
}}
?>

<div class="main-content">
    <div class="page-header">
        <h2>Database Backup & Restore</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="admin_dashboard.php" class="breadcrumb-item"><i class="anticon anticon-home"></i> Dashboard</a>
                <span class="breadcrumb-item active">Backup & Restore</span>
            </nav>
        </div>
    </div>
    
    <!-- Generated Info Banner -->
    <div class="alert alert-info mb-3">
        <i class="anticon anticon-info-circle"></i>
        <strong>Generated:</strong> {timestamp} UTC by {user} | 
        <strong>Database:</strong> scam_recovery
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Database Backups</h5>
                        <button class="btn btn-primary" id="createBackup">
                            <i class="anticon anticon-cloud-upload"></i> Create Backup
                        </button>
                    </div>
                    
                    <?php if (empty($backups)): ?>
                        <div class="alert alert-info">
                            <i class="anticon anticon-info-circle"></i>
                            No backups found. Create your first backup to get started.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Filename</th>
                                        <th>Size</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($backups as $backup): ?>
                                        <tr>
                                            <td>
                                                <i class="anticon anticon-file-zip"></i>
                                                <?= htmlspecialchars($backup['name']) ?>
                                            </td>
                                            <td><?= formatBytes($backup['size']) ?></td>
                                            <td><?= date('Y-m-d H:i:s', $backup['date']) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-success download-backup" 
                                                            data-file="<?= htmlspecialchars($backup['name']) ?>"
                                                            title="Download">
                                                        <i class="anticon anticon-download"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning restore-backup" 
                                                            data-file="<?= htmlspecialchars($backup['name']) ?>"
                                                            title="Restore">
                                                        <i class="anticon anticon-redo"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-backup" 
                                                            data-file="<?= htmlspecialchars($backup['name']) ?>"
                                                            title="Delete">
                                                        <i class="anticon anticon-delete"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5>Backup Settings</h5>
                    
                    <div class="form-group">
                        <label>Auto Backup</label>
                        <select class="form-control" id="autoBackupFrequency">
                            <option value="disabled">Disabled</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Retention Period</label>
                        <select class="form-control" id="backupRetention">
                            <option value="7">7 days</option>
                            <option value="30">30 days</option>
                            <option value="90">90 days</option>
                            <option value="365">1 year</option>
                        </select>
                    </div>
                    
                    <button class="btn btn-success btn-block" id="saveBackupSettings">
                        <i class="anticon anticon-save"></i> Save Settings
                    </button>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h5>Upload Backup</h5>
                    <p class="text-muted">Restore from an uploaded backup file</p>
                    
                    <form id="uploadBackupForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <input type="file" class="form-control" name="backup_file" accept=".sql" required>
                        </div>
                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="anticon anticon-upload"></i> Upload & Restore
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

<script>
$(document).ready(function() {{
    // Create backup
    $('#createBackup').click(function() {{
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="anticon anticon-loading anticon-spin"></i> Creating...');
        
        $.post('admin_ajax/create_backup.php')
        .done(function(response) {{
            if (response.success) {{
                toastr.success(response.message);
                setTimeout(() => location.reload(), 2000);
            }} else {{
                toastr.error(response.message);
            }}
        }})
        .fail(function() {{
            toastr.error('Failed to create backup');
        }})
        .always(function() {{
            btn.prop('disabled', false).html('<i class="anticon anticon-cloud-upload"></i> Create Backup');
        }});
    }});
    
    // Download, delete, restore backup functions
    $('.download-backup').click(function() {{
        const filename = $(this).data('file');
        window.location.href = 'admin_ajax/download_backup.php?file=' + encodeURIComponent(filename);
    }});
    
    $('.delete-backup').click(function() {{
        const filename = $(this).data('file');
        if (confirm('Are you sure you want to delete this backup?')) {{
            $.post('admin_ajax/delete_backup.php', {{ file: filename }})
            .done(function(response) {{
                if (response.success) {{
                    toastr.success(response.message);
                    location.reload();
                }} else {{
                    toastr.error(response.message);
                }}
            }});
        }}
    }});
}});
</script>
'''
    
    return template.format(
        timestamp=CURRENT_TIMESTAMP,
        user=CURRENT_USER
    )

def create_files():
    """Create all missing admin files with proper database integration"""
    
    # Create directories if they don't exist
    os.makedirs('admin_files', exist_ok=True)
    os.makedirs('admin_files/admin_ajax', exist_ok=True)
    
    # Find missing files
    missing_files = [f for f in file_configs.keys() if f not in existing_files]
    
    created_count = 0
    
    print(f"📊 Analysis Results:")
    print(f"   • Total files in sidebar: {len(file_configs)}")
    print(f"   • Existing files: {len(existing_files)}")
    print(f"   • Missing files: {len(missing_files)}")
    print(f"\n🚀 Creating missing files...\n")
    
    for filename in missing_files:
        config = file_configs[filename]
        page_name = filename.replace('admin_', '').replace('.php', '')
        
        print(f"📄 Creating {filename}...")
        
        # Generate file content based on type
        if config['type'] == 'basic':
            content = get_basic_admin_template(config, page_name)
        elif config['type'] == 'info':
            content = get_system_info_template()
        elif config['type'] == 'backup':
            content = get_backup_template()
        else:
            # Generate basic template for other types
            content = get_basic_admin_template(config, page_name)
        
        # Write main file
        with open(f'admin_files/{filename}', 'w', encoding='utf-8') as f:
            f.write(content)
        
        # Create corresponding AJAX files for basic types
        if config['type'] == 'basic' and 'table' in config:
            ajax_files = [
                ('get', get_ajax_template),
                ('add', get_add_ajax_template),
                ('update', get_update_ajax_template),
                ('delete', get_delete_ajax_template)
            ]
            
            for operation, template_func in ajax_files:
                ajax_filename = f"admin_ajax/{operation}_{page_name}.php"
                ajax_content = template_func(config, page_name)
                
                with open(f'admin_files/{ajax_filename}', 'w', encoding='utf-8') as f:
                    f.write(ajax_content)
                
                print(f"   ✅ Created AJAX file: {ajax_filename}")
        
        created_count += 1
        print(f"   ✅ Completed!")
    
    return created_count, missing_files

def create_additional_sql():
    """Create additional SQL for missing tables"""
    
    additional_sql = '''-- Additional tables for Scam Recovery System
-- Generated on: {timestamp} UTC by {user}

-- FAQ Table
CREATE TABLE IF NOT EXISTS `faq` (
    `id` int NOT NULL AUTO_INCREMENT,
    `question` varchar(500) NOT NULL,
    `answer` text NOT NULL,
    `category` varchar(100) DEFAULT NULL,
    `sort_order` int DEFAULT '0',
    `is_active` tinyint(1) DEFAULT '1',
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Help Articles Table
CREATE TABLE IF NOT EXISTS `help_articles` (
    `id` int NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `content` longtext NOT NULL,
    `category` varchar(100) DEFAULT NULL,
    `tags` varchar(255) DEFAULT NULL,
    `is_published` tinyint(1) DEFAULT '0',
    `views` int DEFAULT '0',
    `created_by` int DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `created_by` (`created_by`),
    CONSTRAINT `help_articles_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Admin Roles Table
CREATE TABLE IF NOT EXISTS `admin_roles` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `permissions` text,
    `description` varchar(255) DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT '1',
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- IP Whitelist Table
CREATE TABLE IF NOT EXISTS `ip_whitelist` (
    `id` int NOT NULL AUTO_INCREMENT,
    `ip_address` varchar(45) NOT NULL,
    `description` varchar(255) DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT '1',
    `created_by` int DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `created_by` (`created_by`),
    CONSTRAINT `ip_whitelist_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Blocked IPs Table  
CREATE TABLE IF NOT EXISTS `blocked_ips` (
    `id` int NOT NULL AUTO_INCREMENT,
    `ip_address` varchar(45) NOT NULL,
    `reason` varchar(255) DEFAULT NULL,
    `blocked_until` datetime DEFAULT NULL,
    `is_permanent` tinyint(1) DEFAULT '0',
    `created_by` int DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `created_by` (`created_by`),
    CONSTRAINT `blocked_ips_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insert default admin roles
INSERT IGNORE INTO admin_roles (name, description, permissions) VALUES 
('Super Admin', 'Full system access', '["*"]'),
('Admin', 'Standard admin access', '["users", "cases", "transactions", "reports"]'),
('Support', 'Support team access', '["cases", "tickets", "users.view"]');

-- Generated by Admin Panel File Generator v2.0
-- Timestamp: {timestamp} UTC
-- User: {user}
'''.format(timestamp=CURRENT_TIMESTAMP, user=CURRENT_USER)
    
    with open('admin_files/additional_tables.sql', 'w', encoding='utf-8') as f:
        f.write(additional_sql)
    
    print("📋 Created additional_tables.sql for missing database tables")

def create_readme(created_count, missing_files):
    """Create comprehensive README with installation instructions"""
    
    readme_content = '''# 🚀 Scam Recovery Admin Panel - Generated Files

**Generated on:** {timestamp} UTC  
**Generated by:** {user}  
**Database Schema:** Based on scam_recovery.sql  
**Files Created:** {count} admin pages + AJAX endpoints  
**Version:** 2.0

## 📁 Generated Files

### Main Admin Pages
'''.format(timestamp=CURRENT_TIMESTAMP, user=CURRENT_USER, count=created_count)
    
    for filename in missing_files:
        if filename in file_configs:
            config = file_configs[filename]
            table_info = " (Table: `{0}`)".format(config['table']) if 'table' in config else ""
            readme_content += "- **{0}** - {1}{2}\n".format(filename, config['title'], table_info)