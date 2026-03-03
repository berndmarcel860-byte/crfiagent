<?php
/**
 * Onboarding Guide PDF Generator
 * 
 * Generates professional Onboarding Guide with step-by-step instructions
 * 
 * Usage:
 * php generate_onboarding_guide_pdf.php
 * 
 * Output: documents/trust/Onboarding_Guide_{timestamp}.pdf
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

class OnboardingGuidePDF extends FPDF {
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
        $this->Cell(0, 5, safe_text($this->company['brand_name'] . ' - Onboarding-Leitfaden'), 0, 1, 'C');
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
    
    $pdf = new OnboardingGuidePDF($company);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    /* TITLE */
    $pdf->SetFont('Helvetica', 'B', 20);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 15, safe_text('Onboarding-Leitfaden'), 0, 1, 'C');
    
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->SetTextColor(108, 117, 125);
    $pdf->Cell(0, 8, safe_text('Ihr Weg zur Wiederherstellung in 5 Schritten'), 0, 1, 'C');
    
    $pdf->Ln(10);
    
    /* STEP 1 */
    $pdf->SetFillColor(239, 246, 255);
    $pdf->SetDrawColor(0, 102, 204);
    $pdf->Rect(20, $pdf->GetY(), 170, 30, 'FD');
    
    $yPos = $pdf->GetY();
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(0, 102, 204);
    $pdf->SetXY(25, $yPos + 5);
    $pdf->Cell(0, 7, safe_text('Schritt 1: Registrierung und Einloggen'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->SetX(25);
    $pdf->MultiCell(160, 5, safe_text(
        'Verwenden Sie die erhaltenen Login-Daten. Nach dem ersten Login werden Sie ' .
        'aufgefordert, Ihr Passwort zu ändern.'
    ));
    
    $pdf->SetY($yPos + 35);
    $pdf->Ln(3);
    
    /* STEP 2 */
    $pdf->SetFillColor(232, 245, 233);
    $pdf->SetDrawColor(76, 175, 80);
    $pdf->Rect(20, $pdf->GetY(), 170, 30, 'FD');
    
    $yPos = $pdf->GetY();
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(76, 175, 80);
    $pdf->SetXY(25, $yPos + 5);
    $pdf->Cell(0, 7, safe_text('Schritt 2: Onboarding abschließen'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->SetX(25);
    $pdf->MultiCell(160, 5, safe_text(
        'Geben Sie Informationen über Ihren Verlust an: verlorene Summe, Plattform, ' .
        'Jahr des Verlusts, und Ihre Adressdaten.'
    ));
    
    $pdf->SetY($yPos + 35);
    $pdf->Ln(3);
    
    /* STEP 3 */
    $pdf->SetFillColor(255, 243, 205);
    $pdf->SetDrawColor(255, 193, 7);
    $pdf->Rect(20, $pdf->GetY(), 170, 35, 'FD');
    
    $yPos = $pdf->GetY();
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(255, 152, 0);
    $pdf->SetXY(25, $yPos + 5);
    $pdf->Cell(0, 7, safe_text('Schritt 3: KYC-Verifizierung'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->SetX(25);
    $pdf->MultiCell(160, 5, safe_text(
        'Laden Sie Ihre Identitätsdokumente hoch (Personalausweis oder Reisepass). ' .
        'Dies ist für rechtliche Compliance erforderlich und schützt beide Parteien.'
    ));
    
    $pdf->SetY($yPos + 40);
    $pdf->Ln(3);
    
    /* STEP 4 */
    $pdf->SetFillColor(255, 235, 238);
    $pdf->SetDrawColor(244, 67, 54);
    $pdf->Rect(20, $pdf->GetY(), 170, 35, 'FD');
    
    $yPos = $pdf->GetY();
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(244, 67, 54);
    $pdf->SetXY(25, $yPos + 5);
    $pdf->Cell(0, 7, safe_text('Schritt 4: Wallet-Verifizierung (Satoshi-Test)'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->SetX(25);
    $pdf->MultiCell(160, 5, safe_text(
        'Fügen Sie Ihre Krypto-Wallet hinzu. Der Admin setzt einen Testbetrag, ' .
        'den Sie senden müssen, um Eigentum nachzuweisen.'
    ));
    
    $pdf->SetY($yPos + 40);
    $pdf->Ln(3);
    
    /* STEP 5 */
    $pdf->SetFillColor(232, 234, 246);
    $pdf->SetDrawColor(103, 58, 183);
    $pdf->Rect(20, $pdf->GetY(), 170, 30, 'FD');
    
    $yPos = $pdf->GetY();
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor(103, 58, 183);
    $pdf->SetXY(25, $yPos + 5);
    $pdf->Cell(0, 7, safe_text('Schritt 5: KI-Analyse startet automatisch'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->SetX(25);
    $pdf->MultiCell(160, 5, safe_text(
        'Nach Abschluss beginnt unser KI-Algorithmus mit der Blockchain-Analyse. ' .
        'Sie erhalten Updates über den Fortschritt Ihres Falls.'
    ));
    
    $pdf->SetY($yPos + 35);
    $pdf->Ln(5);
    
    /* FAQ */
    $pdf->AddPage();
    
    $pdf->SetFont('Helvetica', 'B', 14);
    $pdf->SetTextColor(28, 41, 69);
    $pdf->Cell(0, 10, safe_text('Häufig gestellte Fragen (FAQ)'), 0, 1, 'L');
    
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    
    $faqs = [
        ['Wie lange dauert der Prozess?', 'Die KI-Analyse beginnt sofort nach Verifizierung. Die Wiederherstellung kann je nach Komplexität 2-12 Wochen dauern.'],
        ['Muss ich im Voraus bezahlen?', 'NEIN. Sie zahlen NUR bei erfolgreicher Wiederherstellung (3% des Auszahlungsbetrags). Keine Vorauszahlungen.'],
        ['Ist meine Wallet sicher?', 'Ja. Der Satoshi-Test verifiziert nur Eigentum. Wir haben KEINEN Zugriff auf Ihre Wallet.'],
        ['Was ist die Erfolgsrate?', 'Unsere KI identifiziert Betrug in ' . $company['success_rate'] . '% der Fälle. Wiederherstellung hängt von vielen Faktoren ab.'],
    ];
    
    foreach ($faqs as $faq) {
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Cell(10, 6, safe_text('Q:'), 0, 0, 'L');
        $pdf->MultiCell(0, 6, safe_text($faq[0]));
        
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(10, 6, safe_text('A:'), 0, 0, 'L');
        $pdf->MultiCell(0, 6, safe_text($faq[1]));
        $pdf->Ln(3);
    }
    
    // Save PDF
    $outputDir = __DIR__ . '/../../documents/trust';
    $filename = 'Onboarding_Guide_' . date('Ymd_His') . '.pdf';
    $outputPath = $outputDir . '/' . $filename;
    
    $pdf->Output('F', $outputPath);
    
    echo "✓ Onboarding Guide PDF created successfully!\n";
    echo "Location: $outputPath\n";
    echo "Size: " . number_format(filesize($outputPath)) . " bytes\n";
    
} catch (Exception $e) {
    echo "✗ Error generating PDF: " . $e->getMessage() . "\n";
    exit(1);
}
