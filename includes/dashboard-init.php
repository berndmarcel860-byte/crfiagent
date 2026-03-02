<?php
/**
 * Dashboard Initialization
 * Handles configuration, session, and basic setup
 */

// Ensure config.php exists
if (!file_exists(__DIR__ . '/../config.php')) {
    http_response_code(500);
    echo "<h1>Server configuration error</h1><p>Missing config.php</p>";
    exit;
}
require_once __DIR__ . '/../config.php';

// Include header.php
if (file_exists(__DIR__ . '/../header.php')) {
    require_once __DIR__ . '/../header.php';
}

// Validate PDO instance
if (empty($pdo) || !($pdo instanceof PDO)) {
    http_response_code(500);
    echo "<h1>Database connection error</h1><p>Can't find valid PDO instance.</p>";
    exit;
}

// CSRF token init
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Current date/time UTC
$currentDateTime = new DateTime('now', new DateTimeZone('UTC'));
$currentDateTimeFormatted = $currentDateTime->format('Y-m-d H:i:s');

// Branding - Already loaded from header.php but ensure defaults if not set
if (!isset($appName)) {
    $appName = "Fundtracer AI";
}
if (!isset($appTagline)) {
    $appTagline = "Next-Generation Scam Recovery & Fund Tracing";
}

$brandColor = "#2950a8";
$brandGradient = "linear-gradient(90deg,#2950a8 0,#2da9e3 100%)";
$aiStatus = "Online";

// Safe defaults
$passwordChangeRequired = false;
$currentUser = null;
$currentUserLogin = null;
$cases = [];
$ongoingRecoveries = [];
$transactions = [];
$statusCounts = [];
$userId = $_SESSION['user_id'] ?? null;
$kyc_status = 'pending';
$loginLogs = [];
