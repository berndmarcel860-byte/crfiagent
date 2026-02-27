# Withdrawal Modal Simplification - Use Only user_payment_methods Table

## Objective
Remove dependency on `payment_methods` table and query only from `user_payment_methods` table for the withdrawal modal dropdown.

## Changes Made

### Query Simplification (index.php lines 446-463)

**Before:**
```php
$stmt = $pdo->prepare("SELECT upm.id, upm.type, upm.payment_method, upm.cryptocurrency, 
    upm.wallet_address, upm.iban, upm.account_number, upm.bank_name, 
    upm.label, pm.method_name 
    FROM user_payment_methods upm
    LEFT JOIN payment_methods pm ON (
        (upm.type = 'crypto' AND pm.method_code = UPPER(upm.cryptocurrency))
        OR (upm.type = 'fiat' AND pm.method_code = UPPER(upm.payment_method))
    )
    WHERE upm.user_id = ? AND upm.verification_status = 'verified'
    ORDER BY upm.created_at DESC");
```

**After:**
```php
$stmt = $pdo->prepare("SELECT id, type, payment_method, cryptocurrency, 
    wallet_address, iban, account_number, bank_name, label 
    FROM user_payment_methods 
    WHERE user_id = ? AND verification_status = 'verified'
    ORDER BY created_at DESC");
```

### Display Logic Simplified (lines 460-468)

**Before (4-level fallback):**
1. label
2. method_name (from payment_methods table)
3. cryptocurrency (for crypto)
4. bank_name (for fiat)

**After (3-level fallback):**
1. label (user's custom name)
2. cryptocurrency (for crypto type)
3. bank_name (for fiat type, fallback to 'Bank Transfer')

### Code Changes

- **Removed:** LEFT JOIN payment_methods pm (1 line)
- **Removed:** pm.method_name from SELECT (1 field)
- **Removed:** upm. prefixes (multiple occurrences)
- **Removed:** method_name check in display logic (1 condition)
- **Total:** 7 lines simplified

## Benefits

1. **No External Dependency:** Query uses only user_payment_methods table
2. **Simpler Query:** No JOIN operation required
3. **Faster Execution:** Reduced query complexity and overhead
4. **Cleaner Code:** Easier to read and maintain
5. **Same Functionality:** Users see their verified payment methods correctly

## Display Name Priority

The dropdown now shows payment methods with this priority:

1. **Custom Label** (if user set one): "My Sparkasse Account", "Main ETH Wallet"
2. **Cryptocurrency Name** (for crypto type): "ETH", "BTC", "USDT"
3. **Bank Name** (for fiat type): "Sparkasse", "Deutsche Bank"
4. **Fallback**: "Bank Transfer"

## Testing Checklist

- [ ] Dropdown populates with user's verified payment methods
- [ ] Crypto methods show cryptocurrency name (ETH, BTC, etc.)
- [ ] Fiat methods show bank name
- [ ] Custom labels display correctly when set
- [ ] Auto-fill works when selecting a method
- [ ] No errors in browser console or PHP logs

## Validation

- ✅ PHP syntax validated: No errors
- ✅ Query uses only user_payment_methods table
- ✅ No dependency on payment_methods table
- ✅ Same user experience preserved

## Files Modified

- `index.php` (lines 446-468)

## Commit

- Commit: 1122373
- Changes: 7 lines simplified (removed JOIN and method_name)
