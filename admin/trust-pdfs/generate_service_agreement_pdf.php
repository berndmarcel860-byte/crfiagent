<?php
/**
 * Service Agreement PDF Generator
 * 
 * Generates professional Service Agreement (Dienstleistungsvertrag) PDF
 * with transparent fee structure and legal terms
 * 
 * Usage:
 * php generate_service_agreement_pdf.php
 * 
 * Output: documents/trust/Service_Agreement_{timestamp}.pdf
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

if (!function_exists('safe_text')) {
    function safe_text($text) {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text ?? '');
    }
}

class ServiceAgreementPDF extends FPDF {
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
        $this->Cell(0, 5, safe_text('BaFin/FCA Ref: ' . $this->company['fca_reference'] . ' | ' . $this->company['brand_name']), 0, 1, 'C');
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
        } else {
            echo "Warning: System settings not found in database. Using defaults.\n";
        }
    } else {
        echo "Using default company data (database not available).\n";
    }
    
    $pdf = new ServiceAgreementPDF($company);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    /* TITLE */
    $pdf->SetFont('Helvetica', 'B', 20);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 15, safe_text('Dienstleistungsvertrag'), 0, 1, 'C');
    
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->SetTextColor(108, 117, 125);
    $pdf->Cell(0, 8, safe_text('Wiederherstellung verlorener Kryptowährungen'), 0, 1, 'C');
    
    $pdf->Ln(10);
    
    /* PARTIES */
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 8, safe_text('zwischen'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->Cell(0, 7, safe_text($company['brand_name']), 0, 1, 'L');
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell(0, 5, safe_text($company['company_address']), 0, 1, 'L');
    $pdf->Cell(0, 5, safe_text('BaFin/FCA Referenz: ' . $company['fca_reference']), 0, 1, 'L');
    
    $pdf->Ln(3);
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 8, safe_text('und dem Auftraggeber (nachfolgend "Kunde")'), 0, 1, 'L');
    
    $pdf->Ln(8);
    
    /* SECTION 1: LEISTUNGSUMFANG */
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 8, safe_text('§1 Leistungsumfang'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    
    $pdf->MultiCell(0, 5, safe_text(
        '1.1 Der Dienstleister bietet KI-gestützte Blockchain-Analyse zur Identifizierung ' .
        'und Wiederherstellung verlorener oder gestohlener Kryptowährungen.'
    ));
    $pdf->Ln(2);
    
    $pdf->MultiCell(0, 5, safe_text(
        '1.2 Die Dienstleistung umfasst: Blockchain-Tracing, Muster-Erkennung, ' .
        'Identifizierung von Betrugsplattformen, und Wiederherstellungsverfahren.'
    ));
    $pdf->Ln(2);
    
    $pdf->MultiCell(0, 5, safe_text(
        '1.3 Der Kunde erhält während des gesamten Prozesses transparente Updates ' .
        'über den Fortschritt der Wiederherstellung.'
    ));
    
    $pdf->Ln(8);
    
    /* SECTION 2: GEBÜHRENSTRUKTUR */
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 8, safe_text('§2 Gebührenstruktur'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    
    $pdf->MultiCell(0, 5, safe_text(
        '2.1 Die Dienstleistung ist für den Kunden KOSTENLOS bis zur erfolgreichen Wiederherstellung.'
    ));
    $pdf->Ln(2);
    
    // Highlight box for fee
    $pdf->SetFillColor(255, 243, 205);
    $pdf->SetDrawColor(255, 193, 7);
    $pdf->Rect(30, $pdf->GetY(), 150, 20, 'FD');
    
    $yPos = $pdf->GetY();
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->SetXY(35, $yPos + 5);
    $pdf->Cell(0, 6, safe_text('Erfolgsgebühr: 3% des Auszahlungsbetrags'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetX(35);
    $pdf->Cell(0, 6, safe_text('Die Gebühr wird automatisch vom Auszahlungsbetrag abgezogen.'), 0, 1, 'L');
    
    $pdf->SetY($yPos + 25);
    
    $pdf->MultiCell(0, 5, safe_text(
        '2.2 Es gibt KEINE Vorauszahlungen, keine versteckten Kosten, und keine Bearbeitungsgebühren.'
    ));
    $pdf->Ln(2);
    
    $pdf->MultiCell(0, 5, safe_text(
        '2.3 Die Gebühr fällt NUR an, wenn eine erfolgreiche Wiederherstellung erfolgt ist ' .
        'und der Kunde eine Auszahlung beantragt.'
    ));
    
    $pdf->Ln(8);
    
    /* SECTION 3: PFLICHTEN */
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 8, safe_text('§3 Pflichten der Parteien'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    
    $pdf->MultiCell(0, 5, safe_text(
        '3.1 Der Dienstleister verpflichtet sich zur sorgfältigen Durchführung der Blockchain-Analyse ' .
        'unter Einsatz modernster KI-Technologie.'
    ));
    $pdf->Ln(2);
    
    $pdf->MultiCell(0, 5, safe_text(
        '3.2 Der Kunde verpflichtet sich zur vollständigen und wahrheitsgemäßen Angabe aller ' .
        'relevanten Informationen bezüglich des Verlusts.'
    ));
    $pdf->Ln(2);
    
    $pdf->MultiCell(0, 5, safe_text(
        '3.3 Der Kunde verpflichtet sich zur Verifizierung seiner Wallet-Adresse mittels Satoshi-Test.'
    ));
    
    $pdf->Ln(8);
    
    /* SECTION 4: DATENSCHUTZ */
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 8, safe_text('§4 Datenschutz'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    
    $pdf->MultiCell(0, 5, safe_text(
        '4.1 Der Dienstleister verpflichtet sich zur Einhaltung der GDPR (Datenschutz-Grundverordnung) ' .
        'und aller relevanten Datenschutzbestimmungen.'
    ));
    $pdf->Ln(2);
    
    $pdf->MultiCell(0, 5, safe_text(
        '4.2 Kundendaten werden ausschließlich für die Wiederherstellungsdienstleistung verwendet ' .
        'und nicht an Dritte weitergegeben.'
    ));
    
    $pdf->Ln(8);
    
    /* SECTION 5: HAFTUNG */
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 8, safe_text('§5 Haftung'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    
    $pdf->MultiCell(0, 5, safe_text(
        '5.1 Der Dienstleister haftet nicht für die Wiederherstellbarkeit der verlorenen Mittel, ' .
        'da diese von externen Faktoren (Blockchain-Status, Betrugsplattformen, etc.) abhängt.'
    ));
    $pdf->Ln(2);
    
    $pdf->MultiCell(0, 5, safe_text(
        '5.2 Der Dienstleister verpflichtet sich zur sorgfältigen Durchführung der Analyse ' .
        'nach bestem Wissen und Gewissen.'
    ));
    
    $pdf->Ln(8);
    
    /* NEW PAGE for additional terms */
    $pdf->AddPage();
    
    /* SECTION 6: VERTRAGSDAUER */
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 8, safe_text('§6 Vertragsdauer und Kündigung'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    
    $pdf->MultiCell(0, 5, safe_text(
        '6.1 Der Vertrag beginnt mit der Registrierung und endet mit der erfolgreichen ' .
        'Wiederherstellung oder nach 12 Monaten ohne Erfolg.'
    ));
    $pdf->Ln(2);
    
    $pdf->MultiCell(0, 5, safe_text(
        '6.2 Beide Parteien können den Vertrag jederzeit schriftlich kündigen.'
    ));
    
    $pdf->Ln(8);
    
    /* SIGNATURE SECTION */
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 8, safe_text('Unterschriften'), 0, 1, 'L');
    
    $pdf->Ln(5);
    
    // Company signature
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->Cell(85, 6, safe_text('Für ' . $company['brand_name'] . ':'), 0, 1, 'L');
    $pdf->Ln(15);
    $pdf->Line(20, $pdf->GetY(), 95, $pdf->GetY());
    $pdf->Ln(2);
    $pdf->SetFont('Helvetica', 'I', 9);
    $pdf->Cell(0, 5, safe_text('Unterschrift und Datum'), 0, 1, 'L');
    
    $pdf->Ln(10);
    
    // Customer signature
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell(85, 6, safe_text('Kunde:'), 0, 1, 'L');
    $pdf->Ln(15);
    $pdf->Line(20, $pdf->GetY(), 95, $pdf->GetY());
    $pdf->Ln(2);
    $pdf->SetFont('Helvetica', 'I', 9);
    $pdf->Cell(0, 5, safe_text('Unterschrift und Datum'), 0, 1, 'L');
    
    // Save PDF
    $outputDir = __DIR__ . '/../../documents/trust';
    $filename = 'Service_Agreement_' . date('Ymd_His') . '.pdf';
    $outputPath = $outputDir . '/' . $filename;
    
    $pdf->Output('F', $outputPath);
    
    echo "✓ Service Agreement PDF created successfully!\n";
    echo "Location: $outputPath\n";
    echo "Size: " . number_format(filesize($outputPath)) . " bytes\n";
    
} catch (Exception $e) {
    echo "✗ Error generating PDF: " . $e->getMessage() . "\n";
    exit(1);
}
