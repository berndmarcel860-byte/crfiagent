<?php
require_once 'config.php';
require_once 'header.php';

// Handle ticket submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ticket'])) {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    $category = trim($_POST['category']);
    $priority = $_POST['priority'] ?? 'medium';
    
    // Generate ticket number
    $ticket_number = 'TICKET-' . strtoupper(uniqid());
    
    try {
        $stmt = $pdo->prepare("INSERT INTO support_tickets 
                              (user_id, ticket_number, subject, message, category, priority) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $ticket_number, $subject, $message, $category, $priority]);
        
        $_SESSION['success'] = "Ticket submitted successfully! Your ticket number is: $ticket_number";
        header("Location: support.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error submitting ticket: " . $e->getMessage();
    }
}

// Get user's tickets
$tickets = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM support_tickets WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching tickets: " . $e->getMessage();
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4>Contact Support</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Support Information</h5>
                                    <ul class="list-unstyled">
                                        <li><i class="anticon anticon-mail"></i> Email: support@scamrecovery.com</li>
                                        <li><i class="anticon anticon-phone"></i> Phone: +1 (555) 123-4567</li>
                                        <li><i class="anticon anticon-clock-circle"></i> Hours: Mon-Fri, 9AM-5PM EST</li>
                                    </ul>
                                    <p>For urgent matters, please create a ticket with "Urgent" priority.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Create New Ticket</h5>
                                    <form method="POST" id="ticketForm">
                                        <div class="form-group">
                                            <label>Subject</label>
                                            <input type="text" class="form-control" name="subject" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select class="form-control" name="category" required>
                                                <option value="">Select category</option>
                                                <option value="Case Inquiry">Case Inquiry</option>
                                                <option value="Document Submission">Document Submission</option>
                                                <option value="Payment Issue">Payment Issue</option>
                                                <option value="Technical Problem">Technical Problem</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Priority</label>
                                            <select class="form-control" name="priority">
                                                <option value="low">Low</option>
                                                <option value="medium" selected>Medium</option>
                                                <option value="high">High</option>
                                                <option value="critical">Critical</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Message</label>
                                            <textarea class="form-control" rows="5" name="message" required></textarea>
                                        </div>
                                        <button type="submit" name="submit_ticket" class="btn btn-primary">
                                            <i class="anticon anticon-plus"></i> Submit Ticket
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="m-t-30">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Your Support Tickets</h5>
                            <button class="btn btn-info" id="refreshTickets">
                                <i class="anticon anticon-reload"></i> Refresh
                            </button>
                        </div>
                        
                        <?php if (empty($tickets)): ?>
                            <div class="alert alert-info">No support tickets found.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="ticketsTable">
                                    <thead>
                                        <tr>
                                            <th>Ticket #</th>
                                            <th>Subject</th>
                                            <th>Category</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Last Reply</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets as $ticket): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($ticket['ticket_number']) ?></td>
                                            <td><?= htmlspecialchars($ticket['subject']) ?></td>
                                            <td><?= htmlspecialchars($ticket['category']) ?></td>
                                            <td>
                                                <span class="badge badge-<?= 
                                                    $ticket['priority'] == 'low' ? 'info' : 
                                                    ($ticket['priority'] == 'medium' ? 'warning' : 
                                                    ($ticket['priority'] == 'high' ? 'danger' : 'dark')) 
                                                ?>">
                                                    <?= ucfirst($ticket['priority']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= 
                                                    $ticket['status'] == 'open' ? 'primary' : 
                                                    ($ticket['status'] == 'in_progress' ? 'warning' :
                                                    ($ticket['status'] == 'resolved' ? 'success' : 'secondary'))
                                                ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
                                                </span>
                                            </td>
                                            <td><?= $ticket['last_reply_at'] ? date('M d, Y H:i', strtotime($ticket['last_reply_at'])) : '-' ?></td>
                                            <td><?= date('M d, Y H:i', strtotime($ticket['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-info view-ticket" 
                                                            data-id="<?= $ticket['id'] ?>" 
                                                            data-ticket="<?= htmlspecialchars($ticket['ticket_number']) ?>">
                                                        <i class="anticon anticon-eye"></i> View
                                                    </button>
                                                    <?php if ($ticket['status'] != 'closed'): ?>
                                                    <button class="btn btn-sm btn-primary reply-ticket" 
                                                            data-id="<?= $ticket['id'] ?>"
                                                            data-ticket="<?= htmlspecialchars($ticket['ticket_number']) ?>">
                                                        <i class="anticon anticon-message"></i> Reply
                                                    </button>
                                                    <?php endif; ?>
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
        </div>
    </div>
</div>

<!-- View Ticket Modal -->
<div class="modal fade" id="viewTicketModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ticket Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="ticketDetails">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>Loading ticket details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="replyFromView" style="display: none;">
                    <i class="anticon anticon-message"></i> Reply
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reply to Ticket</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <form id="replyForm">
                <div class="modal-body">
                    <input type="hidden" name="ticket_id" id="reply_ticket_id">
                    <div class="alert alert-info">
                        <strong>Ticket #:</strong> <span id="reply_ticket_number"></span>
                    </div>
                    <div class="form-group">
                        <label>Your Reply</label>
                        <textarea class="form-control" name="message" rows="6" required placeholder="Type your reply message here..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Attach Files (Optional)</label>
                        <input type="file" class="form-control-file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <small class="form-text text-muted">You can attach multiple files. Max 10MB per file.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="anticon anticon-send"></i> Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>

<script>
$(document).ready(function() {
    let currentTicketId = null;
    
    // View ticket handler
    $(document).on('click', '.view-ticket', function() {
        const ticketId = $(this).data('id');
        const ticketNumber = $(this).data('ticket');
        currentTicketId = ticketId;
        
        // Show loading
        $('#ticketDetails').html(`
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p>Loading ticket details...</p>
            </div>
        `);
        
        $('#viewTicketModal').modal('show');
        
        // Load ticket details
        $.get('ajax/get_ticket_details.php', { id: ticketId })
        .done(function(response) {
            if (response.success) {
                const ticket = response.data.ticket;
                const replies = response.data.replies || [];
                
                let repliesHtml = '';
                replies.forEach(function(reply) {
                    const isAdmin = reply.admin_id ? true : false;
                    const senderName = isAdmin ? reply.admin_name : 'You';
                    const cardClass = isAdmin ? 'border-primary' : 'border-info';
                    const headerClass = isAdmin ? 'bg-primary text-white' : 'bg-info text-white';
                    
                    repliesHtml += `
                        <div class="card mb-3 ${cardClass}">
                            <div class="card-header ${headerClass}">
                                <div class="d-flex justify-content-between">
                                    <strong>${senderName}</strong>
                                    <small>${new Date(reply.created_at).toLocaleString()}</small>
                                </div>
                            </div>
                            <div class="card-body">
                                ${reply.message.replace(/\n/g, '<br>')}
                                ${reply.attachments ? getAttachmentsHtml(reply.attachments) : ''}
                            </div>
                        </div>
                    `;
                });
                
                $('#ticketDetails').html(`
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Ticket Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Ticket #:</strong></td><td>${ticket.ticket_number}</td></tr>
                                <tr><td><strong>Subject:</strong></td><td>${ticket.subject}</td></tr>
                                <tr><td><strong>Category:</strong></td><td>${ticket.category}</td></tr>
                                <tr><td><strong>Priority:</strong></td><td><span class="badge badge-${getPriorityClass(ticket.priority)}">${ticket.priority.toUpperCase()}</span></td></tr>
                                <tr><td><strong>Status:</strong></td><td><span class="badge badge-${getStatusClass(ticket.status)}">${ticket.status.replace('_', ' ').toUpperCase()}</span></td></tr>
                                <tr><td><strong>Created:</strong></td><td>${new Date(ticket.created_at).toLocaleString()}</td></tr>
                                ${ticket.last_reply_at ? `<tr><td><strong>Last Reply:</strong></td><td>${new Date(ticket.last_reply_at).toLocaleString()}</td></tr>` : ''}
                            </table>
                        </div>
                        <div class="col-md-8">
                            <h6>Original Message</h6>
                            <div class="card mb-3">
                                <div class="card-body">
                                    ${ticket.message.replace(/\n/g, '<br>')}
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h6>Conversation History</h6>
                    <div style="max-height: 500px; overflow-y: auto;">
                        ${repliesHtml || '<p class="text-muted">No replies yet.</p>'}
                    </div>
                `);
                
                // Show reply button if ticket is not closed
                if (ticket.status !== 'closed') {
                    $('#replyFromView').show();
                } else {
                    $('#replyFromView').hide();
                }
            } else {
                $('#ticketDetails').html('<div class="alert alert-danger">Error loading ticket details</div>');
            }
        })
        .fail(function() {
            $('#ticketDetails').html('<div class="alert alert-danger">Failed to load ticket details</div>');
        });
    });
    
    // Reply button handlers
    $(document).on('click', '.reply-ticket', function() {
        const ticketId = $(this).data('id');
        const ticketNumber = $(this).data('ticket');
        openReplyModal(ticketId, ticketNumber);
    });
    
    $('#replyFromView').click(function() {
        if (currentTicketId) {
            const ticketNumber = $('#ticketDetails').find('td').first().next().text();
            openReplyModal(currentTicketId, ticketNumber);
        }
    });
    
    function openReplyModal(ticketId, ticketNumber) {
        $('#reply_ticket_id').val(ticketId);
        $('#reply_ticket_number').text(ticketNumber);
        $('#replyModal').modal('show');
    }
    
    // Reply form submission
    $('#replyForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: 'ajax/reply_ticket.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#replyModal').modal('hide');
                    $('#replyForm')[0].reset();
                    
                    // Show success message
                    toastr.success('Reply sent successfully');
                    
                    // Refresh the tickets table
                    location.reload();
                } else {
                    toastr.error(response.message || 'Failed to send reply');
                }
            },
            error: function() {
                toastr.error('Failed to send reply');
            }
        });
    });
    
    // Refresh tickets
    $('#refreshTickets').click(function() {
        location.reload();
    });
    
    // Helper functions
    function getPriorityClass(priority) {
        const classes = {
            'low': 'info',
            'medium': 'warning',
            'high': 'danger',
            'critical': 'dark'
        };
        return classes[priority] || 'secondary';
    }
    
    function getStatusClass(status) {
        const classes = {
            'open': 'primary',
            'in_progress': 'warning',
            'resolved': 'success',
            'closed': 'secondary'
        };
        return classes[status] || 'secondary';
    }
    
    function getAttachmentsHtml(attachments) {
        if (!attachments) return '';
        
        try {
            const files = JSON.parse(attachments);
            if (!files.length) return '';
            
            let html = '<div class="mt-2"><small class="text-muted">Attachments:</small><br>';
            files.forEach(function(file) {
                html += `<a href="uploads/tickets/${file}" target="_blank" class="btn btn-sm btn-outline-info mr-1 mt-1">
                    <i class="anticon anticon-paper-clip"></i> ${file}
                </a>`;
            });
            html += '</div>';
            return html;
        } catch (e) {
            return '';
        }
    }
});
</script>