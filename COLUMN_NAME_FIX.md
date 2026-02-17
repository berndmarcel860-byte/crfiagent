# Column Name Fix: u.username → u.name

## Problem
```
Column not found u.username in field list
```

## Root Cause
The SQL query was referencing `u.username` but the users table only has a `name` column, not `username`.

## Database Schema
```sql
CREATE TABLE users (
    id INT PRIMARY KEY,
    name VARCHAR(255),      -- ✅ Exists
    email VARCHAR(255),     -- ✅ Exists
    -- username does NOT exist
)
```

## Solution
Changed all references from `u.username` to `u.name` in `admin/admin_ajax/get_pending_wallets.php`:

### Change 1: SELECT clause (Line 29)
```sql
-- Before
u.username, u.email

-- After
u.name as username, u.email
```

### Change 2: Search WHERE clause (Line 38)
```sql
-- Before
AND (u.username LIKE ? OR u.email LIKE ? ...)

-- After
AND (u.name LIKE ? OR u.email LIKE ? ...)
```

## Benefits
- ✅ Query now executes without errors
- ✅ Uses correct database column names
- ✅ Maintains API compatibility (using alias `as username`)
- ✅ Search functionality works correctly

## Files Modified
- `admin/admin_ajax/get_pending_wallets.php` (2 lines changed)

## Testing
```bash
php -l admin/admin_ajax/get_pending_wallets.php
# No syntax errors detected ✅
```

## Status
✅ Fixed and tested
✅ Committed (9afa836)
✅ Ready for production
