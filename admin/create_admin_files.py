#!/usr/bin/env python3
"""
Admin Panel File Generator
Creates missing admin panel files based on sidebar configuration
"""

import os
import re
from datetime import datetime

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

# Files referenced in the sidebar
sidebar_files = [
    # User Management
    'admin_users.php',
    'admin_kyc.php', 
    'admin_online_users.php',
    'admin_user_activity.php',
    
    # Case Management
    'admin_cases.php',
    'admin_case_assignments.php',
    'admin_platforms.php',
    
    # Financial Management
    'admin_transactions.php',
    'admin_deposits.php',
    'admin_withdrawals.php',
    
    # Communications
    'admin_email_logs.php',
    'admin_email_templates.php',
    'admin_notifications.php',
    'admin_smtp_settings.php',
    
    # Support System
    'admin_support_tickets.php',
    'admin_faq.php',
    'admin_help_articles.php',
    
    # Reports & Analytics
    'admin_reports.php',
    'admin_analytics.php',
    'admin_statistics.php',
    'admin_export.php',
    
    # Admin Management
    'admin_admins.php',
    'admin_roles.php',
    'admin_login_logs.php',
    
    # System Settings
    'admin_settings.php',
    'admin_audit_logs.php',
    'admin_system_info.php',
    'admin_backup.php',
    'admin_maintenance.php',
    
    # Payment System
    'admin_payment_methods.php',
    'admin_payment_settings.php',
    'admin_crypto_settings.php',
    
    # File Management
    'admin_documents.php',
    'admin_file_manager.php',
    'admin_media_library.php',
    
    # Security Center
    'admin_security.php',
    'admin_ip_whitelist.php',
    'admin_blocked_ips.php',
    'admin_2fa_settings.php'
]

# Find missing files
missing_files = [f for f in sidebar_files if f not in existing_files]

print(f"Total files in sidebar: {len(sidebar_files)}")
print(f"Existing files: {len(existing_files)}")
print(f"Missing files: {len(missing_files)}")
print("\nMissing files:")
for file in missing_files:
    print(f"  - {file}")

# File templates
def get_basic_admin_template(title, page_name, table_name=None, has_datatable=True):
    """Generate basic admin page template"""
    
    # Convert title to breadcrumb format
    breadcrumb = title
    
    # Generate DataTable section if needed
    datatable_section = ""
    if has_datatable and table_name:
        datatable_section = f"""
            <div class="table-responsive">
                <table id="{page_name}Table" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>"""
    
    # Generate JavaScript section
    js_section = ""
    if has_datatable:
        js_section = f"""
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
        order: [[3, 'desc']],
        columns: [
            {{ data: 'id' }},
            {{ data: 'name' }},
            {{ 
                data: 'status',
                render: function(data) {{
                    const statusClass = data == 'active' ? 'success' : 'secondary';
                    return `<span class="badge badge-${{statusClass}}">${{data.toUpperCase()}}</span>`;
                }}
            }},
            {{ 
                data: 'created_at',
                render: function(data) {{
                    return new Date(data).toLocaleDateString();
                }}
            }},
            {{
                data: 'id',
                render: function(data, type, row) {{
                    return `
                        <div class="btn-group">
                            <button class="btn btn-sm btn-info view-item" 
                                    data-id="${{data}}" 
                                    title="View Details">
                                <i class="anticon anticon-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary edit-item" 
                                    data-id="${{data}}" 
                                    title="Edit">
                                <i class="anticon anticon-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-item" 
                                    data-id="${{data}}" 
                                    title="Delete">
                                <i class="anticon anticon-delete"></i>
                            </button>
                        </div>
                    `;
                }}
            }}
        ]
    }});
    
    // Refresh button
    $('#refresh{page_name.title()}').click(function() {{
        {page_name}Table.ajax.reload();
    }});
}});
</script>"""

    return f"""<?php
require_once 'admin_header.php';
?>

<div class="main-content">
    <div class="page-header">
        <h2>{title}</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="admin_dashboard.php" class="breadcrumb-item"><i class="anticon anticon-home"></i> Dashboard</a>
                <span class="breadcrumb-item active">{breadcrumb}</span>
            </nav>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>{title}</h5>
                <div class="d-flex">
                    <button class="btn btn-info mr-2" id="refresh{page_name.title()}">
                        <i class="anticon anticon-reload"></i> Refresh
                    </button>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#add{page_name.title()}Modal">
                        <i class="anticon anticon-plus"></i> Add New
                    </button>
                </div>
            </div>
            
            {datatable_section}
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="add{page_name.title()}Modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New {title}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <form id="add{page_name.title()}Form">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
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

<?php require_once 'admin_footer.php'; ?>

{js_section}
"""

def get_ajax_template(endpoint_name, table_name=None):
    """Generate AJAX endpoint template"""
    
    return f"""<?php
require_once '../admin_session.php';

header('Content-Type: application/json');

try {{
    // For DataTables server-side processing
    $draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 1;
    $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
    $length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
    $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
    
    // Base query - MODIFY TABLE NAME AS NEEDED
    $query = "
        SELECT 
            id,
            name,
            status,
            created_at
        FROM {table_name or 'your_table_name'}
        WHERE 1=1
    ";
    
    $params = [];
    
    // Add search filter
    if ($search) {{
        $query .= " AND (name LIKE ? OR status LIKE ?)";
        $searchTerm = "%$search%";
        $params = array_merge($params, [$searchTerm, $searchTerm]);
    }}
    
    // Get total records count
    $countQuery = "SELECT COUNT(*) as total FROM {table_name or 'your_table_name'} WHERE 1=1";
    if ($search) {{
        $countQuery .= " AND (name LIKE ? OR status LIKE ?)";
    }}
    
    $stmt = $pdo->prepare($countQuery);
    $stmt->execute($search ? [$searchTerm, $searchTerm] : []);
    $totalRecords = $stmt->fetchColumn();
    
    // Add sorting
    $orderColumn = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 0;
    $orderDirection = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'desc';
    
    $columns = ['id', 'name', 'status', 'created_at', 'id'];
    
    if (isset($columns[$orderColumn])) {{
        $query .= " ORDER BY {{$columns[$orderColumn]}} $orderDirection";
    }} else {{
        $query .= " ORDER BY created_at DESC";
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
?>"""

def get_settings_template(title, settings_type):
    """Generate settings page template"""
    
    return f"""<?php
require_once 'admin_header.php';

// Get current settings
$settings = [];
$stmt = $pdo->prepare("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE ?");
$stmt->execute(["{settings_type}_%"]);
while ($row = $stmt->fetch()) {{
    $settings[$row['setting_key']] = $row['setting_value'];
}}
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
            <h5>{title} Configuration</h5>
            
            <form id="settingsForm" class="mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Setting Name</label>
                            <input type="text" class="form-control" name="{settings_type}_setting1" 
                                   value="<?= htmlspecialchars($settings['{settings_type}_setting1'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="{settings_type}_status">
                                <option value="enabled" <?= ($settings['{settings_type}_status'] ?? '') == 'enabled' ? 'selected' : '' ?>>Enabled</option>
                                <option value="disabled" <?= ($settings['{settings_type}_status'] ?? '') == 'disabled' ? 'selected' : '' ?>>Disabled</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" name="{settings_type}_description" rows="3"><?= htmlspecialchars($settings['{settings_type}_description'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="anticon anticon-save"></i> Save Settings
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

<script>
$(document).ready(function() {{
    $('#settingsForm').submit(function(e) {{
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.post('admin_ajax/save_settings.php', formData + '&type={settings_type}')
        .done(function(response) {{
            if (response.success) {{
                toastr.success(response.message);
            }} else {{
                toastr.error(response.message);
            }}
        }})
        .fail(function() {{
            toastr.error('Failed to save settings');
        }});
    }});
}});
</script>
"""

# File configurations
file_configs = {
    'admin_case_assignments.php': {
        'title': 'Case Assignments',
        'table': 'cases',
        'type': 'basic'
    },
    'admin_email_templates.php': {
        'title': 'Email Templates', 
        'table': 'email_templates',
        'type': 'basic'
    },
    'admin_notifications.php': {
        'title': 'Notifications',
        'table': 'admin_notifications', 
        'type': 'basic'
    },
    'admin_smtp_settings.php': {
        'title': 'SMTP Settings',
        'type': 'settings',
        'settings_type': 'smtp'
    },
    'admin_support_tickets.php': {
        'title': 'Support Tickets',
        'table': 'support_tickets',
        'type': 'basic'
    },
    'admin_faq.php': {
        'title': 'FAQ Management',
        'table': 'faq',
        'type': 'basic'
    },
    'admin_help_articles.php': {
        'title': 'Help Articles',
        'table': 'help_articles',
        'type': 'basic'
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
        'title': 'Statistics',
        'type': 'statistics'
    },
    'admin_export.php': {
        'title': 'Data Export',
        'type': 'export'
    },
    'admin_admins.php': {
        'title': 'Manage Admins',
        'table': 'admins',
        'type': 'basic'
    },
    'admin_roles.php': {
        'title': 'Roles & Permissions',
        'table': 'admin_roles',
        'type': 'basic'
    },
    'admin_login_logs.php': {
        'title': 'Admin Login Logs',
        'table': 'admin_login_logs',
        'type': 'basic'
    },
    'admin_system_info.php': {
        'title': 'System Information',
        'type': 'info'
    },
    'admin_backup.php': {
        'title': 'Backup & Restore',
        'type': 'backup'
    },
    'admin_maintenance.php': {
        'title': 'Maintenance Mode',
        'type': 'settings',
        'settings_type': 'maintenance'
    },
    'admin_payment_methods.php': {
        'title': 'Payment Methods',
        'table': 'payment_methods',
        'type': 'basic'
    },
    'admin_payment_settings.php': {
        'title': 'Payment Settings',
        'type': 'settings',
        'settings_type': 'payment'
    },
    'admin_crypto_settings.php': {
        'title': 'Crypto Settings',
        'type': 'settings',
        'settings_type': 'crypto'
    },
    'admin_documents.php': {
        'title': 'User Documents',
        'table': 'documents',
        'type': 'basic'
    },
    'admin_file_manager.php': {
        'title': 'File Manager',
        'type': 'file_manager'
    },
    'admin_media_library.php': {
        'title': 'Media Library',
        'type': 'media'
    },
    'admin_security.php': {
        'title': 'Security Settings',
        'type': 'settings',
        'settings_type': 'security'
    },
    'admin_ip_whitelist.php': {
        'title': 'IP Whitelist',
        'table': 'ip_whitelist',
        'type': 'basic'
    },
    'admin_blocked_ips.php': {
        'title': 'Blocked IPs',
        'table': 'blocked_ips',
        'type': 'basic'
    },
    'admin_2fa_settings.php': {
        'title': '2FA Settings',
        'type': 'settings',
        'settings_type': '2fa'
    }
}

def create_files():
    """Create all missing admin files"""
    
    # Create directories if they don't exist
    os.makedirs('admin_files', exist_ok=True)
    os.makedirs('admin_files/admin_ajax', exist_ok=True)
    
    created_count = 0
    
    for filename in missing_files:
        if filename in file_configs:
            config = file_configs[filename]
            page_name = filename.replace('admin_', '').replace('.php', '')
            
            print(f"Creating {filename}...")
            
            # Generate file content based on type
            if config['type'] == 'basic':
                content = get_basic_admin_template(
                    config['title'], 
                    page_name, 
                    config.get('table'),
                    has_datatable=True
                )
            elif config['type'] == 'settings':
                content = get_settings_template(
                    config['title'],
                    config['settings_type']
                )
            else:
                # Generate basic template for other types
                content = get_basic_admin_template(
                    config['title'], 
                    page_name,
                    has_datatable=False
                )
            
            # Write main file
            with open(f'admin_files/{filename}', 'w', encoding='utf-8') as f:
                f.write(content)
            
            # Create corresponding AJAX file if basic type
            if config['type'] == 'basic':
                ajax_filename = f"admin_ajax/get_{page_name}.php"
                ajax_content = get_ajax_template(page_name, config.get('table'))
                
                with open(f'admin_files/{ajax_filename}', 'w', encoding='utf-8') as f:
                    f.write(ajax_content)
                
                print(f"  Created AJAX file: {ajax_filename}")
            
            created_count += 1
            
        else:
            print(f"Warning: No configuration found for {filename}")
    
    print(f"\n✅ Successfully created {created_count} files!")
    print(f"📁 Files created in 'admin_files' directory")

def create_readme():
    """Create README with instructions"""
    
    readme_content = f"""# Admin Panel Files Generator

Generated on: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}

## Files Created

### Main Admin Pages
"""
    
    for filename in missing_files:
        if filename in file_configs:
            config = file_configs[filename]
            readme_content += f"- **{filename}** - {config['title']}\n"
    
    readme_content += f"""
### AJAX Endpoints
- Corresponding AJAX files created in `admin_ajax/` directory
- Each basic CRUD page has associated AJAX handlers

## Installation Instructions

1. **Upload Files**
   - Copy all files from `admin_files/` to your admin directory
   - Copy AJAX files to your `admin/admin_ajax/` directory

2. **Database Setup**
   - Some pages reference database tables that may not exist
   - Create necessary tables or modify queries as needed
   - Update table names in AJAX files to match your schema

3. **Customization**
   - Modify form fields in modal forms as needed
   - Update DataTable columns to match your data structure
   - Customize validation and processing logic
   - Add proper error handling and security measures

4. **Security**
   - Ensure all forms have CSRF protection
   - Add proper input validation and sanitization
   - Implement proper access control checks

## File Types Generated

1. **Basic CRUD Pages** - Full featured pages with DataTables, modals, and AJAX
2. **Settings Pages** - Configuration pages with form handling
3. **Specialty Pages** - Custom pages for specific functionality

## Notes

- All pages include responsive design
- DataTables with server-side processing
- Modal forms for create/edit operations
- Consistent styling with your existing admin theme
- Error handling and success messages via toastr
- Proper breadcrumb navigation

## Next Steps

1. Review generated files for your specific needs
2. Update database table references
3. Customize form fields and validation
4. Test functionality and fix any issues
5. Add any missing business logic
"""
    
    with open('admin_files/README.md', 'w', encoding='utf-8') as f:
        f.write(readme_content)

if __name__ == "__main__":
    print("🚀 Admin Panel Files Generator")
    print("=" * 50)
    
    create_files()
    create_readme()
    
    print("\n📋 Summary:")
    print(f"Total sidebar files: {len(sidebar_files)}")
    print(f"Existing files: {len(existing_files)}")
    print(f"Missing files: {len(missing_files)}")
    print(f"Files created: {len([f for f in missing_files if f in file_configs])}")
    
    print("\n📁 Generated files are in 'admin_files' directory")
    print("📖 Check README.md for installation instructions")
    print("\n✨ Generation complete!")