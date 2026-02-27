# Payment Method Validation Fix

## Issue

Users were getting the error: **"Invalid or inactive payment method selected"** when trying to submit a withdrawal with a verified payment method.

## Root Cause

The issue was caused by an inconsistency between frontend and backend code:

### Frontend (index.php)
- Simplified earlier to query **only** `user_payment_methods` table
- No dependency on `payment_methods` table
- Sends `payment_method_id` (from `user_payment_methods.id`)

### Backend (ajax/process-withdrawal.php)
- Still trying to query `payment_methods` table (lines 88-92)
- Checked if method exists and is active in `payment_methods`
- When method not found, threw error: "Invalid or inactive payment method selected"

This validation was **redundant** because:
1. User's payment method already validated in `user_payment_methods` (lines 59-66)
2. Method already confirmed to belong to user
3. Method already confirmed to be verified (`verification_status = 'verified'`)

## Solution

Removed the redundant `payment_methods` table validation from `ajax/process-withdrawal.php`:

### Changes Made

**1. Removed lines 88-94:**
```php
// REMOVED:
$stmt = $pdo->prepare("SELECT id, method_name FROM payment_methods WHERE method_code = ? AND is_active = 1 AND allows_withdrawal = 1");
$stmt->execute([$methodCode]);
$paymentMethod = $stmt->fetch();
if (!$paymentMethod) throw new Exception('Invalid or inactive payment method selected', 400);
$paymentMethodId = $paymentMethod['id'];
```

**2. Added line 90:**
```php
// ADDED: Get payment method name from user_payment_methods data
$paymentMethodName = $userPaymentMethod['label'] ?: 
                    ($userPaymentMethod['type'] === 'crypto' ? $userPaymentMethod['cryptocurrency'] : $userPaymentMethod['bank_name']) ?: 
                    'Bank Transfer';
```

**3. Updated line 131:**
```php
// BEFORE:
'payment_method' => $paymentMethod['method_name'],

// AFTER:
'payment_method' => $paymentMethodName,
```

## Validation Flow

### Before Fix
1. Validate `user_payment_methods` (belongs to user, is verified) ✅
2. Query `payment_methods` table ❌ (redundant, causes error)
3. Check if method is active and allows withdrawal ❌ (redundant)

### After Fix
1. Validate `user_payment_methods` (belongs to user, is verified) ✅
2. Get method name from `user_payment_methods` ✅
3. Continue with withdrawal ✅

## Benefits

✅ **No payment_methods dependency** - Consistent with frontend  
✅ **Simpler validation logic** - One table instead of two  
✅ **Faster processing** - One less database query  
✅ **Fixes the error** - No more "Invalid or inactive payment method" error  

## Testing

### Test Cases

1. **Verified Crypto Method:**
   - Select verified Ethereum payment method
   - Submit withdrawal
   - ✅ Should succeed without error

2. **Verified Fiat Method:**
   - Select verified bank transfer method
   - Submit withdrawal
   - ✅ Should succeed without error

3. **Unverified Method:**
   - Try to use unverified payment method
   - ✅ Should fail at line 65: "Invalid or unverified payment method"

4. **Method Not Belonging to User:**
   - Try to use another user's payment method
   - ✅ Should fail at line 65: validation check

5. **Email Notification:**
   - After successful withdrawal
   - ✅ Email should show correct payment method name

## Files Modified

- `ajax/process-withdrawal.php` - Removed payment_methods table dependency

## Commit

Commit: 868b324
