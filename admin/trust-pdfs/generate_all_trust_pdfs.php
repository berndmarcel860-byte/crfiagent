<?php
/**
 * Master Trust PDF Generator
 * 
 * Generates ALL trust-building PDFs at once for welcome email attachments
 * 
 * Usage:
 * php generate_all_trust_pdfs.php
 * 
 * Output: Creates all 4 trust PDFs in documents/trust/ directory
 */

declare(strict_types=1);

echo "=====================================\n";
echo "Trust PDF Generator\n";
echo "=====================================\n\n";

$startTime = microtime(true);
$pdfsGenerated = 0;
$errors = [];

// Generate BaFin Verification PDF
echo "[1/4] Generating BaFin Verification PDF...\n";
ob_start();
try {
    include __DIR__ . '/generate_bafin_verification_pdf.php';
    $pdfsGenerated++;
} catch (Exception $e) {
    $errors[] = "BaFin PDF: " . $e->getMessage();
}
$output = ob_get_clean();
echo $output . "\n";

// Generate Service Agreement PDF
echo "[2/4] Generating Service Agreement PDF...\n";
ob_start();
try {
    include __DIR__ . '/generate_service_agreement_pdf.php';
    $pdfsGenerated++;
} catch (Exception $e) {
    $errors[] = "Service Agreement PDF: " . $e->getMessage();
}
$output = ob_get_clean();
echo $output . "\n";

// Generate Company Information PDF
echo "[3/4] Generating Company Information PDF...\n";
ob_start();
try {
    include __DIR__ . '/generate_company_info_pdf.php';
    $pdfsGenerated++;
} catch (Exception $e) {
    $errors[] = "Company Info PDF: " . $e->getMessage();
}
$output = ob_get_clean();
echo $output . "\n";

// Generate Onboarding Guide PDF
echo "[4/4] Generating Onboarding Guide PDF...\n";
ob_start();
try {
    include __DIR__ . '/generate_onboarding_guide_pdf.php';
    $pdfsGenerated++;
} catch (Exception $e) {
    $errors[] = "Onboarding Guide PDF: " . $e->getMessage();
}
$output = ob_get_clean();
echo $output . "\n";

$endTime = microtime(true);
$duration = round($endTime - $startTime, 2);

echo "=====================================\n";
echo "Summary\n";
echo "=====================================\n";
echo "PDFs Generated: $pdfsGenerated / 4\n";
echo "Time Taken: {$duration}s\n";

if (count($errors) > 0) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "✗ $error\n";
    }
    exit(1);
} else {
    echo "\n✓ All trust PDFs generated successfully!\n";
    echo "\nGenerated files are in: documents/trust/\n";
    echo "These can be attached to welcome emails.\n";
}
