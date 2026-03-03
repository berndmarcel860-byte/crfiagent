<?php
/**
 * BaFin License Verification HTML Document Generator
 * 
 * Generates HTML that can be printed to PDF or sent as HTML email attachment
 * Uses data from system_settings table, fca_reference_number = BaFin reference
 * 
 * Usage:
 * php generate_bafin_verification_html.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config.php';

try {
    // Fetch system settings
    $stmt = $pdo->query("SELECT * FROM system_settings WHERE id = 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$settings) {
        throw new Exception("System settings not found");
    }
    
    $brandName = htmlspecialchars($settings['brand_name'] ?? 'CryptoFinanz');
    $companyAddress = htmlspecialchars($settings['company_address'] ?? 'Davidson House Forbury Square, Reading, RG1 3EU');
    $contactEmail = htmlspecialchars($settings['contact_email'] ?? 'support@cryptofinanze.de');
    $contactPhone = htmlspecialchars($settings['contact_phone'] ?? '+44 (0) 20 1234 5678');
    $fcaReference = htmlspecialchars($settings['fca_reference_number'] ?? '910584');
    $siteUrl = htmlspecialchars($settings['site_url'] ?? 'https://cryptofinanze.de');
    
    $currentDate = date('d.m.Y');
    
    // Start building HTML
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BaFin-Lizenz Verifizierung - <?php echo $brandName; ?></title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: #212529;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: right;
            color: #1c2945;
            font-size: 24px;
            font-weight: bold;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 30px;
        }
        .title {
            text-align: center;
            color: #1c2945;
            font-size: 32px;
            font-weight: bold;
            margin: 20px 0;
        }
        .subtitle {
            text-align: center;
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .license-box {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            border: 3px solid #4caf50;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin: 30px auto;
            max-width: 600px;
        }
        .license-badge {
            color: #4caf50;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .license-label {
            font-size: 18px;
            color: #1c2945;
            font-weight: bold;
            margin: 10px 0;
        }
        .license-number {
            font-size: 32px;
            color: #4caf50;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 15px 0;
        }
        .section-title {
            color: #1c2945;
            font-size: 20px;
            font-weight: bold;
            margin: 25px 0 15px 0;
            border-bottom: 2px solid #1c2945;
            padding-bottom: 5px;
        }
        .company-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .company-info strong {
            color: #1c2945;
        }
        .verification-step {
            margin: 15px 0;
            padding-left: 30px;
        }
        .verification-step strong {
            color: #1c2945;
            font-size: 16px;
        }
        .bafin-link {
            color: #0066cc;
            font-weight: bold;
            font-size: 16px;
            text-decoration: none;
            padding-left: 20px;
        }
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .warning-title {
            color: #856404;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .comparison-table th {
            background: #e8f5e9;
            padding: 12px;
            border: 1px solid #4caf50;
            font-weight: bold;
            text-align: center;
        }
        .comparison-table td {
            padding: 10px;
            border: 1px solid #dee2e6;
        }
        .comparison-table .legit {
            background: #f8f9fa;
        }
        .comparison-table .scam {
            background: #fff5f5;
        }
        .notes-list {
            list-style: none;
            padding: 0;
        }
        .notes-list li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }
        .notes-list li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #1c2945;
            font-weight: bold;
            font-size: 18px;
        }
        .contact-box {
            background: #eff6ff;
            border: 2px solid #0066cc;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .contact-box strong {
            color: #003366;
        }
        .footer-text {
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            margin-top: 40px;
            font-style: italic;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header"><?php echo $brandName; ?></div>
    
    <div class="title">BaFin-Lizenz Verifizierung</div>
    <div class="subtitle">Offizielle Lizenzinformationen und Verifizierungsanleitung</div>
    
    <div class="license-box">
        <div class="license-badge">✓ LIZENZIERT</div>
        <div class="license-label">BaFin/FCA Referenznummer</div>
        <div class="license-number"><?php echo $fcaReference; ?></div>
    </div>
    
    <div class="section-title">Lizenzinhaber</div>
    <div class="company-info">
        <strong><?php echo $brandName; ?></strong><br>
        <?php echo $companyAddress; ?><br>
        E-Mail: <?php echo $contactEmail; ?><br>
        Telefon: <?php echo $contactPhone; ?>
    </div>
    
    <div class="section-title">So verifizieren Sie unsere Lizenz</div>
    
    <div class="verification-step">
        <strong>1.</strong> Besuchen Sie die offizielle BaFin-Website:<br>
        <a href="https://www.bafin.de/" class="bafin-link">https://www.bafin.de/</a>
    </div>
    
    <div class="verification-step">
        <strong>2.</strong> Navigieren Sie zu "Unternehmensdatenbank" oder "Lizenzregister"
    </div>
    
    <div class="verification-step">
        <strong>3.</strong> Suchen Sie nach:<br>
        <div style="padding-left: 20px; margin-top: 10px;">
            <strong>Firmenname:</strong> <?php echo $brandName; ?><br>
            <strong>Lizenz-Nummer:</strong> <?php echo $fcaReference; ?>
        </div>
    </div>
    
    <div class="verification-step">
        <strong>4.</strong> Verifizieren Sie folgende Informationen:<br>
        <div style="padding-left: 20px; margin-top: 10px;">
            ✓ Lizenzstatus: Aktiv<br>
            ✓ Lizenzinhaber: <?php echo $brandName; ?><br>
            ✓ Lizenzart: Finanzdienstleistung
        </div>
    </div>
    
    <div class="warning-box">
        <div class="warning-title">⚠ WARNUNG VOR BETRUG</div>
        <p>
            Viele Betrüger verwenden ähnliche Strukturen wie legitime Unternehmen. 
            Verifizieren Sie <strong>IMMER</strong> die BaFin-Lizenz direkt auf der offiziellen BaFin-Website. 
            Echte Unternehmen haben eine verifizierbare Lizenz. Betrüger können keine 
            gültige BaFin-Lizenz vorweisen.
        </p>
    </div>
    
    <div class="section-title">Echte Unternehmen vs. Betrüger</div>
    
    <table class="comparison-table">
        <thead>
            <tr>
                <th>Legitime Unternehmen</th>
                <th>Betrüger</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="legit">✓ Verifizierbare BaFin-Lizenz</td>
                <td class="scam">✗ Keine oder gefälschte Lizenz</td>
            </tr>
            <tr>
                <td class="legit">✓ Physisches Büro & Adresse</td>
                <td class="scam">✗ Nur Online-Kontakt</td>
            </tr>
            <tr>
                <td class="legit">✓ Transparente Gebührenstruktur</td>
                <td class="scam">✗ Versteckte Kosten</td>
            </tr>
            <tr>
                <td class="legit">✓ KEINE Vorauszahlung erforderlich</td>
                <td class="scam">✗ Vorauszahlung gefordert</td>
            </tr>
            <tr>
                <td class="legit">✓ Rechtliche Verträge</td>
                <td class="scam">✗ Keine schriftlichen Vereinbarungen</td>
            </tr>
            <tr>
                <td class="legit">✓ GDPR-konform</td>
                <td class="scam">✗ Datenschutz unklar</td>
            </tr>
        </tbody>
    </table>
    
    <div class="section-title">Wichtige Hinweise</div>
    
    <ul class="notes-list">
        <li>Unsere Dienstleistung ist <strong>KOSTENLOS</strong> bis zur erfolgreichen Wiederherstellung.</li>
        <li>Sie zahlen <strong>NUR</strong> bei erfolgreicher Wiederherstellung (3% des Auszahlungsbetrags).</li>
        <li>Wir verlangen <strong>NIEMALS</strong> Vorauszahlungen oder "Bearbeitungsgebühren".</li>
        <li>Ihre Daten sind nach <strong>GDPR-Standards</strong> geschützt.</li>
        <li>Alle Kommunikation erfolgt über offizielle E-Mail-Adressen.</li>
    </ul>
    
    <div class="contact-box">
        <strong>Bei Fragen oder Zweifeln kontaktieren Sie uns:</strong><br><br>
        E-Mail: <?php echo $contactEmail; ?><br>
        Telefon: <?php echo $contactPhone; ?><br>
        Website: <a href="<?php echo $siteUrl; ?>"><?php echo $siteUrl; ?></a>
    </div>
    
    <div class="footer-text">
        Erstellt am: <?php echo $currentDate; ?><br>
        Gültig zum Zeitpunkt der Erstellung<br>
        BaFin/FCA Referenz: <?php echo $fcaReference; ?>
    </div>
    
    <div class="footer-text">
        Erstellt am: <?php echo $currentDate; ?><br>
        Gültig zum Zeitpunkt der Erstellung<br>
        BaFin/FCA Referenz: <?php echo $fcaReference; ?>
    </div>
    
    <div class="no-print" style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center;">
        <p><strong>Drucken Sie dieses Dokument als PDF:</strong></p>
        <p>Datei → Drucken → Als PDF speichern</p>
    </div>
</body>
</html>
<?php
    
    $html = ob_get_clean();
    
    // Save to file
    $outputDir = __DIR__ . '/../../documents/trust';
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }
    
    $filename = 'BaFin_Verification_' . date('Ymd_His') . '.html';
    $outputPath = $outputDir . '/' . $filename;
    
    file_put_contents($outputPath, $html);
    
    echo "✓ BaFin Verification HTML created successfully!\n";
    echo "Location: $outputPath\n";
    echo "Size: " . number_format(filesize($outputPath)) . " bytes\n";
    echo "\nCompany Data Used:\n";
    echo "- Brand: $brandName\n";
    echo "- FCA/BaFin Reference: $fcaReference\n";
    echo "- Address: $companyAddress\n";
    echo "\nTo convert to PDF: Open in browser and print as PDF\n";
    
} catch (Exception $e) {
    echo "✗ Error generating document: " . $e->getMessage() . "\n";
    exit(1);
}
