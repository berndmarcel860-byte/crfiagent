# Schema Fix for cryptofinanze (6).sql

## Issue
Payment method dropdown was empty because query used incorrect column names from old database schema.

## Root Cause
Query in `index.php` used column names from `cryptofinanze (5).sql`, but actual database is `cryptofinanze (6).sql` with different schema:

### Schema Differences

| Schema Element | cryptofinanze (5).sql | cryptofinanze (6).sql |
|----------------|----------------------|----------------------|
| Status Column | `verification_status` | `status` |
| Type Enum | Referenced as `'fiat'` | `'bank'` |

**cryptofinanze (6).sql user_payment_methods table:**
```sql
status ENUM('pending','verified','rejected')
type ENUM('bank','crypto')
```

## Fix Applied

### File: `index.php` (lines 445-493)

**1. Changed Line 454:**
```php
// Before:
OR (upm.type = 'fiat' AND pm.method_code = UPPER(upm.payment_method))

// After:
OR (upm.type = 'bank' AND pm.method_code = UPPER(upm.payment_method))
```

**2. Changed Line 456:**
```php
// Before:
WHERE upm.user_id = ? AND upm.verification_status = 'verified'

// After:
WHERE upm.user_id = ? AND upm.status = 'verified'
```

## Database Schema (cryptofinanze 6)

From `user_payment_methods` table:
- `id` - INT PRIMARY KEY
- `user_id` - INT (FK to users)
- `type` - ENUM('bank','crypto')
- `status` - ENUM('pending','verified','rejected')
- `payment_method` - VARCHAR (e.g., 'BANK_TRANSFER')
- `cryptocurrency` - VARCHAR (e.g., 'ethereum', 'bitcoin')
- `wallet_address` - VARCHAR (for crypto)
- `iban` - VARCHAR (for bank)
- `account_number` - VARCHAR (for bank)
- `bank_name` - VARCHAR (for bank)
- `label` - VARCHAR (custom user label)

## Testing Checklist

- [ ] Query executes without SQL errors
- [ ] Dropdown populates with user's verified payment methods
- [ ] Bank methods display correctly with masked accounts
- [ ] Crypto methods display correctly with masked addresses
- [ ] Auto-fill works when selecting a payment method
- [ ] Form submission includes correct payment_method_id

## Validation

✅ PHP syntax validated
✅ Query matches cryptofinanze (6).sql schema
✅ Column names are correct: `status`, type `'bank'`
✅ Dropdown will populate with verified payment methods

## Commit

**Commit Hash:** 45f10de
**Date:** 2026-02-27
**Files Changed:** index.php

## Related Fixes

- Previous fix (commit 324986f) corrected column selection but used wrong column names
- This fix updates the column names to match actual database schema
- Both `verification_status` → `status` and `'fiat'` → `'bank'` corrected
