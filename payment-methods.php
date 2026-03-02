<?php 
/**
 * Zahlungsmethoden Verwaltungsseite
 * Ermöglicht Benutzern die Verwaltung ihrer Fiat- und Krypto-Zahlungsmethoden
 * Aktualisiert: 2026-03-01 - Modernes professionelles Design mit Tabellen
 */
include 'header.php'; 
?>

<style>
/* Moderne Tabellen-Styles */
.payment-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.payment-table thead {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
}

.payment-table thead th {
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 3px solid #1e3a8a;
}

.payment-table tbody tr {
    border-bottom: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.payment-table tbody tr:hover {
    background: #f8f9fc;
    transform: scale(1.01);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.payment-table tbody td {
    padding: 16px 20px;
    vertical-align: middle;
    font-size: 14px;
}

.payment-table tbody tr:last-child {
    border-bottom: none;
}

/* Status-Badges */
.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.verified {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.status-badge.pending {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
}

.status-badge.failed {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
}

.status-badge.not-verified {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
}

/* Standard-Badge */
.default-badge {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

/* Aktions-Buttons */
.action-btn {
    padding: 6px 12px;
    margin: 0 2px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.action-btn.btn-primary {
    background: linear-gradient(135deg, #4e73df, #224abe);
    color: white;
}

.action-btn.btn-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.action-btn.btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.action-btn.btn-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

/* Seitenkopf */
.page-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    padding: 32px;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: 0 4px 16px rgba(78, 115, 223, 0.3);
}

.page-header h2 {
    font-size: 2em;
    font-weight: 700;
    margin: 0 0 8px 0;
}

.page-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 1.1em;
}

/* Add-Button */
.add-method-btn {
    width: 100%;
    padding: 16px;
    margin-top: 16px;
    border: 2px dashed #cbd5e0;
    background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%);
    color: #4a5568;
    border-radius: 12px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.add-method-btn:hover {
    border-style: solid;
    border-color: #4e73df;
    background: linear-gradient(135deg, #e7f1ff 0%, #f0f7ff 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(78, 115, 223, 0.15);
    color: #4e73df;
}

.add-method-btn i {
    font-size: 1.3em;
}

/* Karten-Container */
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    margin-bottom: 24px;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    padding: 20px;
    border-bottom: none;
}

.card-header h4 {
    margin: 0;
    font-size: 1.4em;
    font-weight: 700;
}

.card-body {
    padding: 0;
}

/* Info-Alert */
.info-alert {
    background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
    border: none;
    border-left: 5px solid #3b82f6;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(59, 130, 246, 0.1);
}

.info-alert h5 {
    color: #1e40af;
    font-weight: 700;
    margin-bottom: 12px;
}

.info-alert p {
    color: #1e40af;
    margin-bottom: 8px;
}

/* Leerzustand */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #718096;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    color: #cbd5e0;
}

.empty-state h5 {
    font-size: 1.3em;
    font-weight: 600;
    margin-bottom: 12px;
    color: #4a5568;
}

/* Ladezustand */
.loading-state {
    text-align: center;
    padding: 40px 20px;
}

.loading-state i {
    font-size: 48px;
    color: #4e73df;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Modal-Verbesserungen */
.modal-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    border-bottom: none;
}

.modal-title {
    font-weight: 700;
}

.modal-body {
    padding: 24px;
}

/* View Details Modal Styling */
.form-control-plaintext {
    font-size: 14px;
    font-weight: 500;
    color: #2d3748;
}

.form-control-plaintext code {
    background: #f7fafc;
    padding: 2px 6px;
    border-radius: 4px;
    color: #2d3748;
}

.btn-info {
    background: linear-gradient(135deg, #17a2b8, #117a8b);
    border: none;
    color: white;
}

.btn-info:hover {
    background: linear-gradient(135deg, #138496, #0f6674);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
}

#viewDetailsContent .row {
    margin: 0;
}

#viewDetailsContent .col-md-6,
#viewDetailsContent .col-md-4,
#viewDetailsContent .col-md-12 {
    padding: 0 8px;
}

.form-group label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
}

.form-control {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 14px;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
}

/* Badge in Header */
.badge-light {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .payment-table {
        font-size: 12px;
    }
    
    .payment-table thead th,
    .payment-table tbody td {
        padding: 12px 10px;
    }
    
    .action-btn {
        padding: 4px 8px;
        font-size: 11px;
    }
    
    .page-header {
        padding: 24px;
    }
    
    .page-header h2 {
        font-size: 1.6em;
    }
}

/* Methodentyp-Icons */
.method-icon {
    font-size: 1.3em;
    margin-right: 8px;
    color: #4e73df;
}

/* Datum-Formatierung */
.date-text {
    color: #6b7280;
    font-size: 13px;
}

</style>

<!-- Content Wrapper START -->
<div class="main-content">
    <div class="container-fluid">
        <div class="page-header">
            <h2><i class="fas fa-wallet"></i> Zahlungsmethoden</h2>
            <p>Verwalten Sie Ihre Fiat- und Kryptowährungs-Zahlungsmethoden sicher</p>
        </div>

        <!-- Info-Alert -->
        <div class="info-alert alert-dismissible fade show" role="alert">
            <h5>
                <i class="fas fa-shield-alt"></i> Über die Kryptowährungs-Wallet-Verifizierung
            </h5>
            <p class="mb-2">
                Kryptowährungs-Wallets erfordern eine Verifizierung durch einen <strong>Satoshi-Test</strong>, bevor sie für Auszahlungen verwendet werden können. 
                Dies ist eine Sicherheitsmaßnahme zum Nachweis des Wallet-Eigentums und zum Schutz Ihrer Gelder.
            </p>
            <p class="mb-0">
                <a href="satoshi-test-guide.php" class="btn btn-sm btn-primary">
                    <i class="fas fa-book-open"></i> Mehr über den Satoshi-Test erfahren
                </a>
            </p>
            <button type="button" class="close" data-dismiss="alert" aria-label="Schließen">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="row">
            <!-- Fiat-Zahlungsmethoden -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-university"></i> Bankkonten</h4>
                        <span class="badge badge-light" id="fiatCount">0</span>
                    </div>
                    <div class="card-body">
                        <div id="fiatMethodsContainer">
                            <div class="loading-state">
                                <i class="fas fa-spinner"></i>
                                <p>Lade Bankkonten...</p>
                            </div>
                        </div>
                        <button class="add-method-btn" onclick="showAddFiatModal()">
                            <i class="fas fa-plus-circle"></i>
                            <span>Bankkonto hinzufügen</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Krypto-Wallets -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fab fa-bitcoin"></i> Krypto-Wallets</h4>
                        <span class="badge badge-light" id="cryptoCount">0</span>
                    </div>
                    <div class="card-body">
                        <div id="cryptoMethodsContainer">
                            <div class="loading-state">
                                <i class="fas fa-spinner"></i>
                                <p>Lade Krypto-Wallets...</p>
                            </div>
                        </div>
                        <button class="add-method-btn" onclick="showAddCryptoModal()">
                            <i class="fas fa-plus-circle"></i>
                            <span>Krypto-Wallet hinzufügen</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Content Wrapper END -->

<!-- Fiat-Zahlungsmethode hinzufügen Modal -->
<div class="modal fade" id="addFiatModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-university"></i> Bankkonto hinzufügen</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addFiatForm">
                <div class="modal-body">
                    <input type="hidden" name="type" value="fiat">
                    
                    <div class="form-group">
                        <label>Zahlungsmethode <span class="text-danger">*</span></label>
                        <select class="form-control" name="payment_method" required>
                            <option value="">Bitte wählen...</option>
                            <option value="Bank Transfer">Banküberweisung (SEPA)</option>
                            <option value="Wire Transfer">Auslandsüberweisung</option>
                            <option value="Credit Card">Kredit-/Debitkarte</option>
                            <option value="PayPal">PayPal</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Kontoinhaber <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="account_holder" 
                               placeholder="Max Mustermann" required>
                    </div>

                    <div class="form-group">
                        <label>Bankname <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="bank_name" 
                               placeholder="Sparkasse München" required>
                    </div>

                    <div class="form-group">
                        <label>IBAN <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="iban" 
                               placeholder="DE89 3704 0044 0532 0130 00" required>
                    </div>

                    <div class="form-group">
                        <label>BIC/SWIFT</label>
                        <input type="text" class="form-control" name="bic" 
                               placeholder="COBADEFFXXX">
                    </div>

                    <div class="form-group">
                        <label>Land <span class="text-danger">*</span></label>
                        <select class="form-control" name="country" required>
                            <option value="">Bitte wählen...</option>
                            <option value="DE">Deutschland</option>
                            <option value="AT">Österreich</option>
                            <option value="CH">Schweiz</option>
                            <option value="FR">Frankreich</option>
                            <option value="IT">Italien</option>
                            <option value="ES">Spanien</option>
                            <option value="NL">Niederlande</option>
                            <option value="BE">Belgien</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Beschreibung (optional)</label>
                        <input type="text" class="form-control" name="label" 
                               placeholder="z.B. Mein Hauptkonto">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Abbrechen
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Bankkonto hinzufügen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Krypto-Wallet hinzufügen Modal -->
<div class="modal fade" id="addCryptoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fab fa-bitcoin"></i> Krypto-Wallet hinzufügen</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addCryptoForm">
                <div class="modal-body">
                    <input type="hidden" name="type" value="crypto">
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Wichtig:</strong> Nach dem Hinzufügen müssen Sie Ihre Wallet durch einen Satoshi-Test verifizieren, bevor Sie Auszahlungen vornehmen können.
                    </div>

                    <div class="form-group">
                        <label>Kryptowährung <span class="text-danger">*</span></label>
                        <select class="form-control" name="cryptocurrency" required>
                            <option value="">Bitte wählen...</option>
                            <option value="Bitcoin">Bitcoin (BTC)</option>
                            <option value="Ethereum">Ethereum (ETH)</option>
                            <option value="Litecoin">Litecoin (LTC)</option>
                            <option value="Ripple">Ripple (XRP)</option>
                            <option value="Bitcoin Cash">Bitcoin Cash (BCH)</option>
                            <option value="Tether">Tether (USDT)</option>
                            <option value="USDC">USD Coin (USDC)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Netzwerk <span class="text-danger">*</span></label>
                        <select class="form-control" name="network" required>
                            <option value="">Bitte wählen...</option>
                            <option value="Mainnet">Mainnet</option>
                            <option value="ERC20">ERC20 (Ethereum)</option>
                            <option value="TRC20">TRC20 (Tron)</option>
                            <option value="BEP20">BEP20 (BSC)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Wallet-Adresse <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="wallet_address" 
                               placeholder="1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa" required>
                        <small class="form-text text-muted">
                            Bitte überprüfen Sie die Adresse sorgfältig. Falsche Adressen können zu Geldverlust führen.
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Beschreibung (optional)</label>
                        <input type="text" class="form-control" name="label" 
                               placeholder="z.B. Meine BTC Cold Wallet">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Abbrechen
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Wallet hinzufügen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Payment Method Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye"></i> <span id="viewModalTitle">Zahlungsmethode Details</span></h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewDetailsContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Schließen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Fiat Payment Method Modal -->
<div class="modal fade" id="editFiatModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Bankkonto bearbeiten</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editFiatForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_fiat_id" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_payment_method">Zahlungsmethode *</label>
                            <select class="form-control" id="edit_payment_method" name="payment_method" required>
                                <option value="">Bitte wählen...</option>
                                <option value="Banküberweisung (SEPA)">Banküberweisung (SEPA)</option>
                                <option value="Auslandsüberweisung">Auslandsüberweisung</option>
                                <option value="Kredit-/Debitkarte">Kredit-/Debitkarte</option>
                                <option value="PayPal">PayPal</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_bank_name">Bankname *</label>
                            <input type="text" class="form-control" id="edit_bank_name" name="bank_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_account_holder">Kontoinhaber *</label>
                            <input type="text" class="form-control" id="edit_account_holder" name="account_holder" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_iban">IBAN *</label>
                            <input type="text" class="form-control" id="edit_iban" name="iban" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_bic">BIC/SWIFT</label>
                            <input type="text" class="form-control" id="edit_bic" name="bic">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_country">Land *</label>
                            <select class="form-control" id="edit_country" name="country" required>
                                <option value="">Bitte wählen...</option>
                                <option value="Deutschland">Deutschland</option>
                                <option value="Österreich">Österreich</option>
                                <option value="Schweiz">Schweiz</option>
                                <option value="Frankreich">Frankreich</option>
                                <option value="Italien">Italien</option>
                                <option value="Spanien">Spanien</option>
                                <option value="Niederlande">Niederlande</option>
                                <option value="Belgien">Belgien</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="edit_label">Beschreibung (optional)</label>
                            <input type="text" class="form-control" id="edit_label" name="label" placeholder="z.B. Mein Hauptkonto">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Abbrechen
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Änderungen speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Crypto Payment Method Modal -->
<div class="modal fade" id="editCryptoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Krypto-Wallet bearbeiten</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editCryptoForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_crypto_id" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_cryptocurrency">Kryptowährung *</label>
                            <select class="form-control" id="edit_cryptocurrency" name="cryptocurrency" required>
                                <option value="">Bitte wählen...</option>
                                <option value="Bitcoin">Bitcoin (BTC)</option>
                                <option value="Ethereum">Ethereum (ETH)</option>
                                <option value="Litecoin">Litecoin (LTC)</option>
                                <option value="Ripple">Ripple (XRP)</option>
                                <option value="Bitcoin Cash">Bitcoin Cash (BCH)</option>
                                <option value="Tether">Tether (USDT)</option>
                                <option value="USD Coin">USD Coin (USDC)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_network">Netzwerk *</label>
                            <select class="form-control" id="edit_network" name="network" required>
                                <option value="">Bitte wählen...</option>
                                <option value="Mainnet">Mainnet</option>
                                <option value="ERC20">ERC20 (Ethereum)</option>
                                <option value="TRC20">TRC20 (Tron)</option>
                                <option value="BEP20">BEP20 (BSC)</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="edit_wallet_address">Wallet-Adresse *</label>
                            <input type="text" class="form-control" id="edit_wallet_address" name="wallet_address" required>
                            <small class="form-text text-muted">
                                <i class="fas fa-exclamation-triangle"></i> Bitte überprüfen Sie die Adresse sorgfältig. Falsche Adressen können zu Geldverlust führen.
                            </small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="edit_crypto_label">Beschreibung (optional)</label>
                            <input type="text" class="form-control" id="edit_crypto_label" name="label" placeholder="z.B. Meine BTC Cold Wallet">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Abbrechen
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Änderungen speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Verify Wallet Modal -->
<div class="modal fade" id="verifyWalletModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-shield-alt"></i> Wallet Verifizierung - Satoshi Test</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="verifyWalletForm">
                <div class="modal-body">
                    <input type="hidden" id="verify_wallet_id" name="wallet_id">
                    
                    <div class="alert alert-danger">
                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Warum ist die Wallet-Verifizierung zwingend erforderlich?</h5>
                        <p class="mb-2"><strong>Die Verifizierung Ihres Crypto-Wallets durch den Satoshi-Test ist aus folgenden kritischen Gründen erforderlich:</strong></p>
                        
                        <h6 class="mt-3"><i class="fas fa-lock"></i> Sicherheit & Betrugsschutz:</h6>
                        <ul>
                            <li><strong>Eigentumsnachweis:</strong> Nur der tatsächliche Wallet-Besitzer kann eine Transaktion von dieser Adresse initiieren</li>
                            <li><strong>Schutz vor Identitätsdiebstahl:</strong> Verhindert, dass Dritte fremde Wallet-Adressen missbrauchen</li>
                            <li><strong>Betrugsprävention:</strong> Reduziert das Risiko von Geldwäsche und betrügerischen Transaktionen</li>
                        </ul>
                        
                        <h6 class="mt-3"><i class="fas fa-university"></i> Compliance & Regulierung:</h6>
                        <ul>
                            <li><strong>Gesetzliche Anforderungen:</strong> Erfüllt KYC/AML-Vorschriften (Know Your Customer / Anti-Money Laundering)</li>
                            <li><strong>Finanzaufsicht:</strong> Entspricht den Richtlinien der Finanzregulierungsbehörden</li>
                            <li><strong>Plattform-Schutz:</strong> Schützt unsere Plattform vor illegalen Aktivitäten</li>
                        </ul>
                        
                        <h6 class="mt-3"><i class="fas fa-coins"></i> Transaktionssicherheit:</h6>
                        <ul>
                            <li><strong>Einzahlungen:</strong> Ohne Verifizierung können wir eingehende Zahlungen nicht sicher Ihrem Konto zuordnen</li>
                            <li><strong>Auszahlungen:</strong> Verhindert versehentliche Überweisungen an falsche oder nicht kontrollierte Adressen</li>
                            <li><strong>Rückabwicklung:</strong> Bei Problemen können verifizierte Transaktionen besser nachvollzogen werden</li>
                        </ul>
                        
                        <h6 class="mt-3"><i class="fas fa-shield-alt"></i> Ihre Sicherheit:</h6>
                        <ul>
                            <li><strong>Schutz vor Verlusten:</strong> Verhindert, dass Gelder an falsche Adressen gesendet werden</li>
                            <li><strong>Kontosicherheit:</strong> Zusätzliche Sicherheitsebene für Ihr Konto</li>
                            <li><strong>Verantwortung:</strong> Sie behalten die volle Kontrolle über Ihre Krypto-Assets</li>
                        </ul>
                        
                        <div class="alert alert-warning mt-3 mb-0">
                            <strong><i class="fas fa-ban"></i> Wichtig:</strong> Ohne verifiziertes Wallet können <u>keine Einzahlungen empfangen</u> und <u>keine Auszahlungen durchgeführt</u> werden. Die Verifizierung ist eine einmalige Maßnahme zum Schutz aller Beteiligten.
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Was ist der Satoshi Test?</h6>
                        <p class="mb-0">Sie senden einen sehr kleinen Test-Betrag (meist 1000 Satoshi = 0.00001 BTC oder gleichwertig) von Ihrer Wallet an unsere Verifizierungs-Adresse. Dies beweist unwiderlegbar, dass Sie der Besitzer des Wallets sind und Zugriff auf die Private Keys haben.</p>
                    </div>
                    
                    <div id="verifyWalletDetails">
                        <!-- Wallet details will be populated here -->
                    </div>
                    
                    <div class="card mt-3 bg-light">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-list-ol"></i> Verifizierungsschritte:</h6>
                            <ol class="mb-0">
                                <li>Senden Sie den angegebenen Betrag an die Verifizierungs-Adresse</li>
                                <li>Warten Sie auf die Blockchain-Bestätigung (5-10 Minuten)</li>
                                <li>Geben Sie die Transaktions-ID unten ein</li>
                                <li>Unser System wird die Transaktion überprüfen</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12 mb-3">
                            <label for="verify_transaction_id">Transaktions-ID (TxID) *</label>
                            <input type="text" class="form-control" id="verify_transaction_id" name="transaction_id" required placeholder="Geben Sie die Transaktions-ID ein">
                            <small class="form-text text-muted">
                                Die Transaktions-ID finden Sie in Ihrer Wallet nach dem Senden
                            </small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="verify_notes">Anmerkungen (optional)</label>
                            <textarea class="form-control" id="verify_notes" name="notes" rows="2" placeholder="Zusätzliche Informationen..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Abbrechen
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Verifizierung einreichen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Zahlungsmethoden laden
function loadPaymentMethods() {
    $.ajax({
        url: 'ajax/get_payment_methods.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // API returns response.methods.fiat and response.methods.crypto
                const fiatMethods = (response.methods && response.methods.fiat) || response.fiat || [];
                const cryptoMethods = (response.methods && response.methods.crypto) || response.crypto || [];
                displayFiatMethods(fiatMethods);
                displayCryptoMethods(cryptoMethods);
            } else {
                showError(response.message || 'Fehler beim Laden der Zahlungsmethoden');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading payment methods:', error);
            showError('Serverfehler beim Laden der Zahlungsmethoden');
        }
    });
}

// Fiat-Methoden anzeigen
function displayFiatMethods(methods) {
    const container = $('#fiatMethodsContainer');
    $('#fiatCount').text(methods.length);
    
    if (methods.length === 0) {
        container.html(`
            <div class="empty-state">
                <i class="fas fa-university"></i>
                <h5>Keine Bankkonten</h5>
                <p>Fügen Sie Ihr erstes Bankkonto hinzu</p>
            </div>
        `);
        return;
    }

    let tableHtml = `
        <table class="payment-table">
            <thead>
                <tr>
                    <th>Methode</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th>Hinzugefügt</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
    `;

    methods.forEach(method => {
        const isDefault = method.is_default == 1;
        const statusClass = getStatusClass(method.verification_status);
        const statusText = getStatusTextDE(method.verification_status);
        
        tableHtml += `
            <tr>
                <td>
                    <i class="fas fa-university method-icon"></i>
                    <strong>${escapeHtml(method.bank_name || method.payment_method)}</strong>
                    ${isDefault ? '<br><span class="default-badge">Standard</span>' : ''}
                </td>
                <td>
                    <div><strong>${escapeHtml(method.account_holder)}</strong></div>
                    <div class="text-muted" style="font-size: 12px;">${maskIBAN(method.iban)}</div>
                </td>
                <td>
                    <span class="status-badge ${statusClass}">${statusText}</span>
                </td>
                <td>
                    <span class="date-text">${formatDate(method.created_at)}</span>
                </td>
                <td>
                    <button class="action-btn btn-info" onclick='viewMethodDetails(${JSON.stringify(method)}, "fiat")' title="Details anzeigen">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn btn-primary" onclick="editMethod(${method.id}, 'fiat')" title="Bearbeiten">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${!isDefault ? `
                    <button class="action-btn btn-success" onclick="setDefaultMethod(${method.id})" title="Als Standard setzen">
                        <i class="fas fa-star"></i>
                    </button>
                    ` : ''}
                    <button class="action-btn btn-danger" onclick="deleteMethod(${method.id})" title="Löschen">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    tableHtml += `
            </tbody>
        </table>
    `;

    container.html(tableHtml);
}

// Krypto-Methoden anzeigen
function displayCryptoMethods(methods) {
    const container = $('#cryptoMethodsContainer');
    $('#cryptoCount').text(methods.length);
    
    if (methods.length === 0) {
        container.html(`
            <div class="empty-state">
                <i class="fab fa-bitcoin"></i>
                <h5>Keine Krypto-Wallets</h5>
                <p>Fügen Sie Ihr erstes Krypto-Wallet hinzu</p>
            </div>
        `);
        return;
    }

    let tableHtml = `
        <table class="payment-table">
            <thead>
                <tr>
                    <th>Kryptowährung</th>
                    <th>Wallet-Adresse</th>
                    <th>Status</th>
                    <th>Hinzugefügt</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
    `;

    methods.forEach(method => {
        const isDefault = method.is_default == 1;
        const statusClass = getStatusClass(method.verification_status);
        const statusText = getStatusTextDE(method.verification_status);
        const needsVerification = method.verification_status !== 'verified';
        
        tableHtml += `
            <tr>
                <td>
                    <i class="fab fa-${getCryptoIcon(method.cryptocurrency)} method-icon"></i>
                    <strong>${escapeHtml(method.cryptocurrency)}</strong>
                    ${isDefault ? '<br><span class="default-badge">Standard</span>' : ''}
                </td>
                <td>
                    <code style="font-size: 12px;">${maskWalletAddress(method.wallet_address)}</code>
                    <br>
                    <span class="text-muted" style="font-size: 11px;">Netzwerk: ${escapeHtml(method.network)}</span>
                </td>
                <td>
                    <span class="status-badge ${statusClass}">${statusText}</span>
                </td>
                <td>
                    <span class="date-text">${formatDate(method.created_at)}</span>
                </td>
                <td>
                    <button class="action-btn btn-info" onclick='viewMethodDetails(${JSON.stringify(method)}, "crypto")' title="Details anzeigen">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${needsVerification ? `
                    <button class="action-btn btn-warning" onclick="verifyWallet(${method.id})" title="Verifizieren">
                        <i class="fas fa-shield-alt"></i>
                    </button>
                    ` : ''}
                    <button class="action-btn btn-primary" onclick="editMethod(${method.id}, 'crypto')" title="Bearbeiten">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${!isDefault ? `
                    <button class="action-btn btn-success" onclick="setDefaultMethod(${method.id})" title="Als Standard setzen">
                        <i class="fas fa-star"></i>
                    </button>
                    ` : ''}
                    <button class="action-btn btn-danger" onclick="deleteMethod(${method.id})" title="Löschen">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    tableHtml += `
            </tbody>
        </table>
    `;

    container.html(tableHtml);
}

// Hilfsfunktionen
function getStatusClass(status) {
    const statusMap = {
        'verified': 'verified',
        'pending': 'pending',
        'failed': 'failed',
        'not_verified': 'not-verified'
    };
    return statusMap[status] || 'not-verified';
}

function getStatusTextDE(status) {
    const textMap = {
        'verified': 'Verifiziert',
        'pending': 'Ausstehend',
        'failed': 'Fehlgeschlagen',
        'not_verified': 'Nicht verifiziert'
    };
    return textMap[status] || 'Unbekannt';
}

function getCryptoIcon(crypto) {
    const iconMap = {
        'Bitcoin': 'bitcoin',
        'Ethereum': 'ethereum',
        'Litecoin': 'litecoin',
        'Ripple': 'ripple',
        'Bitcoin Cash': 'bitcoin',
        'Tether': 'dollar-sign',
        'USDC': 'dollar-sign'
    };
    return iconMap[crypto] || 'coins';
}

function maskIBAN(iban) {
    if (!iban) return '';
    const cleaned = iban.replace(/\s/g, '');
    if (cleaned.length > 8) {
        return cleaned.substring(0, 4) + ' **** **** ' + cleaned.substring(cleaned.length - 4);
    }
    return iban;
}

function maskWalletAddress(address) {
    if (!address) return '';
    if (address.length > 16) {
        return address.substring(0, 8) + '...' + address.substring(address.length - 8);
    }
    return address;
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('de-DE');
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Modal-Funktionen
function showAddFiatModal() {
    $('#addFiatModal').modal('show');
}

function showAddCryptoModal() {
    $('#addCryptoModal').modal('show');
}

// Formular-Handler
$('#addFiatForm').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'ajax/add_payment_method.php',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#addFiatModal').modal('hide');
                showSuccess('Bankkonto erfolgreich hinzugefügt');
                loadPaymentMethods();
                $('#addFiatForm')[0].reset();
            } else {
                showError(response.message || 'Fehler beim Hinzufügen des Bankkontos');
            }
        },
        error: function() {
            showError('Serverfehler beim Hinzufügen des Bankkontos');
        }
    });
});

$('#addCryptoForm').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'ajax/add_payment_method.php',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#addCryptoModal').modal('hide');
                showSuccess('Krypto-Wallet erfolgreich hinzugefügt. Bitte verifizieren Sie es über den Satoshi-Test.');
                loadPaymentMethods();
                $('#addCryptoForm')[0].reset();
            } else {
                showError(response.message || 'Fehler beim Hinzufügen des Krypto-Wallets');
            }
        },
        error: function() {
            showError('Serverfehler beim Hinzufügen des Krypto-Wallets');
        }
    });
});

// Aktions-Funktionen
function deleteMethod(id) {
    if (!confirm('Möchten Sie diese Zahlungsmethode wirklich löschen?')) {
        return;
    }
    
    $.ajax({
        url: 'ajax/delete_payment_method.php',
        method: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showSuccess('Zahlungsmethode erfolgreich gelöscht');
                loadPaymentMethods();
            } else {
                showError(response.message || 'Fehler beim Löschen');
            }
        },
        error: function() {
            showError('Serverfehler beim Löschen');
        }
    });
}

function setDefaultMethod(id) {
    $.ajax({
        url: 'ajax/set_default_payment_method.php',
        method: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showSuccess('Standard-Zahlungsmethode aktualisiert');
                loadPaymentMethods();
            } else {
                showError(response.message || 'Fehler beim Setzen der Standard-Methode');
            }
        },
        error: function() {
            showError('Serverfehler');
        }
    });
}

function verifyWallet(id) {
    // Load wallet details for verification
    $.ajax({
        url: 'ajax/get_payment_methods.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const methods = response.methods ? 
                    [...(response.methods.crypto || []), ...(response.crypto || [])] :
                    (response.crypto || []);
                const wallet = methods.find(m => m.id == id);
                
                if (wallet) {
                    showVerifyWalletModal(wallet);
                } else {
                    showError('Wallet nicht gefunden');
                }
            }
        }
    });
}

function showVerifyWalletModal(wallet) {
    $('#verify_wallet_id').val(wallet.id);
    
    // Check if admin has set verification details
    const hasVerificationDetails = wallet.verification_amount && wallet.verification_address;
    
    let detailsHtml = `
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="text-muted mb-1"><small>Ihr Wallet</small></label>
                <div class="form-control-plaintext border rounded p-2 bg-light">
                    <i class="fab fa-${getCryptoIcon(wallet.cryptocurrency)}"></i>
                    <strong>${escapeHtml(wallet.cryptocurrency)}</strong>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="text-muted mb-1"><small>Netzwerk</small></label>
                <div class="form-control-plaintext border rounded p-2 bg-light">
                    ${escapeHtml(wallet.network)}
                </div>
            </div>
            <div class="col-md-12 mb-3">
                <label class="text-muted mb-1"><small>Ihre Wallet-Adresse</small></label>
                <div class="form-control-plaintext border rounded p-2 bg-light">
                    <code style="font-size: 11px; word-break: break-all;">${escapeHtml(wallet.wallet_address)}</code>
                </div>
            </div>
            <div class="col-md-12 mb-3">
                <label class="text-muted mb-1"><small>Verifizierungsstatus</small></label>
                <div class="form-control-plaintext border rounded p-2 bg-light">
                    <span class="status-badge ${getStatusClass(wallet.verification_status)}">
                        ${getStatusTextDE(wallet.verification_status)}
                    </span>
                </div>
            </div>
    `;
    
    if (!hasVerificationDetails) {
        // Admin hasn't set verification details yet
        detailsHtml += `
            <div class="col-md-12 mt-3">
                <div class="alert alert-warning">
                    <h6 class="alert-heading"><i class="fas fa-clock"></i> Warten auf Administrator-Einrichtung</h6>
                    <p class="mb-2">Die Verifizierungsdetails für dieses Wallet wurden noch nicht vom Administrator eingerichtet.</p>
                    <p class="mb-0">
                        <strong>Was bedeutet das?</strong><br>
                        Unser Administrator muss zunächst die Verifizierungs-Adresse und den Test-Betrag für Ihr ${escapeHtml(wallet.cryptocurrency)}-Wallet konfigurieren. 
                        Dies ist ein einmaliger Prozess zur Sicherstellung der korrekten Netzwerk-Konfiguration.
                    </p>
                </div>
                <div class="alert alert-info">
                    <p class="mb-0">
                        <i class="fas fa-info-circle"></i> <strong>Nächste Schritte:</strong><br>
                        Sie werden per E-Mail benachrichtigt, sobald die Verifizierungsdetails bereitstehen. 
                        Danach können Sie den Verifizierungsprozess abschließen und Ihr Wallet für Ein- und Auszahlungen freischalten.
                    </p>
                </div>
            </div>
        `;
        
        // Hide the transaction form when details are not ready
        $('#verifyWalletForm').find('.row.mt-4').hide();
        $('#verifyWalletForm').find('button[type="submit"]').hide();
    } else {
        // Admin has set verification details - show them
        const verificationAmount = wallet.verification_amount;
        const verificationAddress = wallet.verification_address;
        
        detailsHtml += `
            <div class="col-md-12 mt-3">
                <div class="alert alert-success">
                    <h6 class="alert-heading"><i class="fas fa-check-circle"></i> Verifizierung bereit</h6>
                    <p class="mb-0">Die Verifizierungsdetails sind eingerichtet. Sie können jetzt den Test-Betrag senden.</p>
                </div>
            </div>
            <div class="col-md-12 mt-3">
                <div class="alert alert-warning">
                    <h6 class="alert-heading"><i class="fas fa-arrow-right"></i> Senden Sie den Test-Betrag</h6>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong><i class="fas fa-coins"></i> Exakter Betrag:</strong><br>
                            <code class="text-dark" style="font-size: 14px;">${verificationAmount} ${escapeHtml(wallet.cryptocurrency)}</code>
                            <button class="btn btn-sm btn-outline-primary ml-2" onclick="copyToClipboard('${verificationAmount}')" title="Betrag kopieren">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong><i class="fas fa-wallet"></i> An Verifizierungs-Adresse:</strong><br>
                            <code class="text-dark" style="font-size: 10px; word-break: break-all;">${escapeHtml(verificationAddress)}</code>
                            <button class="btn btn-sm btn-outline-primary ml-2" onclick="copyToClipboard('${verificationAddress}')" title="Adresse kopieren">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>Wichtig:</strong> Senden Sie genau den angegebenen Betrag. Abweichungen können die Verifizierung verzögern.
                        </small>
                    </div>
                </div>
            </div>
        `;
        
        // Show the transaction form when details are ready
        $('#verifyWalletForm').find('.row.mt-4').show();
        $('#verifyWalletForm').find('button[type="submit"]').show();
    }
    
    detailsHtml += `</div>`;
    
    $('#verifyWalletDetails').html(detailsHtml);
    $('#verifyWalletModal').modal('show');
}

// Handle verify wallet form submission
$('#verifyWalletForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        wallet_id: $('#verify_wallet_id').val(),
        transaction_id: $('#verify_transaction_id').val(),
        notes: $('#verify_notes').val()
    };
    
    $.ajax({
        url: 'ajax/submit_wallet_verification.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#verifyWalletModal').modal('hide');
                showSuccess('Verifizierung eingereicht. Wir werden Ihre Transaktion prüfen.');
                loadPaymentMethods();
                // Clear form
                $('#verifyWalletForm')[0].reset();
            } else {
                showError(response.message || 'Fehler beim Einreichen der Verifizierung');
            }
        },
        error: function() {
            showError('Serverfehler beim Einreichen der Verifizierung');
        }
    });
});

function editMethod(id, type) {
    // Load method details and show edit modal
    $.ajax({
        url: 'ajax/get_payment_methods.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let methods = [];
                if (type === 'fiat') {
                    methods = response.methods ? response.methods.fiat || [] : response.fiat || [];
                } else {
                    methods = response.methods ? response.methods.crypto || [] : response.crypto || [];
                }
                
                const method = methods.find(m => m.id == id);
                
                if (method) {
                    showEditModal(method, type);
                } else {
                    showError('Zahlungsmethode nicht gefunden');
                }
            }
        }
    });
}

function showEditModal(method, type) {
    if (type === 'fiat') {
        // Populate fiat edit form
        $('#edit_fiat_id').val(method.id);
        $('#edit_payment_method').val(method.payment_method);
        $('#edit_bank_name').val(method.bank_name);
        $('#edit_account_holder').val(method.account_holder);
        $('#edit_iban').val(method.iban);
        $('#edit_bic').val(method.bic);
        $('#edit_country').val(method.country);
        $('#edit_label').val(method.label);
        
        $('#editFiatModal').modal('show');
    } else {
        // Populate crypto edit form
        $('#edit_crypto_id').val(method.id);
        $('#edit_cryptocurrency').val(method.cryptocurrency);
        $('#edit_network').val(method.network);
        $('#edit_wallet_address').val(method.wallet_address);
        $('#edit_crypto_label').val(method.label);
        
        $('#editCryptoModal').modal('show');
    }
}

// Handle edit fiat form submission
$('#editFiatForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'ajax/update_payment_method.php',
        method: 'POST',
        data: $(this).serialize() + '&type=fiat',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#editFiatModal').modal('hide');
                showSuccess('Bankkonto erfolgreich aktualisiert');
                loadPaymentMethods();
            } else {
                showError(response.message || 'Fehler beim Aktualisieren');
            }
        },
        error: function() {
            showError('Serverfehler beim Aktualisieren');
        }
    });
});

// Handle edit crypto form submission
$('#editCryptoForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'ajax/update_payment_method.php',
        method: 'POST',
        data: $(this).serialize() + '&type=crypto',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#editCryptoModal').modal('hide');
                showSuccess('Krypto-Wallet erfolgreich aktualisiert');
                loadPaymentMethods();
            } else {
                showError(response.message || 'Fehler beim Aktualisieren');
            }
        },
        error: function() {
            showError('Serverfehler beim Aktualisieren');
        }
    });
});

// View method details in modal
function viewMethodDetails(method, type) {
    const modalTitle = type === 'fiat' ? 'Bankkonto Details' : 'Krypto-Wallet Details';
    $('#viewModalTitle').html('<i class="fas fa-' + (type === 'fiat' ? 'university' : 'bitcoin') + '"></i> ' + modalTitle);
    
    let detailsHtml = '';
    
    if (type === 'fiat') {
        detailsHtml = `
            <div class="row">
                <div class="col-md-12">
                    <h6 class="mb-3" style="color: #4e73df; font-weight: 600;">
                        <i class="fas fa-info-circle"></i> Allgemeine Informationen
                    </h6>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-1"><small>Zahlungsmethode</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        ${escapeHtml(method.payment_method || 'N/A')}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-1"><small>Bankname</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        ${escapeHtml(method.bank_name || 'N/A')}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-1"><small>Kontoinhaber</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        ${escapeHtml(method.account_holder || 'N/A')}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-1"><small>IBAN</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        <code style="font-size: 13px;">${escapeHtml(method.iban || 'N/A')}</code>
                        <button class="btn btn-sm btn-outline-primary ml-2" onclick="copyToClipboard('${escapeHtml(method.iban)}')" title="Kopieren">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-1"><small>BIC/SWIFT</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        ${escapeHtml(method.bic || 'N/A')}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-1"><small>Land</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        ${escapeHtml(method.country || 'N/A')}
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="text-muted mb-1"><small>Beschreibung</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        ${escapeHtml(method.label || 'Keine Beschreibung')}
                    </div>
                </div>
                <div class="col-md-12 mt-3">
                    <h6 class="mb-3" style="color: #4e73df; font-weight: 600;">
                        <i class="fas fa-check-circle"></i> Status & Daten
                    </h6>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="text-muted mb-1"><small>Verifizierungsstatus</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        <span class="status-badge ${getStatusClass(method.verification_status)}">
                            ${getStatusTextDE(method.verification_status)}
                        </span>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="text-muted mb-1"><small>Standard-Methode</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        ${method.is_default == 1 ? '<span class="badge badge-primary">Ja</span>' : '<span class="badge badge-secondary">Nein</span>'}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="text-muted mb-1"><small>Hinzugefügt am</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        ${formatDate(method.created_at)}
                    </div>
                </div>
            </div>
        `;
    } else {
        // Crypto method
        detailsHtml = `
            <div class="row">
                <div class="col-md-12">
                    <h6 class="mb-3" style="color: #4e73df; font-weight: 600;">
                        <i class="fas fa-info-circle"></i> Wallet Informationen
                    </h6>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-1"><small>Kryptowährung</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        <i class="fab fa-${getCryptoIcon(method.cryptocurrency)}"></i>
                        ${escapeHtml(method.cryptocurrency || 'N/A')}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted mb-1"><small>Netzwerk</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        ${escapeHtml(method.network || 'N/A')}
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="text-muted mb-1"><small>Wallet-Adresse</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        <code style="font-size: 12px; word-break: break-all;">${escapeHtml(method.wallet_address || 'N/A')}</code>
                        <button class="btn btn-sm btn-outline-primary ml-2" onclick="copyToClipboard('${escapeHtml(method.wallet_address)}')" title="Kopieren">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="text-muted mb-1"><small>Beschreibung</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        ${escapeHtml(method.label || 'Keine Beschreibung')}
                    </div>
                </div>
                <div class="col-md-12 mt-3">
                    <h6 class="mb-3" style="color: #4e73df; font-weight: 600;">
                        <i class="fas fa-shield-alt"></i> Verifizierung & Status
                    </h6>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="text-muted mb-1"><small>Verifizierungsstatus</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        <span class="status-badge ${getStatusClass(method.verification_status)}">
                            ${getStatusTextDE(method.verification_status)}
                        </span>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="text-muted mb-1"><small>Standard-Methode</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        ${method.is_default == 1 ? '<span class="badge badge-primary">Ja</span>' : '<span class="badge badge-secondary">Nein</span>'}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="text-muted mb-1"><small>Hinzugefügt am</small></label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        ${formatDate(method.created_at)}
                    </div>
                </div>
                ${method.verification_status !== 'verified' ? `
                <div class="col-md-12 mt-2">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Verifizierung erforderlich:</strong> Diese Wallet muss durch den Satoshi-Test verifiziert werden, bevor sie für Auszahlungen verwendet werden kann.
                    </div>
                </div>
                ` : ''}
            </div>
        `;
    }
    
    $('#viewDetailsContent').html(detailsHtml);
    $('#viewDetailsModal').modal('show');
}

// Copy to clipboard function
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            showSuccess('In Zwischenablage kopiert!');
        }, function() {
            fallbackCopyToClipboard(text);
        });
    } else {
        fallbackCopyToClipboard(text);
    }
}

function fallbackCopyToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.top = "-9999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        showSuccess('In Zwischenablage kopiert!');
    } catch (err) {
        showError('Kopieren fehlgeschlagen');
    }
    document.body.removeChild(textArea);
}

// Benachrichtigungen
function showSuccess(message) {
    // Implementieren Sie hier Ihre Toast/Notification-Logik
    alert(message);
}

function showError(message) {
    // Implementieren Sie hier Ihre Toast/Notification-Logik
    alert(message);
}

// Beim Laden der Seite
$(document).ready(function() {
    loadPaymentMethods();
});
</script>

<?php include 'footer.php'; ?>
