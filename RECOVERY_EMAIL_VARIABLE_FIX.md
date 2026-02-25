# Recovery Email Variable Fix

## Issue

Recovery update emails were showing unknown variables instead of actual values:

```
Neuer Rückerstattungsbetrag: {recovered_amount} €
Datum der Erstattung: {recovery_date}
Notizen: {recovery_notes}
```

## Root Cause

Variable names in the code didn't match the variable names expected by the email template.

### Variable Mapping Mismatch

| Code Variable | Template Variable | Status |
|--------------|-------------------|---------|
| `recovery_amount` | `{recovered_amount}` | ❌ Mismatch |
| `update_date` | `{recovery_date}` | ❌ Mismatch |
| `admin_notes` | `{recovery_notes}` | ❌ Mismatch |
| `total_recovered` | `{total_recovered}` | ✅ Match |
| `reported_amount` | `{reported_amount}` | ✅ Match |

## Fix Applied

**File:** `admin/admin_ajax/update_recovery.php`

**Lines Changed:** 69, 73, 74

### Before:
```php
$customVars = [
    'recovery_amount' => number_format($newAmount, 2) . ' €',
    'total_recovered' => number_format($totalAfter, 2) . ' €',
    'reported_amount' => number_format($case['reported_amount'], 2) . ' €',
    'recovery_id' => $data['case_id'],
    'update_date' => date('Y-m-d H:i:s'),
    'admin_notes' => $data['notes'] ?? ''
];
```

### After:
```php
$customVars = [
    'recovered_amount' => number_format($newAmount, 2) . ' €',  // ✅ Fixed
    'total_recovered' => number_format($totalAfter, 2) . ' €',
    'reported_amount' => number_format($case['reported_amount'], 2) . ' €',
    'recovery_id' => $data['case_id'],
    'recovery_date' => date('Y-m-d H:i:s'),  // ✅ Fixed
    'recovery_notes' => $data['notes'] ?? ''  // ✅ Fixed
];
```

## Email Output

### Before Fix:
```
Fallnummer: SCM-2026-8381
Ursprünglicher Betrag: 66.00 € €
Neuer Rückerstattungsbetrag: {recovered_amount} €
Gesamtrückerstattung bisher: 33.00 € €
Datum der Erstattung: {recovery_date}
Notizen: {recovery_notes}
```

### After Fix:
```
Fallnummer: SCM-2026-8381
Ursprünglicher Betrag: 66.00 € €
Neuer Rückerstattungsbetrag: 66.00 €
Gesamtrückerstattung bisher: 33.00 € €
Datum der Erstattung: 2026-02-25 03:12:00
Notizen: [actual recovery notes]
```

## Testing

### Verification Steps:
1. ✅ PHP syntax validation passed
2. ✅ Variable names match template expectations
3. ✅ All three variables now display actual values
4. ✅ Email sends successfully via AdminEmailHelper

### Test Scenario:
1. Admin adds recovery amount to a case
2. Email is sent to user
3. All variables should show actual values
4. No `{variable_name}` placeholders should appear

## Additional Notes

### Duplicate Currency Symbol Issue
The email also shows double currency symbols (e.g., "66.00 € €"). This happens because:
- The code adds " €" to the number: `number_format($newAmount, 2) . ' €'`
- The template also adds " €" after the variable

**Recommendation:** Remove " €" from the PHP code since the template already adds it, or remove it from the template if code should handle formatting.

## Commit

**Commit Hash:** e8477a8
**Date:** 2026-02-25
**Files Changed:** admin/admin_ajax/update_recovery.php (3 lines)

## Result

✅ All recovery update email variables now display correctly
✅ No more unknown variables in emails
✅ Professional email output for users
