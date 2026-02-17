# Database Files

## Main Database File

**File:** `admin/cryptofinanze.sql`  
**Description:** Primary database schema and structure for the CryptoFinanze application

This is the main database dump file that should be imported to set up the complete database structure.

## Updated Database File

**File:** `admin/cryptofinanze_updated.sql`  
**Description:** Updated database schema (February 17, 2026)  
**Tables:** 46 total tables

### What's New in the Updated Version:

1. **cryptocurrencies** table
   - Stores supported cryptocurrencies (BTC, ETH, USDT, etc.)
   - Includes symbol, name, icon, and active status
   
2. **crypto_networks** table
   - Stores blockchain networks for each cryptocurrency
   - Supports multiple networks per crypto (ERC-20, TRC-20, BEP-20, etc.)
   - Includes chain_id and explorer_url

### Additional Updates:

The updated file includes all tables from migrations that were run after the original database export:
- Migration 003: Enhanced user_payment_methods
- Migration 004: Wallet verification system
- Migration 005: Cryptocurrency and network tables

## How to Use

### For New Installation:

```bash
# Use the updated file for new installations
mysql -u username -p database_name < admin/cryptofinanze_updated.sql
```

### For Existing Installation:

If you already have the database, run the migration scripts instead:

```bash
# Run each migration in order
mysql -u username -p database_name < admin/migrations/003_enhance_user_payment_methods.sql
mysql -u username -p database_name < admin/migrations/004_add_wallet_verification_system.sql
mysql -u username -p database_name < admin/migrations/005_create_crypto_and_network_tables.sql
```

## Database Structure

### Total Tables: 46

**Core Tables:**
- admins
- users
- cases
- deposits
- withdrawals
- transactions

**Authentication & Security:**
- admin_login_logs
- login_logs
- remember_tokens
- otp_logs

**Cryptocurrency Management:**
- cryptocurrencies (NEW)
- crypto_networks (NEW)
- user_payment_methods

**KYC & Verification:**
- kyc_verifications
- kyc_verification_requests
- documents

**Support & Communication:**
- support_tickets
- ticket_replies
- email_logs
- email_templates

**System Settings:**
- system_settings
- smtp_settings
- audit_logs

... and more (46 tables total)

## Notes

- Both files are complete database dumps from phpMyAdmin
- File size: approximately 4.0 MB each
- Character set: utf8mb4
- Includes all foreign key constraints
- Contains sample/test data

## Troubleshooting

If you encounter errors during import:

1. **Check MySQL version:** Requires MySQL 8.0 or higher
2. **Check permissions:** Ensure user has CREATE, ALTER, INSERT privileges
3. **Check character set:** Database should use utf8mb4
4. **Drop existing database:** If updating, drop and recreate database first

```bash
# Drop and recreate (WARNING: This deletes all data!)
mysql -u username -p -e "DROP DATABASE IF EXISTS database_name; CREATE DATABASE database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u username -p database_name < admin/cryptofinanze_updated.sql
```

## File History

- **Original:** `admin/cryptofinanze.sql` (44 tables)
- **Updated:** `admin/cryptofinanze_updated.sql` (46 tables - includes cryptocurrency tables)
- **Date:** February 17, 2026
- **Exported from:** phpMyAdmin 4.9.5deb2
- **MySQL Version:** 8.0.42

## Support

For issues with database import or structure, check:
1. `debug_crypto_system.php` - Test database connectivity and tables
2. `test_crypto_tables.php` - Verify cryptocurrency tables exist
3. Migration files in `admin/migrations/` - Individual schema updates
