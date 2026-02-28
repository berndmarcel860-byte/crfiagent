# MySQL Collation Mismatch Fix

## Issue
**Error:** "General error 1267: Illegal mix of collations (utf8mb4_0900_ai_ci, utf8mb4_unicode_ci)"

This error occurs when MySQL tries to compare or join string columns with different character set collations.

## Root Cause
The database schema (`cryptofinanze (6).sql`) has mixed collations:
- Some tables use `utf8mb4_0900_ai_ci` as default collation
- Some columns explicitly use `utf8mb4_unicode_ci` collation

When JOIN conditions compare columns with different collations, MySQL rejects the query with error 1267.

## Files Fixed

### 1. ajax/transactions.php
Fixed 5 JOIN conditions:

**Lines 63-66:** Main query JOINs
- `t.reference = w.reference` (transactions to withdrawals)
- `w.method_code = upm.payment_method` (withdrawals to user_payment_methods)
- `t.reference = d.reference` (transactions to deposits)

**Lines 121-122:** Filtered count query JOINs
- `t.reference = w.reference` (transactions to withdrawals)
- `w.method_code = upm.payment_method` (withdrawals to user_payment_methods)

### 2. ajax/get-withdrawal.php
Fixed 1 JOIN condition:

**Line 31:**
- `w.method_code = pm.method_code` (withdrawals to payment_methods)

## Solution
Added explicit `COLLATE utf8mb4_unicode_ci` clauses to all string comparisons in JOIN conditions.

### Before:
```sql
LEFT JOIN withdrawals w ON t.reference = w.reference AND t.type = 'withdrawal'
LEFT JOIN user_payment_methods upm ON w.user_id = upm.user_id AND w.method_code = upm.payment_method
```

### After:
```sql
LEFT JOIN withdrawals w ON t.reference COLLATE utf8mb4_unicode_ci = w.reference COLLATE utf8mb4_unicode_ci AND t.type = 'withdrawal'
LEFT JOIN user_payment_methods upm ON w.user_id = upm.user_id AND w.method_code COLLATE utf8mb4_unicode_ci = upm.payment_method COLLATE utf8mb4_unicode_ci
```

## Why This Works
- The `COLLATE` clause explicitly sets the collation for the comparison
- `utf8mb4_unicode_ci` is a general-purpose, case-insensitive collation
- Both sides of the comparison use the same collation
- MySQL can now safely compare the strings

## Testing Checklist
- [ ] Transactions page loads without error 1267
- [ ] Withdrawal details load correctly
- [ ] Search functionality works on transactions page
- [ ] JOIN queries return correct data

## Prevention Tips
1. Use consistent collations across all tables and columns
2. Set database-level default collation during schema creation
3. Add COLLATE clauses when joining string columns from different tables
4. Test queries with mixed-collation data

## Related Commit
- Commit: 7b3711d
- Fix: Added COLLATE utf8mb4_unicode_ci to 6 JOIN conditions
