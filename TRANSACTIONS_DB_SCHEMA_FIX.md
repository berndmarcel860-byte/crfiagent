# Transactions Database Schema Fix

## Issue
**Error:** "Failed to load transactions"

The transactions page was showing an error when trying to load transaction data.

## Root Cause

The `ajax/transactions.php` file was trying to SELECT columns from the `withdrawals` table that don't exist in the actual database schema (cryptofinanze (6).sql).

### Columns Code Tried to Use (❌ = doesn't exist):
- `w.otp_verified` ❌ (doesn't exist in withdrawals table)
- `w.rejected_reason` ❌ (doesn't exist)
- `w.approved_at` ❌ (doesn't exist)
- `w.rejected_at` ❌ (doesn't exist)

### Actual Withdrawals Table Columns (from cryptofinanze (6).sql):
```sql
CREATE TABLE `withdrawals` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `method_code` varchar(50) NOT NULL,
  `payment_details` text NOT NULL,
  `status` enum('pending','processing','completed','failed','cancelled'),
  `reference` varchar(100) NOT NULL,
  `admin_notes` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `admin_id` int DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `processed_by` int DEFAULT NULL
)
```

**Note:** The `otp_verified` field exists in the `transactions` table, not in `withdrawals`.

## Solution

### 1. Updated ajax/transactions.php (lines 37-69)

**Removed non-existent columns:**
- `w.otp_verified` → Changed to `t.otp_verified` (exists in transactions table)
- `w.rejected_reason` → Removed (doesn't exist)
- `w.approved_at` → Removed (doesn't exist)
- `w.rejected_at` → Removed (doesn't exist)

**Added actual columns:**
- `w.processed_at` ✅ (exists in withdrawals)
- `w.updated_at` ✅ (exists in withdrawals)

**Before (incorrect):**
```php
SELECT 
    ...
    w.otp_verified,
    w.admin_notes,
    w.rejected_reason,
    w.approved_at,
    w.rejected_at
FROM transactions t
LEFT JOIN withdrawals w ...
```

**After (correct):**
```php
SELECT 
    ...
    t.otp_verified,
    w.admin_notes,
    w.processed_at,
    w.updated_at
FROM transactions t
LEFT JOIN withdrawals w ...
```

### 2. Updated transactions.php (lines 279-306)

Updated the modal population logic to use the fields that actually exist:

**Changes:**
- Use `processed_at` for "Approved Date" when status is 'completed'
- Use `updated_at` for "Rejected Date" when status is 'failed' or 'cancelled'
- Hide `rejected_reason` section (field doesn't exist in database)
- Keep `admin_notes` (exists and can contain rejection reasons)

**Code:**
```javascript
// Use processed_at for approved date if status is completed
if (rowData.status && rowData.status.toLowerCase() === 'completed' && rowData.processed_at) {
    $('#approved-date-group').show();
    $('#detail-approved').text(formatDate(rowData.processed_at));
} else {
    $('#approved-date-group').hide();
}

// Use updated_at for rejected date if status is failed/cancelled
if ((rowData.status && (rowData.status.toLowerCase() === 'failed' || rowData.status.toLowerCase() === 'cancelled')) && rowData.updated_at) {
    $('#rejected-date-group').show();
    $('#detail-rejected').text(formatDate(rowData.updated_at));
} else {
    $('#rejected-date-group').hide();
}

// Hide rejected_reason section (doesn't exist in DB)
$('#rejected-reason-group').hide();
```

## Files Modified

1. **ajax/transactions.php**
   - Lines 37-69: Updated SELECT query
   - Lines 92-110: Updated returned data structure

2. **transactions.php**
   - Lines 279-306: Updated modal population logic

## Testing

### Test Cases:
1. ✅ Load transactions page - should display without errors
2. ✅ DataTable displays transaction list
3. ✅ Search functionality works
4. ✅ Click "View Details" on a withdrawal
5. ✅ Modal shows withdrawal information
6. ✅ Completed withdrawals show "Approved Date" (using processed_at)
7. ✅ Failed withdrawals show "Rejected Date" (using updated_at)
8. ✅ Admin notes display correctly

## Result

✅ **Transactions page loads successfully**  
✅ **DataTable displays transaction data correctly**  
✅ **Modal shows withdrawal details with actual database fields**  
✅ **No SQL errors**  
✅ **Code aligned with cryptofinanze (6).sql database schema**

## Related Commits
- Commit: c03de94 - "Fix transactions loading error - align with cryptofinanze (6).sql database schema"
