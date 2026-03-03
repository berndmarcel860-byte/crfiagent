<?php
/**
 * Company Information PDF Generator
 * 
 * Generates professional Company Information Sheet with credentials,
 * team info, and success statistics
 * 
 * Usage:
 * php generate_company_info_pdf.php
 * 
 * Output: documents/trust/Company_Information_{timestamp}.pdf
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use setasign\Fpdi\FpdfTpl as FPDF;

// Database connection (with fallback)
$pdo = null;
$dbAvailable = false;

// Check if PDO MySQL driver is available
if (!extension_loaded('pdo_mysql')) {
    echo "Warning: PDO MySQL driver not found. Using default values.\n";
    echo "To install: sudo apt-get install php-mysql (or php8.x-mysql)\n";
    echo "Then restart PHP: sudo systemctl restart php-fpm\n\n";
} else {
    try {
        require_once __DIR__ . '/../../config.php';
        $dbAvailable = true;
    } catch (Exception $e) {
        echo "Warning: Database connection failed: " . $e->getMessage() . "\n";
        echo "Using default values.\n\n";
    }
}

function safe_text($text) {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text ?? '');
}

class CompanyInfoPDF extends FPDF {
    private array $company = [];
    
    function __construct($company) {
        parent::__construct();
        $this->company = $company;
    }
    
    function Header() {
        $this->SetFont('Helvetica', 'B', 16);
        $this->SetTextColor(28, 41, 69);
        $this->SetXY(20, 15);
        $this->Cell(0, 10, safe_text($this->company['brand_name']), 0, 1, 'R');
        
        $this->SetDrawColor(222, 226, 230);
        $this->Line(20, 32, 190, 32);
        $this->Ln(10);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Helvetica', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, safe_text('BaFin/FCA Ref: ' . $this->company['fca_reference']), 0, 1, 'C');
        $this->Cell(0, 5, 'Seite ' . $this->PageNo(), 0, 0, 'C');
    }
}

try {
    // Default company data
    $company = [
        'brand_name' => 'CryptoFinanz',
        'company_address' => 'Davidson House Forbury Square, Reading, RG1 3EU',
        'contact_email' => 'support@cryptofinanze.de',
        'contact_phone' => '+44 (0) 20 1234 5678',
        'fca_reference' => '910584',
        'site_url' => 'https://cryptofinanze.de'
    ];
    
    // Default statistics
    $totalCases = 150;
    $resolvedCases = 131;
    
    // Fetch from database if available
    if ($dbAvailable && $pdo) {
        $stmt = $pdo->query("SELECT * FROM system_settings WHERE id = 1");
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($settings) {
            // Override defaults with database values
            $company = [
                'brand_name' => $settings['brand_name'] ?? $company['brand_name'],
                'company_address' => $settings['company_address'] ?? $company['company_address'],
                'contact_email' => $settings['contact_email'] ?? $company['contact_email'],
                'contact_phone' => $settings['contact_phone'] ?? $company['contact_phone'],
                'fca_reference' => $settings['fca_reference_number'] ?? $company['fca_reference'],
                'site_url' => $settings['site_url'] ?? $company['site_url']
            ];
            echo "Using company data from database.\n";
        }
        
        // Get statistics from database if available
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as total_cases FROM user_cases");
            $totalCases = $stmt->fetch(PDO::FETCH_ASSOC)['total_cases'] ?? $totalCases;
            
            $stmt = $pdo->query("SELECT COUNT(*) as resolved FROM user_cases WHERE status IN ('resolved', 'closed')");
            $resolvedCases = $stmt->fetch(PDO::FETCH_ASSOC)['resolved'] ?? $resolvedCases;
            
            echo "Using statistics from database.\n";
        } catch (Exception $e) {
            echo "Warning: Could not fetch statistics. Using defaults.\n";
        }
    } else {
        echo "Using default company data and statistics (database not available).\n";
    }
    
    $successRate = $totalCases > 0 ? round(($resolvedCases / $totalCases) * 100, 1) : 0;
    
    // Add statistics to company array
    $company['total_cases'] = $totalCases;
    $company['resolved_cases'] = $resolvedCases;
    $company['success_rate'] = $successRate;
    
    $pdf = new CompanyInfoPDF($company);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    /* TITLE */
    $pdf->SetFont('Helvetica', 'B', 20);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 15, safe_text('Unternehmensinformationen'), 0, 1, 'C');
    
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->SetTextColor(108, 117, 125);
    $pdf->Cell(0, 8, safe_text('Professionelle KI-gestützte Wiederherstellung von Kryptowährungen'), 0, 1, 'C');
    
    $pdf->Ln(10);
    
    /* LICENSE BOX */
    $pdf->SetFillColor(232, 245, 233);
    $pdf->SetDrawColor(76, 175, 80);
    $pdf->Rect(20, $pdf->GetY(), 170, 20, 'FD');
    
    $yPos = $pdf->GetY();
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->SetTextColor(76, 175, 80);
    $pdf->SetXY(25, $yPos + 6);
    $pdf->Cell(0, 8, safe_text('✓ BaFin/FCA Lizenziert - Referenznummer: ' . $company['fca_reference']), 0, 1, 'C');
    
    $pdf->SetY($yPos + 25);
    $pdf->Ln(5);
    
    /* COMPANY DETAILS */
    $pdf->SetFont('Helvetica', 'B', 14);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 10, safe_text('Über uns'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    
    $pdf->MultiCell(0, 5, safe_text(
        'Wir sind ein BaFin-lizenziertes Finanzdienstleistungsunternehmen, das sich auf die ' .
        'Wiederherstellung verlorener Kryptowährungen durch modernste KI-gestützte ' .
        'Blockchain-Analyse spezialisiert hat. Unser Algorithmus identifiziert Betrugsplattformen ' .
        'und verfolgt Transaktionen, um verlorene Gelder zurückzugewinnen.'
    ));
    
    $pdf->Ln(8);
    
    /* CONTACT INFORMATION */
    $pdf->SetFont('Helvetica', 'B', 14);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 10, safe_text('Kontaktinformationen'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    
    $pdf->Cell(40, 6, safe_text('Unternehmen:'), 0, 0, 'L');
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->Cell(0, 6, safe_text($company['brand_name']), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell(40, 6, safe_text('Adresse:'), 0, 0, 'L');
    $pdf->Cell(0, 6, safe_text($company['company_address']), 0, 1, 'L');
    
    $pdf->Cell(40, 6, safe_text('E-Mail:'), 0, 0, 'L');
    $pdf->Cell(0, 6, safe_text($company['contact_email']), 0, 1, 'L');
    
    $pdf->Cell(40, 6, safe_text('Telefon:'), 0, 0, 'L');
    $pdf->Cell(0, 6, safe_text($company['contact_phone']), 0, 1, 'L');
    
    $pdf->Cell(40, 6, safe_text('Website:'), 0, 0, 'L');
    $pdf->SetTextColor(0, 102, 204);
    $pdf->Cell(0, 6, safe_text($company['site_url']), 0, 1, 'L');
    $pdf->SetTextColor(33, 37, 41);
    
    $pdf->Cell(40, 6, safe_text('BaFin/FCA Ref:'), 0, 0, 'L');
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->SetTextColor(76, 175, 80);
    $pdf->Cell(0, 6, safe_text($company['fca_reference']), 0, 1, 'L');
    $pdf->SetTextColor(33, 37, 41);
    
    $pdf->Ln(10);
    
    /* SUCCESS STATISTICS */
    $pdf->SetFont('Helvetica', 'B', 14);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 10, safe_text('Erfolgsstatistiken'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    
    // Stats boxes
    $boxWidth = 50;
    $boxHeight = 25;
    $spacing = 5;
    
    // Success Rate Box
    $pdf->SetFillColor(232, 245, 233);
    $pdf->SetDrawColor(76, 175, 80);
    $pdf->Rect(20, $pdf->GetY(), $boxWidth, $boxHeight, 'FD');
    
    $yPos = $pdf->GetY();
    $pdf->SetFont('Helvetica', '', 9);
    $pdf->SetXY(20, $yPos + 5);
    $pdf->Cell($boxWidth, 5, safe_text('Erfolgsrate'), 0, 1, 'C');
    
    $pdf->SetFont('Helvetica', 'B', 16);
    $pdf->SetTextColor(76, 175, 80);
    $pdf->SetX(20);
    $pdf->Cell($boxWidth, 10, safe_text($company['success_rate'] . '%'), 0, 1, 'C');
    
    // Total Cases Box
    $pdf->SetTextColor(33, 37, 41);
    $pdf->SetFillColor(239, 246, 255);
    $pdf->SetDrawColor(0, 102, 204);
    $pdf->Rect(20 + $boxWidth + $spacing, $yPos, $boxWidth, $boxHeight, 'FD');
    
    $pdf->SetFont('Helvetica', '', 9);
    $pdf->SetXY(20 + $boxWidth + $spacing, $yPos + 5);
    $pdf->Cell($boxWidth, 5, safe_text('Bearbeitete Fälle'), 0, 1, 'C');
    
    $pdf->SetFont('Helvetica', 'B', 16);
    $pdf->SetTextColor(0, 102, 204);
    $pdf->SetX(20 + $boxWidth + $spacing);
    $pdf->Cell($boxWidth, 10, safe_text(number_format($company['total_cases'])), 0, 1, 'C');
    
    // Resolved Cases Box
    $pdf->SetTextColor(33, 37, 41);
    $pdf->SetFillColor(255, 243, 205);
    $pdf->SetDrawColor(255, 193, 7);
    $pdf->Rect(20 + ($boxWidth + $spacing) * 2, $yPos, $boxWidth, $boxHeight, 'FD');
    
    $pdf->SetFont('Helvetica', '', 9);
    $pdf->SetXY(20 + ($boxWidth + $spacing) * 2, $yPos + 5);
    $pdf->Cell($boxWidth, 5, safe_text('Gelöste Fälle'), 0, 1, 'C');
    
    $pdf->SetFont('Helvetica', 'B', 16);
    $pdf->SetTextColor(255, 152, 0);
    $pdf->SetX(20 + ($boxWidth + $spacing) * 2);
    $pdf->Cell($boxWidth, 10, safe_text(number_format($company['resolved_cases'])), 0, 1, 'C');
    
    $pdf->SetY($yPos + $boxHeight + 10);
    
    /* SERVICES */
    $pdf->SetFont('Helvetica', 'B', 14);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 10, safe_text('Unsere Dienstleistungen'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    
    $services = [
        'KI-gestützte Blockchain-Analyse',
        'Identifizierung von Betrugsplattformen',
        'Transaktions-Tracing und Muster-Erkennung',
        'Rechtliche Unterstützung bei Wiederherstellung',
        'Transparente Fallverfolgung und Updates',
        'GDPR-konforme Datenverarbeitung'
    ];
    
    foreach ($services as $service) {
        $pdf->Cell(10, 6, safe_text('✓'), 0, 0, 'L');
        $pdf->Cell(0, 6, safe_text($service), 0, 1, 'L');
    }
    
    $pdf->Ln(8);
    
    /* WHY CHOOSE US */
    $pdf->SetFont('Helvetica', 'B', 14);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 10, safe_text('Warum uns wählen?'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    
    $reasons = [
        'BaFin/FCA lizenziert und reguliert',
        'KEINE Vorauszahlung - nur 3% bei Erfolg',
        'Modernste KI-Technologie',
        'Transparente Kommunikation',
        'GDPR-konform',
        'Erfahrenes Team'
    ];
    
    foreach ($reasons as $reason) {
        $pdf->Cell(10, 6, safe_text('•'), 0, 0, 'L');
        $pdf->Cell(0, 6, safe_text($reason), 0, 1, 'L');
    }
    
    // Save PDF
    $outputDir = __DIR__ . '/../../documents/trust';
    $filename = 'Company_Information_' . date('Ymd_His') . '.pdf';
    $outputPath = $outputDir . '/' . $filename;
    
    $pdf->Output('F', $outputPath);
    
    echo "✓ Company Information PDF created successfully!\n";
    echo "Location: $outputPath\n";
    echo "Size: " . number_format(filesize($outputPath)) . " bytes\n";
    
} catch (Exception $e) {
    echo "✗ Error generating PDF: " . $e->getMessage() . "\n";
    exit(1);
}
