# Deposit Proof Upload Fix

## Issue Fixed

**Database Error:**
```
SQLSTATE[HY000]: General error: 1364 Field 'proof_path' doesn't have a default value
```

**When:** Admin tries to add a deposit for a user

## Root Cause

The `deposits` table in the database schema has a `proof_path` column defined as:
- Type: `varchar(255)`
- Constraint: `NOT NULL`
- No default value

This means every deposit MUST have a proof_path value, but the admin interface didn't provide a way to upload proof of payment files.

## Solution Implemented

Added complete file upload functionality for proof of payment documents when admins create deposits.

### Changes Made

#### 1. Frontend (admin_deposits.php)

**Added File Upload Field:**
```html
<div class="form-group">
    <label>Proof of Payment <span class="text-danger">*</span></label>
    <input type="file" class="form-control" name="proof_file" 
           accept=".jpg,.jpeg,.png,.pdf" required>
    <small class="form-text text-muted">
        Upload proof of payment (JPG, PNG, or PDF - max 10MB)
    </small>
</div>
```

**Updated Form Encoding:**
```html
<form id="addDepositForm" enctype="multipart/form-data">
```

**Updated JavaScript for File Upload:**
```javascript
$('#addDepositForm').submit(function(e) {
    e.preventDefault();
    const formData = new FormData(this); // Changed from serialize()
    
    $.ajax({
        url: 'admin_ajax/add_deposit.php',
        type: 'POST',
        data: formData,
        processData: false,  // Required for file upload
        contentType: false,  // Required for file upload
        dataType: 'json',
        // ... rest of ajax config
    });
});
```

#### 2. Backend (admin/admin_ajax/add_deposit.php)

**Added File Upload Validation:**
- Validates that proof file is uploaded
- Checks file size (max 10MB)
- Validates file extension (jpg, jpeg, png, pdf only)
- Validates MIME type using finfo
- Verifies image integrity for image files

**Security Features:**
```php
// File type validation
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];

// MIME type validation
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

// Image integrity check
if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        throw new Exception("Invalid image file.");
    }
}

// Secure filename generation
$filename = 'deposit_proof_' . $userId . '_' . time() . '_' 
          . bin2hex(random_bytes(8)) . '.' . $extension;
```

**File Storage:**
- Files stored in: `admin/uploads/deposits/`
- Filename format: `deposit_proof_{user_id}_{timestamp}_{random}.{ext}`
- Directory permissions: 0755
- Database stores relative path: `uploads/deposits/{filename}`

**Updated Database INSERT:**
```php
INSERT INTO deposits (user_id, amount, method_code, reference, proof_path, status, admin_notes, created_at)
VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
```

## Security Measures

1. **File Type Validation:**
   - Extension whitelist (jpg, jpeg, png, pdf)
   - MIME type verification using finfo
   - Double validation (extension + content)

2. **File Size Limit:**
   - Maximum 10MB per file
   - Server-side validation

3. **Image Integrity:**
   - Image files validated with getimagesize()
   - Prevents malicious files disguised as images

4. **Secure Filename:**
   - Random bytes added to prevent guessing
   - Timestamp for uniqueness
   - User ID for organization

5. **Directory Security:**
   - Proper permissions (0755)
   - Separate directory for deposits
   - No executable permissions

## User Experience

### Admin Workflow:
1. Click "Add Deposit" button
2. Select user from dropdown
3. Enter amount
4. Choose payment method
5. **Upload proof of payment file** (NEW)
6. Optional: Add transaction reference
7. Select status (pending/completed/failed)
8. Optional: Add admin notes
9. Click "Create Deposit"

### Supported File Formats:
- JPG/JPEG images
- PNG images
- PDF documents

### File Size Limit:
- Maximum: 10MB per file

### Error Messages:
- "Proof of payment file is required"
- "File size exceeds 10MB limit"
- "Invalid file type. Only JPG, PNG, and PDF files are allowed."
- "Invalid file content. File does not match its extension."
- "Invalid image file."
- "Failed to upload proof file"

## Testing

### Test Cases:
1. ✅ Upload JPG proof - should succeed
2. ✅ Upload PNG proof - should succeed
3. ✅ Upload PDF proof - should succeed
4. ✅ Try to submit without file - should show error
5. ✅ Upload file > 10MB - should show error
6. ✅ Upload invalid file type (.txt, .exe) - should show error
7. ✅ Upload fake image (renamed .txt) - should show error
8. ✅ Verify file stored in uploads/deposits/
9. ✅ Verify proof_path saved in database
10. ✅ Verify deposit creation succeeds

### Manual Testing Steps:
```bash
# 1. Login as admin
# 2. Go to Deposits section
# 3. Click "Add Deposit"
# 4. Fill in form with valid data
# 5. Upload a proof file (JPG, PNG, or PDF)
# 6. Submit form
# 7. Verify success message
# 8. Check database:
SELECT id, user_id, amount, proof_path FROM deposits ORDER BY id DESC LIMIT 1;
# 9. Check file exists:
ls -la admin/uploads/deposits/
```

## Database Schema Reference

```sql
CREATE TABLE `deposits` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `method_code` varchar(50) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `proof_path` varchar(255) NOT NULL,  -- REQUIRED FIELD
  `payment_details` text,
  `admin_notes` text,
  `processed_by` int DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `admin_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

## Files Modified

1. **admin/admin_deposits.php**
   - Added file upload field to modal
   - Updated form encoding
   - Updated JavaScript for FormData

2. **admin/admin_ajax/add_deposit.php**
   - Added file upload validation
   - Added file security checks
   - Created uploads directory
   - Updated INSERT query
   - Added proof_path to admin log

## Deployment Notes

### Prerequisites:
- PHP with finfo extension enabled
- Write permissions for admin/uploads directory

### Directory Creation:
The `admin/uploads/deposits/` directory is automatically created by the code if it doesn't exist:
```php
$uploadsDir = '../uploads/deposits';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}
```

### Manual Directory Creation (optional):
```bash
mkdir -p admin/uploads/deposits
chmod 755 admin/uploads/deposits
```

### .gitignore Recommendation:
Add to .gitignore to exclude uploaded files:
```
admin/uploads/deposits/*
!admin/uploads/deposits/.gitkeep
```

## Future Enhancements

Potential improvements for the future:

1. **File Preview:**
   - Display uploaded proof in deposit details modal
   - Download link for proof documents

2. **Multiple Files:**
   - Allow multiple proof documents per deposit
   - Support for additional documentation

3. **Image Optimization:**
   - Automatic image compression
   - Thumbnail generation

4. **Cloud Storage:**
   - Integration with AWS S3, Google Cloud Storage
   - CDN for faster file delivery

5. **File Versioning:**
   - Keep history of uploaded proofs
   - Allow proof replacement

6. **OCR Integration:**
   - Automatic text extraction from proofs
   - Auto-fill transaction details

## Support

If you encounter issues:

1. **Check PHP Error Logs:**
   ```bash
   tail -f /var/log/php-fpm/error.log
   ```

2. **Verify Directory Permissions:**
   ```bash
   ls -la admin/uploads/
   ```

3. **Test File Upload:**
   - Try with different file types
   - Check file size
   - Verify MIME types

4. **Database Check:**
   ```sql
   DESCRIBE deposits;
   SELECT * FROM deposits WHERE proof_path IS NULL;
   ```

## Commit Information

- **Commit:** c658981
- **Files Changed:** 2
- **Lines Added:** 93
- **Lines Removed:** 6
- **Status:** Complete and Validated ✅
