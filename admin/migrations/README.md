# Database Migrations

This directory contains database migration files for the CRFIAgent system.

## Migration: Add Address and Reference Number to System Settings

**File**: `add_address_reference_to_settings.sql`  
**Date**: 2026-02-11

### Purpose
Adds dynamic company address and FCA reference number fields to the `system_settings` table, replacing hardcoded values in email templates.

### Changes
1. Adds `company_address` TEXT column to store company physical address
2. Adds `fca_reference_number` VARCHAR(50) column to store FCA reference number
3. Updates existing record with current values

### How to Apply

**Option 1: MySQL Command Line**
```bash
mysql -u username -p database_name < admin/migrations/add_address_reference_to_settings.sql
```

**Option 2: phpMyAdmin**
1. Open phpMyAdmin
2. Select your database
3. Go to SQL tab
4. Copy and paste the contents of the migration file
5. Click "Go"

**Option 3: Import Full Schema**
The updated schema is already included in `admin/cryptofinanze.sql`. Import the full SQL file to get all tables with the latest structure.

### Verification
After applying the migration, verify the columns were added:
```sql
SHOW COLUMNS FROM system_settings;
```

You should see:
- `company_address` (text)
- `fca_reference_number` (varchar(50))

### Impact
- Email templates now use dynamic company address from database
- FCA reference number is now configurable via admin panel
- Admins can update these values without code changes

### Default Values
- `company_address`: "Davidson House Forbury Square, Reading, RG1 3EU, UNITED KINGDOM"
- `fca_reference_number`: "910584"

These values can be updated through the admin settings panel.
