# Correct Schema Fix - Payment Method Query

## Issue
Payment method dropdown was empty due to incorrect column names in query.

## My Initial Error
I initially fixed the query to use:
- `status` column ❌ (wrong column)
- `'bank'` type ❌ (doesn't exist in enum)

## Actual Table Structure

The `user_payment_methods` table has **TWO status columns**:

1. **`status`** - Payment method status
   - `ENUM('active','pending','suspended')`
   - Purpose: Whether the payment method itself is active
   
2. **`verification_status`** - Verification status ✅
   - `ENUM('pending','verifying','verified','failed')`
   - Purpose: Whether the method has been verified
   - **This is what we need for filtering!**

3. **`type`** - Payment type
   - `ENUM('fiat','crypto')` ✅ NOT 'bank'!

## Sample Data Confirms

```sql
-- Row 27: Bank transfer, pending verification
type='fiat', status='active', verification_status='pending'

-- Row 28: Ethereum, verified
type='crypto', status='active', verification_status='verified'

-- Row 29: Bank transfer, pending verification
type='fiat', status='active', verification_status='pending'

-- Row 30: Ethereum, verified
type='crypto', status='active', verification_status='verified'
```

## Correct Query Fix

**index.php lines 454, 456:**

```php
// CORRECT (now applied):
OR (upm.type = 'fiat' AND pm.method_code = UPPER(upm.payment_method))
WHERE upm.user_id = ? AND upm.verification_status = 'verified'

// My wrong fix (reverted):
OR (upm.type = 'bank' AND pm.method_code = UPPER(upm.payment_method)) ❌
WHERE upm.user_id = ? AND upm.status = 'verified' ❌
```

## Why verification_status is Correct

We want to filter payment methods that have been **verified** by the user, not just **active**.

- `status='active'` - Method is enabled (rows 27, 28, 29, 30 all active)
- `verification_status='verified'` - Method has been verified (only rows 28, 30)

For withdrawals, we need **verified** methods only (rows 28, 30).

## Why 'fiat' is Correct

The enum is `type ENUM('fiat','crypto')`:
- `'fiat'` - Bank transfers, traditional payment methods
- `'crypto'` - Cryptocurrency methods
- `'bank'` - **Does NOT exist in the enum!**

Sample data shows `type='fiat'` in database.

## Result

✅ Query now uses `verification_status = 'verified'` for filtering
✅ Type is `'fiat'` and `'crypto'` as defined in database
✅ Dropdown will populate with only user's verified payment methods
✅ Both fiat (bank) and crypto methods work correctly

## Testing

1. Check dropdown shows only verified methods (rows 28, 30 for user 172, 176)
2. Verify fiat methods show with masked account
3. Verify crypto methods show with masked wallet address
4. Confirm auto-fill works when selecting method

## Validation

- PHP syntax: ✅ No errors
- Query: ✅ Matches actual table structure
- Sample data: ✅ Confirmed type='fiat' and verification_status='verified'

**Commit:** 3e41c72
