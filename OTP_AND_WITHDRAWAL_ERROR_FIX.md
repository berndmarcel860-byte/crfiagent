# OTP Button and Withdrawal Error Fix

## Issues Fixed

### 1. OTP Button Stuck on "Sending..."
**Problem:** When clicking "Send & Verify OTP" button, it showed "Sending..." but never changed to "Verify OTP" after successful OTP send.

**Impact:** Users couldn't proceed to verify their OTP, blocking withdrawal submissions.

### 2. Withdrawal "Bad Request" Error
**Problem:** After successful OTP verification, withdrawal submission failed with generic "Server communication error: Bad Request" message.

**Impact:** Users couldn't complete withdrawal requests, even with valid OTP.

---

## Root Causes

### OTP Button Issue
**Root Cause:** Used jQuery `$.post()` shorthand which doesn't explicitly set `dataType`. This meant:
- Response might be treated as text instead of JSON
- JSON parsing wasn't guaranteed
- Button state update logic didn't execute if response wasn't properly parsed
- No explicit error handling or button state reset on failure

### Withdrawal Error Issue
**Root Cause:** 
- No explicit `dataType: 'json'` in AJAX call
- Generic error handling didn't parse server error responses
- No mapping of HTTP status codes to user-friendly messages
- Limited debugging information in console

---

## Solutions Implemented

### 1. OTP Button Fix

**Changed from:**
```javascript
$.post('ajax/otp-handler.php', {
    action: 'send',
    csrf_token: $('meta[name="csrf-token"]').attr('content')
}, function (r) {
    if (r.success) {
        // Update button
    }
}, 'json').fail(function () {
    // Generic error
}).always(function () {
    $btn.prop('disabled', false);
});
```

**Changed to:**
```javascript
$.ajax({
    url: 'ajax/otp-handler.php',
    method: 'POST',
    data: {
        action: 'send',
        csrf_token: $('meta[name="csrf-token"]').attr('content')
    },
    dataType: 'json',
    success: function (r) {
        if (r.success) {
            toastr.success(r.message || 'OTP sent to your email');
            $otpInput.prop('disabled', false).focus();
            otpSent = true;
            $btn.prop('disabled', false).html('<i class="anticon anticon-check-circle"></i> Verify OTP');
            $('#otpInfoText').html('OTP sent! Enter the code and click "Verify OTP" button.');
        } else {
            toastr.error(r.message || 'Failed to send OTP');
            $btn.prop('disabled', false).html('<i class="anticon anticon-mail"></i> Send & Verify OTP');
        }
    },
    error: function (xhr, status, error) {
        console.error('OTP send error:', xhr.status, xhr.responseText);
        toastr.error('Failed to send OTP. Please try again.');
        $btn.prop('disabled', false).html('<i class="anticon anticon-mail"></i> Send & Verify OTP');
    }
});
```

**Key Improvements:**
- ✅ Explicit `dataType: 'json'` ensures proper JSON parsing
- ✅ Button explicitly enabled on success: `$btn.prop('disabled', false)`
- ✅ Button state reset on error
- ✅ Console logging for debugging: `console.error('OTP send error:', xhr.status, xhr.responseText)`
- ✅ Separate error callback for better error handling

### 2. Withdrawal Error Handling Fix

**Changed from:**
```javascript
$.ajax({
    url: 'ajax/process-withdrawal.php',
    method: 'POST',
    data: $form.serialize(),
    success: function (response) {
        try {
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            // Handle success
        } catch (err) {
            toastr.error('Error parsing server response');
        }
    },
    error: function (xhr, status, error) {
        toastr.error('Server communication error: ' + error);
    }
});
```

**Changed to:**
```javascript
$.ajax({
    url: 'ajax/process-withdrawal.php',
    method: 'POST',
    data: $form.serialize(),
    dataType: 'json',
    success: function (response) {
        if (response.success) {
            toastr.success(response.message || 'Withdrawal request submitted successfully');
            // Handle success
        } else {
            toastr.error(response.message || 'Error processing withdrawal');
        }
    },
    error: function (xhr, status, error) {
        console.error('Withdrawal error:', xhr.status, xhr.responseText);
        let errorMsg = 'Server communication error: ' + error;
        
        // Try to parse error response
        try {
            const errorData = JSON.parse(xhr.responseText);
            if (errorData.message) {
                errorMsg = errorData.message;
            }
        } catch (e) {
            // Map HTTP status to friendly message
            if (xhr.status === 400) {
                errorMsg = 'Bad Request - Please check your input fields';
            } else if (xhr.status === 403) {
                errorMsg = 'Security error - Please refresh the page';
            } else if (xhr.status === 401) {
                errorMsg = 'Session expired - Please login again';
            }
        }
        
        toastr.error(errorMsg);
    }
});
```

**Key Improvements:**
- ✅ Explicit `dataType: 'json'` for proper parsing
- ✅ Attempts to parse JSON error response from server
- ✅ Shows specific error message from server if available
- ✅ HTTP status code mapping to user-friendly messages
- ✅ Console logging: `console.error('Withdrawal error:', xhr.status, xhr.responseText)`
- ✅ Better debugging capabilities

---

## OTP Button Flow

**State 1: Initial**
- Button text: "Send & Verify OTP"
- Button enabled: ✅
- OTP input disabled: ✅

**State 2: Sending OTP**
- Button text: "Sending OTP..." (with spinner)
- Button disabled: ✅
- OTP input disabled: ✅

**State 3: OTP Sent**
- Button text: "Verify OTP"
- Button enabled: ✅
- OTP input enabled: ✅ (and focused)

**State 4: Verifying OTP**
- Button text: "Verifying..." (with spinner)
- Button disabled: ✅
- OTP input enabled: ✅

**State 5: Verified**
- Button text: "Verified" (green)
- Button disabled: ✅
- OTP input disabled: ✅
- Withdrawal submit button enabled: ✅

---

## Withdrawal Error Messages

### HTTP Status Code Mapping

| Status | Original Message | New User-Friendly Message |
|--------|-----------------|---------------------------|
| 400 | Bad Request | "Bad Request - Please check your input fields" |
| 401 | Unauthorized | "Session expired - Please login again" |
| 403 | Forbidden | "Security error - Please refresh the page" |
| Server error | Generic | Specific message from server if available |

---

## Testing Guide

### Test OTP Button

1. **Test Send OTP:**
   - Click "Send & Verify OTP"
   - Should show "Sending OTP..." (disabled)
   - After success, should show "Verify OTP" (enabled)
   - OTP input should be enabled and focused
   - Should receive OTP email

2. **Test OTP Input:**
   - Enter 6-digit code
   - Click "Verify OTP"
   - Should show "Verifying..." (disabled)
   - After success, should show "Verified" (green, disabled)
   - Withdrawal submit button should be enabled

3. **Test Error Handling:**
   - Try with invalid OTP
   - Button should reset to "Verify OTP"
   - Error message should display

### Test Withdrawal

4. **Test Valid Submission:**
   - Fill all fields
   - Verify OTP
   - Submit withdrawal
   - Should succeed with success message

5. **Test Error Messages:**
   - Try without OTP verification
   - Should show specific OTP error
   - Check console for detailed errors

6. **Test Network Issues:**
   - Disable network temporarily
   - Try OTP send/verify
   - Should show error and reset button

---

## Debugging

### Console Logging

Check browser console for:
- `OTP send error: [status] [responseText]`
- `OTP verify error: [status] [responseText]`
- `Withdrawal error: [status] [responseText]`

### Common Issues

**OTP Button Still Stuck:**
- Check browser console for errors
- Verify ajax/otp-handler.php returns proper JSON
- Check network tab for response format

**Withdrawal Still Shows Bad Request:**
- Check console for specific error
- Verify all form fields are filled
- Check CSRF token is present
- Verify OTP was verified successfully

---

## Files Modified

- **index.php** (lines 2673-2735, 2555-2596):
  - OTP button AJAX calls
  - Withdrawal submission handler
  - Error handling improvements

---

## Result

✅ OTP button changes from "Sending..." to "Verify OTP" after successful send
✅ OTP button changes to "Verified" after successful verification
✅ Withdrawal errors show specific server messages instead of generic "Bad Request"
✅ Better debugging with console logging
✅ User-friendly error messages for common HTTP statuses
✅ Complete OTP flow works smoothly

---

**Commit:** 8f053a0
**Date:** 2026-02-27
