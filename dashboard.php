<?php
/**
 * Dashboard - User Main Page (Refactored)
 * 
 * UPDATED: 2026-03-02
 * Branch: copilot/sub-pr-1
 * 
 * Features:
 * - Modular architecture with separate includes
 * - Clean separation of concerns
 * - Easier maintenance and testing
 * 
 * Security: PDO prepared statements, CSRF protection, session validation
 */

// Initialize dashboard (config, session, basic setup)
require_once __DIR__ . '/includes/dashboard-init.php';

// Fetch all dashboard data
require_once __DIR__ . '/includes/dashboard-data.php';
?>

<?php
// Include HTML sections
require_once __DIR__ . '/includes/dashboard/modals.php';
?>

<!-- Main Dashboard Content -->
<div class="page-wrapper">
    <?php require_once __DIR__ . '/includes/dashboard/welcome-banner.php'; ?>
    
    <?php require_once __DIR__ . '/includes/dashboard/verification-alerts.php'; ?>
    
    <?php require_once __DIR__ . '/includes/dashboard/statistics-cards.php'; ?>
    
    <?php require_once __DIR__ . '/includes/dashboard/case-cards.php'; ?>
</div>

<?php
// Include scripts and styles
require_once __DIR__ . '/includes/dashboard/scripts.php';
require_once __DIR__ . '/includes/dashboard/styles.php';
?>

<?php
// Include footer
if (file_exists(__DIR__ . '/footer.php')) {
    require_once __DIR__ . '/footer.php';
}
?>
