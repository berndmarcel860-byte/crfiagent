<?php
/**
 * BaFin License Verification PDF Generator
 * 
 * Generates a professional PDF document that clients can use to verify
 * the company's BaFin license independently on www.bafin.de
 * 
 * Usage:
 * php generate_bafin_verification_pdf.php
 * 
 * Output: documents/trust/BaFin_Verification_{timestamp}.pdf
 */

declare(strict_types=1);

// Autoload
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Error: Composer vendor/autoload.php not found. Run: composer install\n");
}
require_once $autoloadPath;

use setasign\Fpdi\FpdfTpl as FPDF;

// Database connection (with fallback)
$dbAvailable = false;

// Check if PDO MySQL driver is available
if (!extension_loaded('pdo_mysql')) {
    echo "Warning: PDO MySQL driver not found. Using default values.\n";
    echo "To install: sudo apt-get install php-mysql (or php8.x-mysql)\n";
    echo "Then restart PHP: sudo systemctl restart php-fpm\n\n";
} else {
    try {
        require_once __DIR__ . '/../../config.php';
        
        // Verify $pdo was created by config.php
        if (isset($pdo) && $pdo instanceof PDO) {
            $dbAvailable = true;
            echo "✓ Database connection successful.\n";
        } else {
            echo "Warning: Database connection failed (PDO not initialized).\n";
            echo "Using default values.\n\n";
        }
    } catch (Exception $e) {
        echo "Warning: Database connection error: " . $e->getMessage() . "\n";
        echo "Using default values.\n\n";
    }
}

// Helper function for safe text
if (!function_exists('safe_text')) {
    function safe_text($text) {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text ?? '');
    }
}

/* =========================
   Professional PDF Class
   ========================= */
class BaFinVerificationPDF extends FPDF {
    private array $company = [];
    
    function __construct($company) {
        parent::__construct();
        $this->company = $company;
    }
    
    function Header() {
        // Company name in header
        $this->SetFont('Helvetica', 'B', 16);
        $this->SetTextColor(28, 41, 69);
        $this->SetXY(20, 15);
        $this->Cell(0, 10, safe_text($this->company['brand_name']), 0, 1, 'R');
        
        // Divider line
        $this->SetDrawColor(222, 226, 230);
        $this->Line(20, 32, 190, 32);
        
        $this->Ln(10);
    }
    
    function Footer() {
        $this->SetY(-20);
        $this->SetFont('Helvetica', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        
        // Company info in footer
        $footerText = safe_text($this->company['brand_name'] . ' | ' . $this->company['company_address']);
        $this->Cell(0, 5, $footerText, 0, 1, 'C');
        
        $footerText2 = 'E: ' . $this->company['contact_email'] . ' | T: ' . $this->company['contact_phone'];
        $this->Cell(0, 5, safe_text($footerText2), 0, 1, 'C');
        
        // Page number
        $this->Cell(0, 5, 'Seite ' . $this->PageNo(), 0, 0, 'C');
    }
}

/* =========================
   Main PDF Generation
   ========================= */

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
    
    // Fetch from database if available
    if ($dbAvailable && isset($pdo)) {
        try {
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
                echo "✓ Using actual company data from database:\n";
                echo "  Brand: " . $company['brand_name'] . "\n";
                echo "  FCA Reference: " . $company['fca_reference'] . "\n";
                echo "  Address: " . substr($company['company_address'], 0, 50) . "...\n";
            } else {
                echo "Warning: System settings not found in database (id=1). Using defaults.\n";
            }
        } catch (Exception $e) {
            echo "Warning: Error fetching system settings: " . $e->getMessage() . "\n";
            echo "Using default values.\n";
        }
    } else {
        echo "Using default company data (database not available).\n";
    }
    
    // Create PDF
    $pdf = new BaFinVerificationPDF($company);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    // Set professional fonts
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->SetTextColor(33, 37, 41);
    
    /* TITLE SECTION */
    $pdf->SetFont('Helvetica', 'B', 20);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 15, safe_text('BaFin-Lizenz Verifizierung'), 0, 1, 'C');
    
    $pdf->SetFont('Helvetica', '', 12);
    $pdf->SetTextColor(108, 117, 125);
    $pdf->Cell(0, 8, safe_text('Offizielle Lizenzinformationen und Verifizierungsanleitung'), 0, 1, 'C');
    
    $pdf->Ln(10);
    
    /* LICENSE BOX */
    $pdf->SetFillColor(232, 245, 233);
    $pdf->SetDrawColor(76, 175, 80);
    $pdf->SetLineWidth(0.5);
    $pdf->Rect(30, $pdf->GetY(), 150, 35, 'FD');
    
    $yStart = $pdf->GetY();
    
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->SetTextColor(76, 175, 80);
    $pdf->SetXY(40, $yStart + 5);
    $pdf->Cell(0, 6, safe_text('✓ LIZENZIERT'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', 'B', 14);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->SetXY(40, $yStart + 13);
    $pdf->Cell(0, 8, safe_text('BaFin/FCA Referenznummer'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', 'B', 18);
    $pdf->SetTextColor(76, 175, 80);
    $pdf->SetXY(40, $yStart + 23);
    $pdf->Cell(0, 8, safe_text($company['fca_reference']), 0, 1, 'L');
    
    $pdf->SetY($yStart + 40);
    $pdf->Ln(5);
    
    /* COMPANY INFORMATION */
    $pdf->SetFont('Helvetica', 'B', 14);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 10, safe_text('Lizenzinhaber'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->Cell(0, 7, safe_text($company['brand_name']), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->Cell(0, 6, safe_text($company['company_address']), 0, 1, 'L');
    $pdf->Cell(0, 6, safe_text('E-Mail: ' . $company['contact_email']), 0, 1, 'L');
    $pdf->Cell(0, 6, safe_text('Telefon: ' . $company['contact_phone']), 0, 1, 'L');
    
    $pdf->Ln(10);
    
    /* VERIFICATION INSTRUCTIONS */
    $pdf->SetFont('Helvetica', 'B', 14);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 10, safe_text('So verifizieren Sie unsere Lizenz'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->SetTextColor(33, 37, 41);
    
    // Step 1
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->Cell(10, 8, safe_text('1.'), 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->MultiCell(0, 8, safe_text('Besuchen Sie die offizielle BaFin-Website:'));
    
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->SetTextColor(0, 102, 204);
    $pdf->SetX(30);
    $pdf->Cell(0, 6, safe_text('https://www.bafin.de/'), 0, 1, 'L');
    $pdf->SetTextColor(33, 37, 41);
    
    $pdf->Ln(3);
    
    // Step 2
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->Cell(10, 8, safe_text('2.'), 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->MultiCell(0, 8, safe_text('Navigieren Sie zu "Unternehmensdatenbank" oder "Lizenzregister"'));
    
    $pdf->Ln(3);
    
    // Step 3
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->Cell(10, 8, safe_text('3.'), 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->MultiCell(0, 8, safe_text('Suchen Sie nach:'));
    
    $pdf->SetX(30);
    $pdf->Cell(50, 6, safe_text('Firmenname:'), 0, 0, 'L');
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->Cell(0, 6, safe_text($company['brand_name']), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->SetX(30);
    $pdf->Cell(50, 6, safe_text('Lizenz-Nummer:'), 0, 0, 'L');
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->Cell(0, 6, safe_text($company['fca_reference']), 0, 1, 'L');
    
    $pdf->Ln(3);
    
    // Step 4
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->Cell(10, 8, safe_text('4.'), 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->MultiCell(0, 8, safe_text('Verifizieren Sie folgende Informationen:'));
    
    $pdf->SetX(30);
    $pdf->Cell(0, 6, safe_text('✓ Lizenzstatus: Aktiv'), 0, 1, 'L');
    $pdf->SetX(30);
    $pdf->Cell(0, 6, safe_text('✓ Lizenzinhaber: ' . $company['brand_name']), 0, 1, 'L');
    $pdf->SetX(30);
    $pdf->Cell(0, 6, safe_text('✓ Lizenzart: Finanzdienstleistung'), 0, 1, 'L');
    
    $pdf->Ln(10);
    
    /* WARNING ABOUT SCAMS */
    $pdf->SetFillColor(255, 243, 205);
    $pdf->SetDrawColor(255, 193, 7);
    $pdf->Rect(20, $pdf->GetY(), 170, 50, 'FD');
    
    $yStart = $pdf->GetY();
    
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(133, 100, 4);
    $pdf->SetXY(25, $yStart + 5);
    $pdf->Cell(0, 7, safe_text('⚠ WARNUNG VOR BETRUG'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->SetX(25);
    $pdf->MultiCell(160, 5, safe_text(
        'Viele Betrüger verwenden ähnliche Strukturen wie legitime Unternehmen. ' .
        'Verifizieren Sie IMMER die BaFin-Lizenz direkt auf der offiziellen BaFin-Website. ' .
        'Echte Unternehmen haben eine verifizierbare Lizenz. Betrüger können keine ' .
        'gültige BaFin-Lizenz vorweisen.'
    ));
    
    $pdf->SetY($yStart + 55);
    $pdf->Ln(5);
    
    /* COMPARISON TABLE */
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 10, safe_text('Echte Unternehmen vs. Betrüger'), 0, 1, 'L');
    
    // Table header
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->SetFillColor(232, 245, 233);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->Cell(85, 8, safe_text('Legitime Unternehmen'), 1, 0, 'C', true);
    $pdf->Cell(85, 8, safe_text('Betrüger'), 1, 1, 'C', true);
    
    // Table rows
    $pdf->SetFont('Helvetica', '', 9);
    
    $rows = [
        ['✓ Verifizierbare BaFin-Lizenz', '✗ Keine oder gefälschte Lizenz'],
        ['✓ Physisches Büro & Adresse', '✗ Nur Online-Kontakt'],
        ['✓ Transparente Gebührenstruktur', '✗ Versteckte Kosten'],
        ['✓ KEINE Vorauszahlung erforderlich', '✗ Vorauszahlung gefordert'],
        ['✓ Rechtliche Verträge', '✗ Keine schriftlichen Vereinbarungen'],
        ['✓ GDPR-konform', '✗ Datenschutz unklar'],
    ];
    
    foreach ($rows as $row) {
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Cell(85, 7, safe_text($row[0]), 1, 0, 'L', true);
        
        $pdf->SetFillColor(255, 245, 245);
        $pdf->Cell(85, 7, safe_text($row[1]), 1, 1, 'L', true);
    }
    
    $pdf->Ln(10);
    
    /* IMPORTANT NOTES */
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 10, safe_text('Wichtige Hinweise'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    
    $notes = [
        'Unsere Dienstleistung ist KOSTENLOS bis zur erfolgreichen Wiederherstellung.',
        'Sie zahlen NUR bei erfolgreicher Wiederherstellung (3% des Auszahlungsbetrags).',
        'Wir verlangen NIEMALS Vorauszahlungen oder "Bearbeitungsgebühren".',
        'Ihre Daten sind nach GDPR-Standards geschützt.',
        'Alle Kommunikation erfolgt über offizielle E-Mail-Adressen.',
    ];
    
    foreach ($notes as $note) {
        $pdf->Cell(10, 6, safe_text('•'), 0, 0, 'L');
        $pdf->MultiCell(0, 6, safe_text($note));
    }
    
    $pdf->Ln(10);
    
    /* CONTACT BOX */
    $pdf->SetFillColor(239, 246, 255);
    $pdf->SetDrawColor(0, 102, 204);
    $pdf->Rect(20, $pdf->GetY(), 170, 25, 'FD');
    
    $yStart = $pdf->GetY();
    
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->SetTextColor(0, 51, 102);
    $pdf->SetXY(25, $yStart + 5);
    $pdf->Cell(0, 6, safe_text('Bei Fragen oder Zweifeln kontaktieren Sie uns:'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->SetX(25);
    $pdf->Cell(0, 6, safe_text('E-Mail: ' . $company['contact_email']), 0, 1, 'L');
    $pdf->SetX(25);
    $pdf->Cell(0, 6, safe_text('Telefon: ' . $company['contact_phone']), 0, 1, 'L');
    
    $pdf->SetY($yStart + 30);
    $pdf->Ln(5);
    
    /* FOOTER TEXT */
    $pdf->SetFont('Helvetica', 'I', 9);
    $pdf->SetTextColor(108, 117, 125);
    $pdf->Cell(0, 6, safe_text('Erstellt am: ' . date('d.m.Y')), 0, 1, 'C');
    $pdf->Cell(0, 6, safe_text('Gültig zum Zeitpunkt der Erstellung'), 0, 1, 'C');
    
    // Save PDF
    $outputDir = __DIR__ . '/../../documents/trust';
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }
    
    $filename = 'BaFin_Verification_' . date('Ymd_His') . '.pdf';
    $outputPath = $outputDir . '/' . $filename;
    
    $pdf->Output('F', $outputPath);
    
    echo "✓ BaFin Verification PDF created successfully!\n";
    echo "Location: $outputPath\n";
    echo "Size: " . number_format(filesize($outputPath)) . " bytes\n";
    echo "\nCompany Data Used:\n";
    echo "- Brand: {$company['brand_name']}\n";
    echo "- FCA Reference: {$company['fca_reference']}\n";
    echo "- Address: {$company['company_address']}\n";
    
} catch (Exception $e) {
    echo "✗ Error generating PDF: " . $e->getMessage() . "\n";
    exit(1);
}
