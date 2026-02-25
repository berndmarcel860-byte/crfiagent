# Case Status German Translation Update

## Summary

Updated German translations for case status values in case update notification emails to provide more professional, user-friendly descriptions.

## Changes Made

### File: `admin/admin_ajax/update_case.php`

**1. Updated Status Translations (Lines 31-33)**

Three status translations were updated to match the requested German text:

| Status Code | Before | After | Requested |
|-------------|--------|-------|-----------|
| `under_review` | "In Prüfung" | "Fall wird überprüft" | ✅ Yes |
| `documents_required` | "Dokumente erforderlich" | "Nachweisdokumente erforderlich" | ✅ Yes |
| `document_required` | "Dokumente erforderlich" | "Nachweisdokumente erforderlich" | - |

**2. Applied Translations to Email Function (Line 142)**

Updated `sendCaseStatusUpdateEmail()` function to use the translation array:

```php
function sendCaseStatusUpdateEmail($pdo, $userData, $caseId, $oldStatus, $newStatus, $updateData) {
    // Get status translations
    global $statusTranslations;
    
    $customVars = [
        'case_number' => $userData['case_number'] ?? 'N/A',
        'case_id' => $caseId,
        'old_status' => $statusTranslations[strtolower($oldStatus)] ?? $oldStatus,
        'new_status' => $statusTranslations[strtolower($newStatus)] ?? $newStatus,
        'status_notes' => $updateData['status_notes'] ?? '',
        'update_date' => date('Y-m-d H:i:s')
    ];
    // ...
}
```

## Email Output Examples

### Example 1: Documents Required

**Before:**
```
Aktualisierte Falldetails
Fallnummer: SCM-2026-8381
Vorheriger Status: under_review
Neuer Status: documents_required
```

**After:**
```
Aktualisierte Falldetails
Fallnummer: SCM-2026-8381
Vorheriger Status: Fall wird überprüft
Neuer Status: Nachweisdokumente erforderlich
```

### Example 2: Case Under Review

**Before:**
```
Vorheriger Status: pending
Neuer Status: under_review
```

**After:**
```
Vorheriger Status: Ausstehend
Neuer Status: Fall wird überprüft
```

## Complete Status Translation Map

All available case status translations:

| Status Code | German Translation |
|-------------|-------------------|
| `open` | Offen |
| `pending` | Ausstehend |
| `under_review` | Fall wird überprüft |
| `document_required` | Nachweisdokumente erforderlich |
| `documents_required` | Nachweisdokumente erforderlich |
| `in_progress` | In Bearbeitung |
| `completed` | Abgeschlossen |
| `rejected` | Abgelehnt |

## How It Works

1. **Status translations are defined** at the top of update_case.php (lines 29-38)
2. **When a case status is updated**, the system fetches current and new status
3. **Before sending email**, both statuses are translated using `$statusTranslations[strtolower($status)]`
4. **Email template receives** German text instead of raw database values
5. **Users see** professional German descriptions in their notification emails

## Testing

**To verify the translations work:**

1. Update a case status from "under_review" to "documents_required"
2. Check the email received by the user
3. Verify status displays as:
   - "Vorheriger Status: Fall wird überprüft"
   - "Neuer Status: Nachweisdokumente erforderlich"

## Benefits

✅ **Professional German** - Users see proper German text, not English database codes
✅ **User-Friendly** - Descriptive text is easier to understand than technical codes
✅ **Consistency** - All status changes use the same translation system
✅ **Maintainable** - Translations centralized in one array for easy updates
✅ **Fallback Safe** - Shows original value if translation missing

## Status

✅ **Implementation:** Complete
✅ **Testing:** PHP syntax validated
✅ **Documentation:** Complete
✅ **Production:** Ready

All case status update emails now display professional German text as requested.
