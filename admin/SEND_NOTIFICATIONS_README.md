# Send Notifications System (Benachrichtigungen senden)

## Überblick

Das Send Notifications System ist eine umfassende Lösung zum Senden von Massen-E-Mails an Benutzer basierend auf fortgeschrittenen Filterkriterien. Alle Inhalte sind auf Deutsch.

## Installation

### 1. Deutsche E-Mail-Vorlagen importieren

```bash
mysql -u username -p database_name < admin/german_email_templates.sql
```

Oder über phpMyAdmin:
1. Datenbank auswählen
2. "Importieren" Tab öffnen
3. `german_email_templates.sql` auswählen
4. "OK" klicken

### 2. Überprüfen der Installation

```sql
SELECT template_key, subject FROM email_templates WHERE template_key LIKE '%_de';
```

Sie sollten 6 deutsche Vorlagen sehen.

## Zugriff

**Navigation**: Admin Panel → Kommunikation → Benachrichtigungen senden

**URL**: `admin/admin_send_notifications.php`

**Berechtigung**: Admin-Login erforderlich

## Funktionen

### 1. Benutzerfilter (6 Kategorien)

#### KYC-Status
- **Kein KYC**: Benutzer ohne KYC-Anfrage
- **Ausstehend**: KYC-Anfrage ausstehend
- **Abgelehnt**: KYC wurde abgelehnt
- **Genehmigt**: KYC erfolgreich verifiziert

#### Login-Aktivität
- **Nie angemeldet**: Benutzer, die sich noch nie eingeloggt haben
- **7+ Tage inaktiv**: Letzter Login vor mehr als 7 Tagen
- **14+ Tage inaktiv**: Letzter Login vor mehr als 14 Tagen
- **30+ Tage inaktiv**: Letzter Login vor mehr als 30 Tagen
- **60+ Tage inaktiv**: Letzter Login vor mehr als 60 Tagen
- **90+ Tage inaktiv**: Letzter Login vor mehr als 90 Tagen

#### Guthaben
- **Hat Guthaben (> 0€)**: Alle Benutzer mit positivem Guthaben
- **Hohes Guthaben (> 100€)**: Guthaben über 100€
- **Sehr hohes Guthaben (> 500€)**: Guthaben über 500€
- **Kein Guthaben**: Guthaben = 0€

#### Onboarding-Status
- **Unvollständig**: Onboarding nicht abgeschlossen
- **Vollständig**: Onboarding abgeschlossen

#### Benutzerstatus
- **Aktiv**: Aktive Benutzer
- **Gesperrt**: Gesperrte Benutzer
- **Ausstehend**: Benutzer mit ausstehender Aktivierung

#### E-Mail-Verifizierung
- **Verifiziert**: E-Mail-Adresse bestätigt
- **Nicht verifiziert**: E-Mail-Adresse nicht bestätigt

### 2. Benutzerliste

**Funktionen**:
- Checkbox-Auswahl (einzeln oder alle)
- Sortierung nach allen Spalten
- Suche nach Name oder E-Mail
- 25 Benutzer pro Seite (konfigurierbar)
- Farbcodierte Status-Badges

**Anzeige**:
- ID
- Name
- E-Mail
- KYC-Status (farbcodiert)
- Letzter Login (mit Tagen-Indikator)
- Guthaben (farbcodiert)
- Status
- Onboarding-Status

### 3. E-Mail-Versand

**Workflow**:
1. Filter anwenden
2. Benutzer auswählen (Checkboxen)
3. "An ausgewählte senden" klicken
4. E-Mail-Vorlage auswählen
5. Vorschau anzeigen (optional)
6. Bestätigen und senden

**Sicherheit**:
- Bestätigungsdialog vor dem Senden
- Vorschau vor dem Senden möglich
- Maximum 500 Benutzer pro Batch
- Rate Limiting (0.1s Verzögerung pro 10 E-Mails)

## Deutsche E-Mail-Vorlagen

### 1. kyc_reminder_de
**Zweck**: KYC-Verifizierung Erinnerung  
**Betreff**: Vervollständigen Sie Ihre KYC-Verifizierung - FundTracer AI  
**Verwendung**: Für Benutzer ohne abgeschlossene KYC

**Variablen**:
- first_name, last_name, email
- kyc_url, support_email

### 2. login_reminder_de
**Zweck**: Login-Erinnerung für neue Benutzer  
**Betreff**: Melden Sie sich bei Ihrem FundTracer AI Konto an  
**Verwendung**: Für Benutzer, die sich noch nie eingeloggt haben

**Variablen**:
- first_name, last_name, email
- login_url, reset_password_url

### 3. withdraw_reminder_de
**Zweck**: Auszahlungserinnerung bei Guthaben  
**Betreff**: Guthaben verfügbar - Jetzt Auszahlung beantragen  
**Verwendung**: Für Benutzer mit positivem Guthaben

**Variablen**:
- first_name, last_name, email
- balance, withdrawal_url, support_email

### 4. onboarding_reminder_de
**Zweck**: Onboarding-Vervollständigung  
**Betreff**: Vervollständigen Sie Ihr Profil - FundTracer AI  
**Verwendung**: Für Benutzer mit unvollständigem Onboarding

**Variablen**:
- first_name, last_name, email
- onboarding_url, missing_step_1, missing_step_2, missing_step_3

### 5. inactive_user_de
**Zweck**: Inaktivitätserinnerung  
**Betreff**: Wir vermissen Sie bei FundTracer AI - {{first_name}}  
**Verwendung**: Für inaktive Benutzer

**Variablen**:
- first_name, last_name, email
- days_inactive, login_url, case_number

### 6. balance_alert_de
**Zweck**: Guthaben-Benachrichtigung  
**Betreff**: 💰 Wichtig: Guthaben auf Ihrem Konto - {{first_name}}  
**Verwendung**: Für Benutzer mit hohem Guthaben

**Variablen**:
- first_name, last_name, email
- balance, withdrawal_url, min_withdrawal, max_withdrawal, support_email

## Verwendungsbeispiele

### Beispiel 1: KYC-Erinnerungen an alle senden

1. Filter: **KYC-Status** → "Kein KYC"
2. Filter anwenden
3. "Alle auswählen" Checkbox aktivieren
4. "An ausgewählte senden" klicken
5. Vorlage: **kyc_reminder_de**
6. Senden

### Beispiel 2: Inaktive Benutzer mit Guthaben

1. Filter: **Login-Aktivität** → "30+ Tage inaktiv"
2. Filter: **Guthaben** → "Hat Guthaben (> 0€)"
3. Filter anwenden
4. Benutzer auswählen
5. Vorlage: **withdraw_reminder_de**
6. Senden

### Beispiel 3: Neue Benutzer, die sich nie eingeloggt haben

1. Filter: **Login-Aktivität** → "Nie angemeldet"
2. Filter anwenden
3. Benutzer auswählen
4. Vorlage: **login_reminder_de**
5. Senden

### Beispiel 4: Benutzer mit unvollständigem Onboarding

1. Filter: **Onboarding-Status** → "Unvollständig"
2. Filter anwenden
3. Benutzer auswählen
4. Vorlage: **onboarding_reminder_de**
5. Senden

## Technische Details

### Datenbankstruktur

**Verwendete Tabellen**:
- `email_templates` - Speichert E-Mail-Vorlagen
- `email_logs` - Protokolliert gesendete E-Mails
- `audit_logs` - Protokolliert Admin-Aktionen
- `users` - Benutzerdaten
- `kyc_verification_requests` - KYC-Status

### Dateien

**Frontend**:
- `admin/admin_send_notifications.php` - Hauptseite

**Backend (AJAX)**:
- `admin_ajax/get_filtered_users.php` - Benutzer-Filterung
- `admin_ajax/send_bulk_notifications.php` - Massen-E-Mail-Versand
- `admin_ajax/preview_notification.php` - E-Mail-Vorschau

**Vorlagen**:
- `admin/german_email_templates.sql` - Deutsche Vorlagen

**Helper**:
- `admin/email_template_helper.php` - E-Mail-Template-System

### API-Endpunkte

#### GET /admin_ajax/get_filtered_users.php
**Parameter** (POST):
- `filters` (JSON): Filter-Objekt
- `draw`, `start`, `length` (DataTables)
- `search` (Suchbegriff)
- `order` (Sortierung)

**Response**:
```json
{
  "draw": 1,
  "recordsTotal": 100,
  "recordsFiltered": 25,
  "data": [...]
}
```

#### POST /admin_ajax/send_bulk_notifications.php
**Parameter**:
- `template_key` (string): Vorlagen-Schlüssel
- `users` (JSON): Array von Benutzern

**Response**:
```json
{
  "success": true,
  "sent": 20,
  "failed": 0,
  "total": 20,
  "errors": []
}
```

#### POST /admin_ajax/preview_notification.php
**Parameter**:
- `template_key` (string): Vorlagen-Schlüssel

**Response**:
```json
{
  "success": true,
  "preview": "<html>...",
  "subject": "Betreff"
}
```

## Performance

**Optimierungen**:
- Server-seitiges DataTables Processing
- Batch-Verarbeitung mit Rate Limiting
- Indizierte Datenbankabfragen
- Effiziente Filter-Kombinationen

**Limits**:
- Maximum 500 Benutzer pro Batch
- 0.1s Verzögerung pro 10 E-Mails
- 25 Benutzer pro Seite (anpassbar)

## Protokollierung

**E-Mail-Logs** (`email_logs` Tabelle):
- Empfänger
- Betreff
- Vorlagen-Schlüssel
- Status (sent/failed)
- Zeitstempel
- Benutzer-ID
- Admin-ID

**Audit-Logs** (`audit_logs` Tabelle):
- Admin-ID
- Aktion: 'bulk_email'
- Details: Template, Anzahl gesendet/fehlgeschlagen
- IP-Adresse
- User-Agent
- Zeitstempel

## Fehlerbehebung

### E-Mails werden nicht gesendet

**Prüfen**:
1. SMTP-Einstellungen in Admin-Panel
2. PHP `mail()` Funktion aktiviert
3. E-Mail-Logs für Fehler prüfen
4. PHP error_log überprüfen

### Vorlagen nicht sichtbar

**Lösung**:
```sql
-- Prüfen ob Vorlagen existieren
SELECT * FROM email_templates WHERE template_key LIKE '%_de';

-- Falls nicht, SQL-Datei importieren
SOURCE admin/german_email_templates.sql;
```

### Filter funktionieren nicht

**Prüfen**:
1. Browser-Konsole auf JavaScript-Fehler
2. Network-Tab für AJAX-Fehler
3. PHP error_log für Backend-Fehler
4. Datenbankverbindung

### Benutzer werden nicht gefunden

**Prüfen**:
1. Benutzer in der Datenbank vorhanden
2. Filter-Kombinationen korrekt
3. Datenbankabfrage in `get_filtered_users.php`

## Best Practices

### 1. Testen vor dem Versand
- Immer Vorschau verwenden
- Mit kleiner Benutzergruppe testen
- E-Mail-Logs überprüfen

### 2. Zielgruppenauswahl
- Spezifische Filter verwenden
- Relevante Vorlagen auswählen
- Zeitpunkt beachten

### 3. Vorlagen-Pflege
- Regelmäßig aktualisieren
- Auf Rechtschreibung achten
- Links testen

### 4. Monitoring
- E-Mail-Logs überwachen
- Bounce-Rate prüfen
- Benutzer-Feedback beachten

## Support

Bei Fragen oder Problemen:
- E-Mail-Logs überprüfen: Admin Panel → Kommunikation → E-Mail-Logs
- Audit-Logs prüfen: Admin Panel → System → Audit-Logs
- Fehlerprotokoll: `/admin/admin_ajax/error.log`
- Support: support@fundtracerai.com

## Zukünftige Erweiterungen

Mögliche Verbesserungen:
- [ ] Geplanter E-Mail-Versand
- [ ] A/B-Testing von Vorlagen
- [ ] Erweiterte Statistiken
- [ ] E-Mail-Öffnungsrate Tracking
- [ ] Antwort-Tracking
- [ ] Mehr Filter-Optionen
- [ ] Export von Benutzerlisten
- [ ] Vorlagen-Editor mit Drag & Drop

## Lizenz

Proprietär - FundTracer AI Platform
