# Payment Method Dropdown Fix & withdrawal_pending Template

## Issues Resolved

### 1. Empty Payment Method Dropdown
**Problem:** The withdrawal modal's payment method dropdown was empty because the SQL query was referencing a non-existent column.

**Root Cause:** 
- Query tried to select `account_details` column which doesn't exist in `user_payment_methods` table
- JOIN condition used incorrect case (LOWER vs UPPER)
- Type check used wrong enum value ('bank' instead of 'fiat')

**Solution:**
Updated the query in `index.php` (lines 445-493) to use the correct database schema from `cryptofinanze (5).sql`:

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

**Key Changes:**
- **For Crypto:** Uses `wallet_address` and `cryptocurrency` columns
- **For Fiat/Bank:** Uses `iban`, `account_number`, and `bank_name` columns
- **Fixed JOIN:** Changed to UPPER() for proper method_code matching
- **Fixed Type:** Changed 'bank' to 'fiat' (matches DB enum)
- **Added Label:** Uses user's custom label if provided

**Display Logic:**
1. Priority order for display name:
   - User's custom `label` (if set)
   - `method_name` from payment_methods table
   - `cryptocurrency` name (for crypto)
   - `bank_name` (for fiat)
   
2. Masking for security:
   - Crypto addresses: Shows last 6 characters (...ABC123)
   - Bank accounts: Shows last 4 characters (...1234)

3. Data attributes populated:
   - `data-details`: Full wallet address or IBAN for auto-fill
   - `data-type`: 'crypto' or 'fiat' for conditional logic

### 2. Missing withdrawal_pending Email Template

**Problem:** No email template existed for withdrawal confirmation emails.

**Solution:** Created comprehensive `email_template_withdrawal_pending.sql` with professional German email design.

**Template Features:**

1. **Design:**
   - Beautiful gradient header (purple theme)
   - Responsive mobile layout
   - Professional styling with modern CSS
   - Clear visual hierarchy

2. **Content Structure:**
   - Personalized greeting: "Sehr geehrte/r {first_name} {last_name}"
   - Status confirmation message
   - Detailed withdrawal information box
   - Processing time alert (1-3 business days)
   - Call-to-action button to view transactions
   - Important notes section
   - Professional footer with company info

3. **Variables Included:**

   **Custom Variables (passed by code):**
   - `amount` - Withdrawal amount with currency
   - `reference` - Unique withdrawal reference
   - `payment_method` - Payment method name
   - `payment_details` - Bank account or crypto address
   - `transaction_id` - Transaction identifier
   - `transaction_date` - Timestamp of request
   - `status` - Current status

   **Auto-populated (by EmailHelper):**
   - `first_name`, `last_name` - User names
   - `user_email` - User email address
   - `brand_name` - Company/brand name
   - `site_url` - Website URL
   - `support_email` - Support email address
   - `contact_email` - Contact email
   - `company_address` - Company address
   - `current_year` - Current year for copyright

4. **Status Badge:**
   - Visual indicator showing "Ausstehend" (Pending)
   - Yellow/amber color scheme
   - Clearly visible in email

5. **Installation:**
   ```bash
   mysql -u username -p database_name < email_template_withdrawal_pending.sql
   ```

   The SQL uses `ON DUPLICATE KEY UPDATE` so it can be safely re-run to update the template.

## Database Schema Reference

From `cryptofinanze (5).sql`:

### user_payment_methods Table
```sql
CREATE TABLE `user_payment_methods` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `type` enum('fiat','crypto') NOT NULL DEFAULT 'fiat',
  `label` varchar(100) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `iban` varchar(34) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `wallet_address` varchar(255) DEFAULT NULL,
  `cryptocurrency` varchar(20) DEFAULT NULL,
  `verification_status` enum('pending','verifying','verified','failed') DEFAULT 'pending',
  ...
)
```

### payment_methods Table
```sql
CREATE TABLE `payment_methods` (
  `id` int NOT NULL,
  `method_code` varchar(50) NOT NULL,
  `method_name` varchar(100) NOT NULL,
  `allows_withdrawal` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  ...
)
```

## Testing Checklist

### Payment Method Dropdown:
- [ ] User has at least one verified payment method in database
- [ ] Dropdown shows method name with masked details
- [ ] Selecting method auto-fills payment details textarea
- [ ] Crypto methods show wallet address
- [ ] Fiat methods show IBAN or account number
- [ ] Custom labels are displayed if set

### Email Template:
- [ ] Template installed in database
- [ ] withdrawal_pending template_key exists in email_templates
- [ ] All variables are properly replaced
- [ ] Email displays correctly in email client
- [ ] Responsive design works on mobile
- [ ] Links (View Transactions, Support) work correctly

## Files Modified

1. **index.php** (lines 445-493)
   - Updated payment method query
   - Fixed column references
   - Improved display logic

2. **email_template_withdrawal_pending.sql** (new file)
   - Complete email template
   - 8.9KB professional German email
   - Ready for database installation

## Integration with Existing Code

The fixes integrate seamlessly with existing withdrawal flow:

1. **JavaScript Auto-fill** (line 2601-2616):
   - Already compatible
   - Uses `data-details` attribute
   - No changes needed

2. **EmailHelper Integration**:
   - Template uses EmailHelper variable format
   - All auto-populated variables work
   - Custom variables passed from process-withdrawal.php

3. **Process Flow**:
   ```
   User selects payment method
   ↓
   Dropdown query fetches verified methods
   ↓
   JavaScript auto-fills payment details
   ↓
   User submits withdrawal
   ↓
   EmailHelper sends withdrawal_pending email
   ↓
   User receives professional confirmation
   ```

## Validation

✅ **Query Validation:**
- All columns exist in cryptofinanze (5).sql schema
- JOIN conditions match table structure
- WHERE clause filters correctly

✅ **PHP Validation:**
- No syntax errors
- Proper prepared statements
- Exception handling in place

✅ **Email Template Validation:**
- All variables documented
- HTML structure valid
- Responsive CSS included
- Safe for ON DUPLICATE KEY UPDATE

## Commit Information

**Commit:** 324986f
**Files Changed:** 
- index.php (modified)
- email_template_withdrawal_pending.sql (created)

**Lines Changed:**
- index.php: +49 lines, better logic
- email_template_withdrawal_pending.sql: +294 lines

## Benefits

1. **User Experience:**
   - Dropdown now works correctly
   - Shows user's actual verified methods
   - Auto-fills correct details
   - Professional email confirmation

2. **Code Quality:**
   - Matches actual database schema
   - Proper error handling
   - Clean separation of concerns
   - Well-documented changes

3. **Maintainability:**
   - Uses database-driven templates
   - Easy to update email design
   - Consistent with EmailHelper pattern
   - Clear variable documentation

4. **Security:**
   - Masks sensitive details in dropdown
   - Only shows verified methods
   - Proper SQL parameterization
   - XSS protection with htmlspecialchars
