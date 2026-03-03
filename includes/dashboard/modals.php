<?php if ($passwordChangeRequired): ?>

<div class="modal fade show" id="passwordChangeModal" tabindex="-1" role="dialog"
     aria-labelledby="passwordChangeModalLabel" style="display:block; padding-right:15px;" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title mb-0" id="passwordChangeModalLabel">
                    <i class="anticon anticon-lock m-r-5"></i> Passwortänderung erforderlich
                </h5>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning mb-4" role="alert">
                    <i class="anticon anticon-info-circle"></i>
                    Zu Ihrer Sicherheit aktualisieren Sie bitte Ihr Passwort, bevor Sie fortfahren.
                </div>

                <form id="passwordChangeForm" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">

                    <!-- Current Password -->
                    <div class="form-group">
                        <label for="currentPassword">Aktuelles Passwort</label>
                        <input type="password" class="form-control" id="currentPassword" required aria-required="true" autocomplete="current-password">
                    </div>

                    <!-- New Password -->
                    <div class="form-group">
                        <label for="newPassword">Neues Passwort</label>
                        <input type="password" class="form-control" id="newPassword" required minlength="8" aria-describedby="passwordHelp" autocomplete="new-password">
                        <small id="passwordHelp" class="form-text text-muted">
                            Verwenden Sie ein eindeutiges Passwort. Wir fordern mindestens 8 Zeichen.
                        </small>

                        <!-- Strength Bar -->
                        <div class="progress mt-2" style="height:8px;">
                            <div id="passwordStrengthBar" class="progress-bar bg-danger" style="width:0%;" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small id="passwordStrengthText" class="text-muted small d-block mb-1" aria-live="polite">Stärke: Schwach</small>

                        <!-- Requirements Checklist -->
                        <ul class="list-unstyled small" id="passwordChecklist" aria-hidden="false">
                            <li id="req-length" class="text-danger"><i class="anticon anticon-close"></i> Mindestens 8 Zeichen</li>
                            <li id="req-upper" class="text-danger"><i class="anticon anticon-close"></i> Mindestens ein Großbuchstabe</li>
                            <li id="req-number" class="text-danger"><i class="anticon anticon-close"></i> Mindestens eine Zahl</li>
                            <li id="req-special" class="text-danger"><i class="anticon anticon-close"></i> Mindestens ein Sonderzeichen</li>
                        </ul>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="confirmPassword">Neues Passwort bestätigen</label>
                        <input type="password" class="form-control" id="confirmPassword" required autocomplete="new-password">
                        <small id="passwordMatchText" class="small text-muted" aria-live="polite">Warten auf Eingabe...</small>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submitPasswordChange" aria-label="Change password">
                    <i class="anticon anticon-save"></i> Passwort ändern
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>


<div class="modal fade" id="newDepositModal" tabindex="-1" role="dialog" aria-labelledby="newDepositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #2950a8 0%, #2da9e3 100%); color: #fff; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold" id="newDepositModalLabel">
                    <i class="anticon anticon-plus-circle mr-2"></i>Konto aufladen
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="depositForm" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 d-flex align-items-start" role="alert" style="border-radius: 10px; background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(23, 162, 184, 0.05));">
                        <i class="anticon anticon-info-circle mr-2" style="font-size: 20px;"></i>
                        <div>
                            <strong>Wichtig:</strong> Bitte schließen Sie Ihre Einzahlung innerhalb von 30 Minuten ab, um Verzögerungen bei der Verarbeitung zu vermeiden.
                            <div class="small text-muted mt-1">Einzahlungen helfen, Wiederherstellungsmaßnahmen für Ihre aktiven Fälle zu beschleunigen.</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-600" style="color: #2c3e50;">Betrag (EUR €)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" aria-hidden="true" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; border: none; font-weight: 600;">€</span>
                            </div>
                            <input type="number" class="form-control" name="amount" min="10" step="0.01" required placeholder="Einzahlungsbetrag eingeben" aria-label="Amount in Euros" style="border-radius: 0 8px 8px 0; border-left: none; font-size: 18px; font-weight: 600;">
                        </div>
                        <small class="form-text text-muted"><i class="anticon anticon-check-circle text-success mr-1"></i>Mindesteinzahlung: €10,00 | Bearbeitungsgebühr: 0%</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-600" style="color: #2c3e50;">Zahlungsmethode</label>
                        <select class="form-control select2" name="payment_method" id="paymentMethod" required aria-required="true" style="border-radius: 8px; padding: 12px; font-size: 15px;">
                            <option value="">Zahlungsmethode auswählen</option>
                            <?php
                            try {
                                $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE is_active = 1 AND allows_deposit = 1");
                                $stmt->execute();
                                while ($method = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $details = [
                                        'bank_name' => $method['bank_name'] ?? '',
                                        'account_number' => $method['account_number'] ?? '',
                                        'routing_number' => $method['routing_number'] ?? '',
                                        'wallet_address' => $method['wallet_address'] ?? '',
                                        'instructions' => $method['instructions'] ?? '',
                                        'is_crypto' => $method['is_crypto'] ?? 0
                                    ];
                                    $detailsJson = htmlspecialchars(json_encode($details, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ENT_QUOTES);
                                    echo '<option value="'.htmlspecialchars($method['method_code'], ENT_QUOTES).'" data-details=\''.$detailsJson.'\'>'.htmlspecialchars($method['method_name'], ENT_QUOTES).'</option>';
                                }
                            } catch (Exception $e) {
                                error_log("Payment methods load error: " . $e->getMessage());
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="payment-details-container mt-4" id="paymentDetails" style="display: none;">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Zahlungsanweisungen</h6>
                            </div>
                            <div class="card-body">
                                <div id="bankDetails" style="display: none;">
                                    <div class="mb-3">
                                        <h6 class="text-primary"><i class="anticon anticon-bank"></i> Banküberweisung Details</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Kontoinhaber:</strong></p>
                                                <p class="mb-1"><strong>IBAN:</strong></p>
                                                <p class="mb-1"><strong>BIC / SWIFT:</strong></p>
                                                <p class="mb-1"><strong>Kontotyp:</strong></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1" id="detail-bank-name">-</p>
                                                <p class="mb-1" id="detail-account-number">-</p>
                                                <p class="mb-1" id="detail-routing-number">-</p>
                                                <p class="mb-1">Geschäftsgirokonto</p>
                                            </div>
                                        </div>
                                        <div class="alert alert-warning mt-3">
                                            <i class="anticon anticon-exclamation-circle"></i>
                                            <strong>Hinweis:</strong> Geben Sie Ihre <strong>RF3K8M1ZPW-<?= htmlspecialchars($currentUser['id'],ENT_QUOTES) ?></strong> als Zahlungsreferenz an
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="cryptoDetails" style="display: none;">
                                    <div class="mb-3">
                                        <h6 class="text-primary"><i class="anticon anticon-block"></i> Krypto-Wallet Details</h6>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="mb-1"><strong>Netzwerk:</strong> <span id="detail-crypto-network">Ethereum (ERC20)</span></p>
                                                <p class="mb-1"><strong>Wallet-Adresse:</strong></p>
                                                <div class="input-group mb-2">
                                                    <input type="text" class="form-control" id="detail-wallet-address" readonly aria-label="Wallet address">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button" id="copyWalletAddress" aria-label="Copy wallet address">
                                                            <i class="anticon anticon-copy"></i> Kopieren
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="alert alert-danger">
                                                    <i class="anticon anticon-warning"></i>
                                                    <strong>Wichtig:</strong> Senden Sie nur die angegebene Kryptowährung an diese Adresse.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="generalInstructions" style="display: none;">
                                    <h6 class="text-primary"><i class="anticon anticon-info-circle"></i> Zusätzliche Anweisungen</h6>
                                    <div id="detail-instructions" class="mb-0"></div>
                                </div>
                                
                                <hr>
                                
                                <div class="form-group">
                                    <label class="font-weight-semibold" for="proofOfPayment">Zahlungsnachweis</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="proofOfPayment" name="proof_of_payment" accept="image/*,.pdf" required>
                                        <label class="custom-file-label" for="proofOfPayment">Screenshot oder PDF auswählen</label>
                                    </div>
                                    <small class="form-text text-muted">Akzeptierte Formate: JPG, PNG, PDF (Max. 2MB)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Cancel" style="border-radius: 8px;">
                        <i class="anticon anticon-close mr-1"></i>Abbrechen
                    </button>
                    <button type="submit" class="btn btn-primary" aria-label="Confirm deposit" style="border-radius: 8px; background: linear-gradient(135deg, #2950a8, #2da9e3); border: none;">
                        <i class="anticon anticon-check-circle mr-1"></i>Einzahlung bestätigen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- New Withdrawal Modal -->
<!-- 🔒 Withdrawal Modal -->
<div class="modal fade" id="newWithdrawalModal" tabindex="-1" role="dialog" aria-labelledby="newWithdrawalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #28a745, #20c997); color: #fff; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold" id="newWithdrawalModalLabel">
                    <i class="anticon anticon-download mr-2"></i>Auszahlungsantrag
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="withdrawalForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">

                <div class="modal-body p-4">

                    <div class="alert alert-info border-0 d-flex align-items-start" role="alert" style="border-radius: 10px; background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(23, 162, 184, 0.05));">
                        <i class="anticon anticon-clock-circle mr-2" style="font-size: 20px;"></i>
                        <div>
                            <strong>Bearbeitungszeit:</strong> Auszahlungen werden innerhalb von 1–3 Werktagen bearbeitet.
                        </div>
                    </div>

                    <!-- Hidden real balance for JS -->
                    <input type="hidden" id="availableBalance" value="<?= (float)($currentUser['balance'] ?? 0) ?>">

                    <!-- AMOUNT -->
                    <div class="form-group">
                        <label class="font-weight-600" style="color: #2c3e50;">Amount (EUR €)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; font-weight: 600;">€</span>
                            </div>
                            <input 
                                type="number"
                                class="form-control"
                                name="amount"
                                id="amount"
                                step="0.01"
                                min="1000"
                                required
                                placeholder="Minimum: €1000"
                                style="border-radius: 0 8px 8px 0; border-left: none; font-size: 18px; font-weight: 600;">
                        </div>
                        <small class="form-text text-muted">
                            <i class="anticon anticon-wallet text-success mr-1"></i>Available balance: <strong>€<?= number_format($currentUser['balance'] ?? 0, 2) ?></strong> | Minimum withdrawal: <strong>€1000</strong>
                        </small>
                    </div>

                    <!-- PAYMENT METHOD -->
                    <div class="form-group">
                        <label class="font-weight-600" style="color: #2c3e50;">Payment Method</label>
                        <select class="form-control select2" name="payment_method_id" id="withdrawalMethod" required style="border-radius: 8px; padding: 12px; font-size: 15px;">
                            <option value="">Auszahlungsmethode auswählen</option>
                            <?php
                            try {
                                // Load only user's verified payment methods (no JOIN with payment_methods)
                                $stmt = $pdo->prepare("SELECT id, type, payment_method, cryptocurrency, 
                                    wallet_address, iban, account_number, bank_name, label 
                                    FROM user_payment_methods 
                                    WHERE user_id = ? AND verification_status = 'verified'
                                    ORDER BY created_at DESC");
                                $stmt->execute([$_SESSION['user_id']]);
                                while ($userMethod = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    // Determine display name (only from user_payment_methods)
                                    if ($userMethod['label']) {
                                        $displayName = $userMethod['label'];
                                    } elseif ($userMethod['type'] === 'crypto') {
                                        $displayName = ucfirst($userMethod['cryptocurrency']);
                                    } else {
                                        $displayName = $userMethod['bank_name'] ?? 'Bank Transfer';
                                    }
                                    
                                    // Get account details based on type
                                    if ($userMethod['type'] === 'crypto') {
                                        $details = $userMethod['wallet_address'] ?? '';
                                        // Show masked version for crypto
                                        if (strlen($details) > 10) {
                                            $displayName .= ' (...' . substr($details, -6) . ')';
                                        }
                                    } else {
                                        // For bank: prefer IBAN, fallback to account_number
                                        $details = $userMethod['iban'] ?? $userMethod['account_number'] ?? '';
                                        if (strlen($details) > 10) {
                                            $displayName .= ' (...' . substr($details, -4) . ')';
                                        }
                                    }
                                    
                                    echo '<option value="' . htmlspecialchars($userMethod['id'], ENT_QUOTES) 
                                         . '" data-details="' . htmlspecialchars($details, ENT_QUOTES) 
                                         . '" data-type="' . htmlspecialchars($userMethod['type'], ENT_QUOTES) . '">' 
                                         . htmlspecialchars($displayName, ENT_QUOTES) . '</option>';
                                }
                            } catch (Exception $e) {
                                error_log("Withdrawal methods load error: " . $e->getMessage());
                            }
                            ?>
                        </select>
                        <small class="form-text text-muted">
                            <i class="anticon anticon-safety mr-1"></i>Nur Ihre verifizierten Zahlungsmethoden werden angezeigt
                        </small>
                    </div>

                    <!-- BANK DETAILS (Auto-Fill) -->
                    <div id="bankDetailsContainer" class="mt-3" style="display:none;">
                        <h6 class="text-primary"><i class="anticon anticon-bank"></i> Ihre Bankdaten</h6>
                        <p><strong>Bank:</strong> <span id="user-bank-name">-</span></p>
                        <p><strong>Kontoinhaber:</strong> <span id="user-account-holder">-</span></p>
                        <p><strong>IBAN:</strong> <span id="user-iban">-</span></p>
                        <p><strong>BIC:</strong> <span id="user-bic">-</span></p>
                    </div>

                    <!-- PAYMENT DETAILS -->
                    <div class="form-group mt-3">
                        <label class="font-weight-semibold">Zahlungsdetails</label>
                        <textarea class="form-control" name="payment_details" id="paymentDetails" rows="3" required placeholder="Vollständige Zahlungsdetails eingeben"></textarea>
                    </div>

                    <!-- CONFIRM CHECKBOX -->
                    <div class="form-group mt-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="confirmDetails" required>
                            <label class="custom-control-label" for="confirmDetails">
                                Ich bestätige, dass die angegebenen Zahlungsdetails korrekt sind.
                            </label>
                        </div>
                    </div>

                    <!-- OTP SECTION -->
                    <hr>
                    <div id="otpSection" class="pt-2">
                        <h6 class="text-primary">
                            <i class="anticon anticon-safety"></i> E-Mail-Verifizierung
                        </h6>
                        <p class="text-muted mb-2">
                            Aus Sicherheitsgründen senden wir einen Einmalcode an Ihre E-Mail-Adresse. Klicken Sie auf die Schaltfläche unten, um ihn zu erhalten und zu verifizieren.
                        </p>

                        <div class="form-group">
                            <label class="font-weight-600">Einmalpasswort (OTP)</label>
                            <div class="input-group mb-2">
                                <input type="text" id="otpCode" maxlength="6" class="form-control" placeholder="6-stelliges OTP eingeben" disabled style="font-size: 16px; letter-spacing: 3px; text-align: center; font-weight: 600;">
                                <div class="input-group-append">
                                    <button type="button" id="sendVerifyOtpBtn" class="btn btn-primary" style="min-width: 140px;">
                                        <i class="anticon anticon-mail"></i> OTP senden & verifizieren
                                    </button>
                                </div>
                            </div>
                            <small id="otpInfoText" class="form-text text-muted">
                                <i class="anticon anticon-info-circle"></i> OTP ist 5 Minuten gültig. Klicken Sie auf die Schaltfläche, um den Code an Ihre E-Mail zu senden.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">
                        <i class="anticon anticon-close mr-1"></i>Abbrechen
                    </button>
                    <button type="submit" id="withdrawalSubmitBtn" class="btn btn-success" disabled style="border-radius: 8px; background: linear-gradient(135deg, #28a745, #20c997); border: none;">
                        <i class="anticon anticon-send mr-1"></i>Antrag einreichen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" role="dialog" aria-labelledby="transactionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="transactionDetailsModalLabel">Transaktionsdetails</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-semibold">Transaktions-ID:</label>
                            <p id="txn-id" class="form-control-static">-</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-semibold">Datum & Uhrzeit:</label>
                            <p id="txn-date" class="form-control-static">-</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-semibold">Typ:</label>
                            <p id="txn-type" class="form-control-static">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-semibold">Betrag:</label>
                            <p id="txn-amount" class="form-control-static">-</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-semibold">Status:</label>
                            <p id="txn-status" class="form-control-static">-</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-semibold">Referenz:</label>
                            <p id="txn-reference" class="form-control-static">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Zahlungsdetails</h6>
                    </div>
                    <div class="card-body">
                        <div id="txn-payment-details"></div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Transaktionsverlauf</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush" id="txn-timeline" role="list">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Antrag eingereicht</span>
                                <small class="text-muted">-</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-info" id="printReceiptBtn">Beleg drucken</button>
            </div>
        </div>
    </div>
</div>

<!-- Current Date and Time Display -->
<div class="fixed-bottom text-right p-2" style="z-index: 1000;">
    <small class="bg-dark text-light px-2 py-1 rounded" role="status" aria-live="polite">
        Aktuelles Datum und Uhrzeit (UTC): <?= htmlspecialchars($currentDateTimeFormatted, ENT_QUOTES) ?> | Aktueller Benutzer-Login: <?= htmlspecialchars($currentUserLogin, ENT_QUOTES) ?>
    </small>
</div>

<style>
:root {
    --brand: #2950a8;
    --brand-light: #2da9e3;
    --brand-dark: #1e3a7a;
    --bg: #f7fafd;
    --muted: #6c757d;
    --card-radius: 12px;
    --success: #28a745;
    --danger: #dc3545;
    --warning: #ffc107;
    --info: #17a2b8;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.15);
}

/* Body & Typography */
body {
    background: linear-gradient(135deg, #f7fafd 0%, #e8f2f7 100%);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    font-size: 15px;
    line-height: 1.6;
    color: #333;
}

/* Card Improvements */
.main-content .card {
    border-radius: var(--card-radius);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: var(--shadow-sm);
    background: #fff;
    position: relative;
    overflow: hidden;
}

.main-content .card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, var(--brand) 0%, var(--brand-light) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.main-content .card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.main-content .card:hover::before {
    opacity: 1;
}

.card-body {
    padding: 1.5rem;
}

.card-header {
    background: linear-gradient(180deg, #fff 0%, #f8f9fa 100%);
    border-bottom: 2px solid #f0f0f0;
    padding: 1rem 1.5rem;
    font-weight: 600;
}

/* Avatar Icons */
.avatar-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    padding: 14px;
    color: #fff;
    font-size: 24px;
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
}

.avatar-icon::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: rgba(255, 255, 255, 0.1);
    transform: rotate(45deg);
    transition: all 0.6s ease;
}

.avatar-icon:hover::before {
    top: -60%;
    right: -60%;
}

.avatar-blue {
    background: linear-gradient(135deg, #2950a8, #2da9e3);
    box-shadow: 0 4px 15px rgba(41, 80, 168, 0.3);
}

.avatar-cyan {
    background: linear-gradient(135deg, #17a2b8, #5bd0e6);
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
}

.avatar-gold {
    background: linear-gradient(135deg, #f39c12, #f6c36d);
    box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
}

.avatar-purple {
    background: linear-gradient(135deg, #6f42c1, #b28bff);
    box-shadow: 0 4px 15px rgba(111, 66, 193, 0.3);
}

/* Typography */
.lead {
    font-size: 1.1rem;
    font-weight: 500;
    line-height: 1.5;
}

h5, .h5 {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

/* Algorithm Animation */
.algorithm-animation {
    padding: 12px 0;
}

.algorithm-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 8px;
}

.step {
    flex: 1;
    background: #fff;
    border-radius: 10px;
    padding: 12px 10px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.step.active {
    background: linear-gradient(135deg, #e8f3ff, #f4fbff);
    transform: translateY(-4px);
    border-color: var(--brand-light);
    box-shadow: 0 4px 12px rgba(41, 80, 168, 0.2);
}

.step-icon {
    font-size: 22px;
    margin-bottom: 6px;
    color: var(--brand);
}

.step-label {
    font-size: 12px;
    font-weight: 500;
    color: #555;
}

.algorithm-progress {
    height: 10px;
    background: #e9eef7;
    border-radius: 12px;
    margin-top: 12px;
    overflow: hidden;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}

.algorithm-progress .progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--brand), var(--brand-light));
    transition: width 1s cubic-bezier(0.2, 0.9, 0.3, 1);
    box-shadow: 0 2px 4px rgba(41, 80, 168, 0.3);
}

/* Table Improvements */
.table-hover tbody tr {
    transition: background-color 0.2s ease;
}

.table-hover tbody tr:hover {
    background: rgba(41, 80, 168, 0.04);
}

.table th {
    font-weight: 600;
    color: #555;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

/* Progress Bar */
.live-progress {
    transition: width 0.8s cubic-bezier(0.2, 0.9, 0.3, 1);
}

.progress {
    height: 22px;
    border-radius: 8px;
    background: #e9ecef;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

.progress-bar {
    font-size: 12px;
    font-weight: 600;
    line-height: 22px;
}

/* Scrollable */
.scrollable {
    overflow: auto;
    padding-right: 8px;
}

.scrollable::-webkit-scrollbar {
    width: 6px;
}

.scrollable::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.scrollable::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.scrollable::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Skeleton Loading */
.kv-skeleton {
    background: linear-gradient(90deg, #f3f6fb, #eef6ff);
    border-radius: 8px;
    height: 18px;
    display: inline-block;
    width: 100%;
    animation: skeleton 1.5s linear infinite;
}

@keyframes skeleton {
    0% { opacity: 1; }
    50% { opacity: 0.6; }
    100% { opacity: 1; }
}

/* Badges */
.badge-pill {
    border-radius: 50px;
    padding: 0.35em 0.75em;
    font-weight: 500;
    transition: all 0.2s ease;
}

.badge {
    font-size: 85%;
    font-weight: 500;
    padding: 0.4em 0.6em;
    transition: all 0.2s ease;
}

.badge:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

/* Animated Badges */
.badge-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    animation: pulse-success 2s infinite;
}

@keyframes pulse-success {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
    }
    50% {
        box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
    }
}

/* Buttons */
.btn {
    font-weight: 500;
    border-radius: 8px;
    padding: 0.5rem 1.2rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.btn-primary {
    background: linear-gradient(135deg, var(--brand), var(--brand-light));
    border: none;
    box-shadow: 0 4px 12px rgba(41, 80, 168, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--brand-dark), var(--brand));
    box-shadow: 0 6px 20px rgba(41, 80, 168, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.btn-success:hover {
    background: linear-gradient(135deg, #218838, #1ea77e);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

.btn-info {
    background: linear-gradient(135deg, #17a2b8, #5bd0e6);
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
}

.btn-info:hover {
    background: linear-gradient(135deg, #138496, #4abfd1);
    box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
}

.btn-sm {
    padding: 0.4rem 0.9rem;
    font-size: 13px;
}

/* Alerts */
.alert {
    border-radius: 10px;
    border: none;
    box-shadow: var(--shadow-sm);
}

/* Utilities */
.small-muted {
    color: var(--muted);
}

.tooltip-inner {
    max-width: 280px;
    border-radius: 6px;
}

/* Header Brand Card */
.brand-header-card {
    background: linear-gradient(135deg, #2950a8 0%, #2da9e3 100%);
    border: none;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}

.brand-header-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.brand-header-card .card-body {
    position: relative;
    z-index: 1;
}

/* Timeline Styles */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e0e0e0;
}

.timeline-item-active .timeline-marker {
    width: 14px;
    height: 14px;
    box-shadow: 0 0 0 3px rgba(41, 80, 168, 0.2);
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 17px;
    bottom: -5px;
    width: 2px;
    background: #e0e0e0;
}

.timeline-content {
    background: rgba(41, 80, 168, 0.03);
    padding: 12px;
    border-radius: 8px;
    border-left: 3px solid rgba(41, 80, 168, 0.2);
}

.timeline-item-active .timeline-content {
    background: rgba(41, 80, 168, 0.08);
    border-left-color: var(--brand);
}

/* Responsive */
@media (max-width: 767.98px) {
    .algorithm-steps {
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .step {
        min-width: 80px;
    }
    
    .card-body {
        padding: 1rem;
    }
}

/* Table Enhancements */
.table {
    border-collapse: separate;
    border-spacing: 0;
}

.table thead th {
    background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
    color: #6c757d;
    padding: 1rem 0.75rem;
}

.table tbody tr {
    transition: all 0.2s ease;
    border-bottom: 1px solid #f0f0f0;
}

.table tbody tr:hover {
    background: linear-gradient(90deg, rgba(41, 80, 168, 0.03) 0%, rgba(45, 169, 227, 0.03) 100%);
    transform: scale(1.01);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.table tbody td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
    border-top: none;
}

/* KPI Cards with Trend Indicators */
.trend-indicator {
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    border-radius: 12px;
    margin-left: 8px;
}

.trend-up {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.trend-down {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

/* Animated Progress Bars */
.progress {
    border-radius: 50px;
    overflow: visible;
    background: #e9ecef;
}

.progress-bar {
    border-radius: 50px;
    transition: width 1s ease;
    position: relative;
    overflow: visible;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

/* Pulse Animation for Active Elements */
.pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(41, 80, 168, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(41, 80, 168, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(41, 80, 168, 0);
    }
}
</style>

<!-- INFO MODALS -->
        <!-- KYC Info Modal -->
        <div class="modal fade" id="kycInfoModal" tabindex="-1" role="dialog" aria-labelledby="kycInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                    <div class="modal-header border-0" style="background: linear-gradient(135deg, #ffc107, #ffdb4d); color: #fff; border-radius: 15px 15px 0 0;">
                        <h5 class="modal-title font-weight-bold" id="kycInfoModalLabel">
                            <i class="anticon anticon-idcard mr-2"></i>Warum ist die KYC-Verifizierung wichtig?
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <h6 class="text-primary mb-3" style="font-weight: 600;">
                                <i class="anticon anticon-safety-certificate mr-2"></i>Sicherheit & Compliance
                            </h6>
                            <p style="line-height: 1.8; color: #555;">
                                Die KYC (Know Your Customer)-Verifizierung ist eine wichtige Sicherheitsmaßnahme, die sowohl Sie als auch unsere Plattform schützt. 
                                Sie hilft, Betrug und Geldwäsche zu verhindern und stellt sicher, dass Ihre wiederhergestellten Gelder an den rechtmäßigen Eigentümer zurückgegeben werden.
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-success mb-3" style="font-weight: 600;">
                                <i class="anticon anticon-check-circle mr-2"></i>Vorteile der KYC-Verifizierung
                            </h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="anticon anticon-lock text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Erhöhte Sicherheit:</strong> Schützt Ihr Konto vor unbefugtem Zugriff und betrügerischen Aktivitäten.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="anticon anticon-dollar text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Auszahlungen ermöglichen:</strong> Erforderlich, um wiederhergestellte Gelder auf Ihr Bank- oder Krypto-Wallet abzuheben.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="anticon anticon-thunderbolt text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Zugriff auf erweiterte Tools:</strong> Schalten Sie KI-gestützte Wiederherstellungstools und Premium-Support-Services frei.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="anticon anticon-global text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Einhaltung von Vorschriften:</strong> Entspricht internationalen AML (Anti-Geldwäsche)- und CTF (Terrorismusfinanzierung)-Vorschriften.
                                    </div>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="anticon anticon-shield text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Identitätsschutz:</strong> Verhindert Identitätsdiebstahl und stellt sicher, dass Gelder nur in Ihrem Namen wiederhergestellt werden.
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-info border-0" style="background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(23, 162, 184, 0.05)); border-radius: 10px;">
                            <div class="d-flex align-items-start">
                                <i class="anticon anticon-info-circle mr-3" style="font-size: 24px; color: #17a2b8;"></i>
                                <div>
                                    <strong style="color: #17a2b8;">Schneller & einfacher Prozess</strong>
                                    <p class="mb-0 mt-2" style="color: #555;">
                                        Unsere KYC-Verifizierung dauert normalerweise nur 5-10 Minuten. Sie benötigen einen amtlichen Ausweis 
                                        und ein Selfie zur Identitätsbestätigung. Die meisten Verifizierungen werden innerhalb von 24-48 Stunden bearbeitet.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light" style="border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">
                            <i class="anticon anticon-close mr-1"></i>Schließen
                        </button>
                        <a href="kyc.php" class="btn btn-warning" style="border-radius: 8px; font-weight: 500;">
                            <i class="anticon anticon-arrow-right mr-1"></i>KYC-Verifizierung starten
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Crypto Verification Info Modal -->
        <div class="modal fade" id="cryptoInfoModal" tabindex="-1" role="dialog" aria-labelledby="cryptoInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                    <div class="modal-header border-0" style="background: linear-gradient(135deg, #17a2b8, #5bd0e6); color: #fff; border-radius: 15px 15px 0 0;">
                        <h5 class="modal-title font-weight-bold" id="cryptoInfoModalLabel">
                            <i class="anticon anticon-wallet mr-2"></i>Warum Ihre Krypto-Adresse verifizieren?
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <h6 class="text-primary mb-3" style="font-weight: 600;">
                                <i class="anticon anticon-safety mr-2"></i>Schützen Sie Ihre wiederhergestellten Gelder
                            </h6>
                            <p style="line-height: 1.8; color: #555;">
                                Die Verifizierung der Krypto-Wallet ist für eine sichere Wiederherstellung von Geldern unerlässlich. Durch die Verifizierung des Eigentums Ihrer Wallet-Adresse 
                                stellen wir sicher, dass Ihre wiederhergestellten Gelder an das richtige Ziel gesendet werden und unbefugte Auszahlungen verhindert werden.
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-success mb-3" style="font-weight: 600;">
                                <i class="anticon anticon-check-circle mr-2"></i>Wichtige Sicherheitsvorteile
                            </h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="anticon anticon-shield text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Unbefugten Zugriff verhindern:</strong> Stellt sicher, dass nur Sie Gelder auf Ihre verifizierte Wallet-Adresse erhalten können.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="anticon anticon-check-square text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Eigentumsnachweis:</strong> Bestätigt, dass Sie die privaten Schlüssel kontrollieren und die Gelder empfangen können.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="anticon anticon-warning text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Betrugsprävention:</strong> Schützt vor Wallet-Adressen-Ersetzungsangriffen und Phishing-Versuchen.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="anticon anticon-clock-circle text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Schnellere Auszahlungen:</strong> Vorab verifizierte Adressen ermöglichen eine schnellere Bearbeitung von Auszahlungsanträgen.
                                    </div>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="anticon anticon-file-protect text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Compliance & Prüfpfad:</strong> Erstellt eine sichere Aufzeichnung des Eigentums für Regulierungs- und Prüfungszwecke.
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="card border-warning mb-3">
                            <div class="card-body bg-light">
                                <h6 class="text-warning mb-2" style="font-weight: 600;">
                                    <i class="anticon anticon-exclamation-circle mr-2"></i>Verifizierungsprozess
                                </h6>
                                <p class="mb-2" style="color: #555; font-size: 14px;">
                                    Um Ihre Krypto-Wallet zu verifizieren, müssen Sie:
                                </p>
                                <ol class="mb-0" style="color: #555; font-size: 14px; line-height: 2;">
                                    <li>Ihre Wallet-Adresse zu Ihrem Profil hinzufügen</li>
                                    <li>Eine kleine "Satoshi-Test"-Transaktion durchführen (einen winzigen Betrag senden)</li>
                                    <li>Auf die Admin-Genehmigung warten (normalerweise innerhalb von 24 Stunden)</li>
                                </ol>
                            </div>
                        </div>
                        
                        <div class="alert alert-danger border-0" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05)); border-radius: 10px;">
                            <div class="d-flex align-items-start">
                                <i class="anticon anticon-warning mr-3" style="font-size: 24px; color: #dc3545;"></i>
                                <div>
                                    <strong style="color: #dc3545;">Wichtiger Sicherheitshinweis</strong>
                                    <p class="mb-0 mt-2" style="color: #555;">
                                        Ohne Wallet-Verifizierung können Auszahlungsanträge nicht bearbeitet werden. Diese Sicherheitsmaßnahme verhindert Gelddiebstahl 
                                        und stellt sicher, dass wiederhergestellte Vermögenswerte den rechtmäßigen Eigentümer erreichen. Die Verifizierung ist ein einmaliger Prozess.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light" style="border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">
                            <i class="anticon anticon-close mr-1"></i>Schließen
                        </button>
                        <a href="payment-methods.php" class="btn btn-info" style="border-radius: 8px; font-weight: 500;">
                            <i class="anticon anticon-arrow-right mr-1"></i>Krypto-Adresse verifizieren
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Verification Info Modal -->
        <div class="modal fade" id="emailVerifyInfoModal" tabindex="-1" role="dialog" aria-labelledby="emailVerifyInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                    <div class="modal-header border-0" style="background: linear-gradient(135deg, #dc3545, #e74c5d); color: #fff; border-radius: 15px 15px 0 0;">
                        <h5 class="modal-title font-weight-bold" id="emailVerifyInfoModalLabel">
                            <i class="anticon anticon-mail mr-2"></i>Warum Ihre E-Mail-Adresse verifizieren?
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <h6 class="text-primary mb-3" style="font-weight: 600;">
                                <i class="anticon anticon-safety-certificate mr-2"></i>Kontosicherheit & Kommunikation
                            </h6>
                            <p style="line-height: 1.8; color: #555;">
                                Die E-Mail-Verifizierung bestätigt, dass Sie Zugriff auf die mit Ihrem Konto verknüpfte E-Mail-Adresse haben. 
                                Dies ist für sichere Kommunikation, Passwortwiederherstellung und den Empfang wichtiger Benachrichtigungen über Ihre Wiederherstellungsfälle unerlässlich.
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-success mb-3" style="font-weight: 600;">
                                <i class="anticon anticon-check-circle mr-2"></i>Vorteile der E-Mail-Verifizierung
                            </h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="anticon anticon-mail text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Wichtige Benachrichtigungen:</strong> Erhalten Sie sofortige Updates über Ihren Fallstatus, Auszahlungen und Wiederherstellungen.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="anticon anticon-lock text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Kontowiederherstellung:</strong> Aktivieren Sie Optionen zum Zurücksetzen des Passworts und zur Wiederherstellung des Kontos, wenn Sie den Zugriff verlieren.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="anticon anticon-check text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Vollständiges Profil:</strong> Letzter Schritt, um alle Plattformfunktionen und volle Funktionalität freizuschalten.
                                    </div>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="anticon anticon-shield text-success mr-3 mt-1" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>Sicherheitswarnungen:</strong> Werden Sie über verdächtige Aktivitäten oder Anmeldeversuche auf Ihrem Konto benachrichtigt.
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-info border-0" style="background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(23, 162, 184, 0.05)); border-radius: 10px;">
                            <div class="d-flex align-items-start">
                                <i class="anticon anticon-info-circle mr-3" style="font-size: 24px; color: #17a2b8;"></i>
                                <div>
                                    <strong style="color: #17a2b8;">Schneller Verifizierungsprozess</strong>
                                    <p class="mb-0 mt-2" style="color: #555;">
                                        Klicken Sie oben auf die Schaltfläche "Verifizierungs-E-Mail senden", überprüfen Sie Ihren Posteingang auf unsere E-Mail 
                                        und klicken Sie auf den Verifizierungslink. Der Vorgang dauert weniger als 1 Minute.
                                        Der Verifizierungslink läuft aus Sicherheitsgründen nach 1 Stunde ab.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light" style="border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">
                            <i class="anticon anticon-close mr-1"></i>Schließen
                        </button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" style="border-radius: 8px; font-weight: 500;" onclick="$('#sendVerificationEmailBtn').click();">
                            <i class="anticon anticon-mail mr-1"></i>Verifizierungs-E-Mail senden
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- HEADER & BRAND -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card brand-header-card" style="background: <?= htmlspecialchars($brandGradient, ENT_QUOTES) ?>; color: #fff; border: none; overflow: hidden;">
                    <div class="card-body d-flex flex-wrap align-items-center justify-content-between py-4">
                        <div class="brand-content">
                            <div class="h4 mb-2 text-white" style="font-weight: 700; letter-spacing: 0.3px;">
                                <i class="anticon anticon-safety-certificate mr-2"></i>
                                <?= htmlspecialchars($appName, ENT_QUOTES) ?>
                            </div>
                            <div class="lead mb-3" style="color: rgba(255,255,255,0.95); font-size: 1.05rem;">
                                <?= htmlspecialchars($appTagline, ENT_QUOTES) ?>
                            </div>
                            <div class="mt-3 d-flex flex-wrap">
                                <span class="badge badge-light px-3 py-2 mr-2 mb-2" style="color: var(--brand); background: rgba(255,255,255,0.95); font-weight: 500;">
                                    <i class="anticon anticon-lock mr-1"></i> Verschlüsselt & Sicher
                                </span>
                                <span class="badge badge-success px-3 py-2 mr-2 mb-2" id="ai-status-badge" role="status" aria-live="polite" style="font-weight: 500;">
                                    <i class="anticon anticon-check-circle mr-1"></i> KI-Status: <span id="aiStatusText"><?= htmlspecialchars($aiStatus, ENT_QUOTES) ?></span>
                                </span>
                                <span class="badge badge-info px-3 py-2 mb-2" style="font-weight: 500;">
                                    <i class="anticon anticon-clock-circle mr-1"></i> Letzter Scan: <span id="lastScanText"><?= htmlspecialchars($lastAIScan, ENT_QUOTES) ?></span>
                                </span>
                            </div>
                        </div>
                        <div class="text-right mt-3 mt-md-0">
                            <div class="mb-3">
                                <div class="badge badge-pill px-4 py-2" style="font-size: 1.05em; background: rgba(255,255,255,0.2); color: #fff; font-weight: 500;">
                                    <i class="anticon anticon-user mr-1"></i> Willkommen, <?= htmlspecialchars($currentUser['first_name'] ?? $currentUserLogin, ENT_QUOTES) ?>!
                                </div>
                            </div>
                            <div class="mt-2 p-3 rounded" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                                <div class="text-white mb-1" style="font-size: 0.9em; opacity: 0.9; font-weight: 500;">
                                    <i class="anticon anticon-wallet mr-1"></i> Kontostand
                                </div>
                                <div class="h2 font-weight-bold text-white mb-0" id="balanceCounter" data-value="<?= number_format($currentUser['balance'] ?? 0,2, '.', '') ?>">
                                    €<?= number_format($currentUser['balance'] ?? 0,2) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Professional Case Details Modal -->
<div class="modal fade" id="caseDetailsModal" tabindex="-1" role="dialog" aria-labelledby="caseDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #2950a8 0%, #2da9e3 100%); color: #fff; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold" id="caseDetailsModalLabel">
                    <i class="anticon anticon-file-text mr-2"></i>Falldetails
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="caseModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Lädt...</span>
                    </div>
                    <p class="mt-3 text-muted">Lade Falldetails...</p>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light" style="border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="anticon anticon-close mr-1"></i>Schließen
                </button>
            </div>
        </div>
    </div>
</div>


