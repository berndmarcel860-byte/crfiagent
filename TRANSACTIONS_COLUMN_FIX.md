# Transactions Query Column Fix

## Issue Fixed
**Error:** `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'd.payment_method' in 'field list'`

**Location:** `ajax/transactions.php` line 98

**Date:** 2026-03-01

---

## Problem
The deposits query was using column names that don't exist in the actual deposits table schema.

---

## Solution

### Column Name Corrections

| Incorrect | Correct | Notes |
|-----------|---------|-------|
| `d.payment_method` | `d.method_code` | Payment method identifier |
| `d.confirmed_at` | `d.processed_at` | Processing timestamp |
| `d.confirmed_by` | `d.processed_by` | Admin who processed |
| `d.transaction_id` | `NULL` | Column doesn't exist |
| `d.ip_address` | `NULL` | Column doesn't exist |

---

## Deposits Table Schema

```sql
CREATE TABLE `deposits` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `method_code` varchar(50) NOT NULL,      -- Payment method
  `reference` varchar(50) NOT NULL,
  `proof_path` varchar(255) NOT NULL,
  `payment_details` text,
  `admin_notes` text,
  `processed_by` int DEFAULT NULL,         -- Admin ID
  `processed_at` datetime DEFAULT NULL,    -- Processing time
  `status` enum('pending','completed','failed'),
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `admin_id` int DEFAULT NULL
)
```

**Key Points:**
- Uses `method_code` NOT `payment_method`
- Uses `processed_at`/`processed_by` NOT `confirmed_at`/`confirmed_by`
- No `transaction_id` column
- No `ip_address` column

---

## Fixed Query

```php
$query = "
    SELECT 
        d.id,
        'deposit' as type,
        d.amount,
        d.status,
        d.reference,
        d.created_at,
        d.method_code as method_display,     -- ✅ Fixed
        d.proof_path as details,
        NULL as withdrawal_id,
        d.id as deposit_id,
        d.method_code,                       -- ✅ Fixed
        NULL as otp_verified,
        d.admin_notes,
        d.processed_at,                      -- ✅ Fixed
        d.updated_at,
        NULL as transaction_id,              -- ✅ Fixed (doesn't exist)
        d.processed_by,                      -- ✅ Fixed
        NULL as ip_address                   -- ✅ Fixed (doesn't exist)
    FROM deposits d
    WHERE d.user_id = :user_id1
";
```

---

## Testing

**Verify the fix:**
1. Load transactions page: Should load without errors
2. Check browser console: No SQL errors
3. View deposit details: All data displays correctly
4. DataTables working: Search, sort, pagination all functional

---

## Result

✅ Database error resolved
✅ Transactions page loads successfully
✅ Query aligned with schema
✅ No data loss

**Commit:** 72e29e4
**Status:** Fixed ✅
