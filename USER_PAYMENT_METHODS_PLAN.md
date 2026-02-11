# User Payment Methods Management - Implementation Plan

## Overview
Allow users to add, manage, and use multiple payment methods for both fiat currency and cryptocurrency transactions.

## Database Schema Enhancement

### Enhanced `user_payment_methods` Table

```sql
ALTER TABLE user_payment_methods
ADD COLUMN type ENUM('fiat', 'crypto') NOT NULL DEFAULT 'fiat' AFTER payment_method,
ADD COLUMN account_holder VARCHAR(255) NULL AFTER type,
ADD COLUMN bank_name VARCHAR(255) NULL AFTER account_holder,
ADD COLUMN iban VARCHAR(34) NULL AFTER bank_name,
ADD COLUMN bic VARCHAR(11) NULL AFTER iban,
ADD COLUMN wallet_address VARCHAR(255) NULL AFTER bic,
ADD COLUMN cryptocurrency VARCHAR(20) NULL AFTER wallet_address,
ADD COLUMN network VARCHAR(50) NULL AFTER cryptocurrency,
ADD COLUMN is_verified TINYINT(1) DEFAULT 0 AFTER is_default,
ADD COLUMN last_used_at TIMESTAMP NULL AFTER is_verified,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;
```

## Features

### 1. Fiat Payment Methods
- **Bank Transfer (SEPA)**
  - Account Holder Name
  - Bank Name
  - IBAN (validated)
  - BIC/SWIFT
- **Credit/Debit Card**
  - Card holder name
  - Last 4 digits (stored)
  - Expiry month/year
- **PayPal**
  - PayPal email
- **Other Services**
  - Service name
  - Account identifier

### 2. Crypto Wallets
Supported cryptocurrencies:
- **Bitcoin (BTC)** - Bitcoin Network
- **Ethereum (ETH)** - Ethereum Network
- **USDT** - ERC-20, TRC-20, BEP-20
- **USDC** - ERC-20, BEP-20
- **BNB** - Binance Smart Chain
- **XRP** - Ripple Network
- **ADA** - Cardano Network
- **SOL** - Solana Network
- **DOT** - Polkadot Network
- **DOGE** - Dogecoin Network

## User Interface

### Payment Methods Page (`payment-methods.php`)

```
┌─────────────────────────────────────────────────┐
│ Payment Methods Management                      │
├─────────────────────────────────────────────────┤
│                                                 │
│ 🏦 Fiat Payment Methods                        │
│ ┌─────────────────────────────────────────┐    │
│ │ [+] Add Bank Account                     │    │
│ │ [+] Add Credit Card                      │    │
│ │ [+] Add PayPal                           │    │
│ └─────────────────────────────────────────┘    │
│                                                 │
│ 💰 Cryptocurrency Wallets                      │
│ ┌─────────────────────────────────────────┐    │
│ │ [+] Add Crypto Wallet                    │    │
│ └─────────────────────────────────────────┘    │
│                                                 │
│ Saved Payment Methods:                          │
│ ┌─────────────────────────────────────────┐    │
│ │ 🏦 Bank Transfer (SEPA)           ⭐    │    │
│ │ Deutsche Bank                            │    │
│ │ DE89 3704 0044 0532 0130 00             │    │
│ │ [Edit] [Delete] [Set as Default]        │    │
│ └─────────────────────────────────────────┘    │
│                                                 │
│ ┌─────────────────────────────────────────┐    │
│ │ 💰 Bitcoin Wallet                       │    │
│ │ BTC - Bitcoin Network                    │    │
│ │ bc1qxy2kgdygjrsqtzq2n0yrf2493p...      │    │
│ │ [Edit] [Delete] [Set as Default]        │    │
│ └─────────────────────────────────────────┘    │
└─────────────────────────────────────────────────┘
```

## Backend Endpoints

### 1. Add Payment Method
**File**: `ajax/add_payment_method.php`

**Request**:
```json
{
  "type": "fiat|crypto",
  "payment_method": "Bank Transfer|Bitcoin|Ethereum|...",
  // For fiat:
  "account_holder": "John Doe",
  "bank_name": "Deutsche Bank",
  "iban": "DE89370400440532013000",
  "bic": "COBADEFFXXX",
  // For crypto:
  "wallet_address": "bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh",
  "cryptocurrency": "BTC",
  "network": "Bitcoin",
  "is_default": true
}
```

**Response**:
```json
{
  "success": true,
  "message": "Payment method added successfully",
  "payment_method_id": 123
}
```

### 2. Get Payment Methods
**File**: `ajax/get_payment_methods.php`

**Response**:
```json
{
  "success": true,
  "fiat_methods": [
    {
      "id": 1,
      "type": "fiat",
      "payment_method": "Bank Transfer",
      "account_holder": "John Doe",
      "bank_name": "Deutsche Bank",
      "iban": "DE89370400440532013000",
      "bic": "COBADEFFXXX",
      "is_default": true,
      "is_verified": true,
      "created_at": "2026-01-15 10:30:00"
    }
  ],
  "crypto_methods": [
    {
      "id": 2,
      "type": "crypto",
      "payment_method": "Bitcoin",
      "wallet_address": "bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh",
      "cryptocurrency": "BTC",
      "network": "Bitcoin",
      "is_default": false,
      "is_verified": false,
      "created_at": "2026-01-16 14:20:00"
    }
  ]
}
```

### 3. Update Payment Method
**File**: `ajax/update_payment_method.php`

### 4. Delete Payment Method
**File**: `ajax/delete_payment_method.php`

### 5. Set Default Payment Method
**File**: `ajax/set_default_payment_method.php`

## Validation Rules

### IBAN Validation
- Length: 15-34 characters
- Format: Country code (2 letters) + check digits (2 numbers) + account number
- Checksum validation algorithm

### Crypto Address Validation
- **Bitcoin**: Starts with 1, 3, or bc1, 26-35 characters
- **Ethereum**: Starts with 0x, 42 characters hex
- **USDT (TRC-20)**: Starts with T, 34 characters
- **BNB**: Starts with bnb, 42 characters

## Security Considerations

1. **Input Validation**
   - Sanitize all inputs
   - Validate format for IBAN, BIC, wallet addresses
   - Check for SQL injection attempts

2. **Ownership Verification**
   - Only allow users to manage their own payment methods
   - Check user_id matches session user_id

3. **Sensitive Data**
   - Never log full payment details
   - Mask IBAN (show only last 4 digits in logs)
   - Mask wallet addresses (show first 6 and last 4)

4. **Verification Process**
   - Optional: Send small test transaction for verification
   - Mark as verified after successful transaction

## UI Components

### Add Fiat Payment Method Modal
```html
<div class="modal">
  <h3>Add Bank Account</h3>
  <form>
    <input name="account_holder" placeholder="Account Holder Name" required>
    <input name="bank_name" placeholder="Bank Name" required>
    <input name="iban" placeholder="IBAN" required>
    <input name="bic" placeholder="BIC/SWIFT" required>
    <label>
      <input type="checkbox" name="is_default"> Set as default
    </label>
    <button>Add Payment Method</button>
  </form>
</div>
```

### Add Crypto Wallet Modal
```html
<div class="modal">
  <h3>Add Crypto Wallet</h3>
  <form>
    <select name="cryptocurrency" required>
      <option value="BTC">Bitcoin (BTC)</option>
      <option value="ETH">Ethereum (ETH)</option>
      <option value="USDT">Tether (USDT)</option>
      ...
    </select>
    <select name="network" required>
      <option value="Bitcoin">Bitcoin Network</option>
      <option value="Ethereum">Ethereum (ERC-20)</option>
      <option value="Tron">Tron (TRC-20)</option>
      <option value="BSC">Binance Smart Chain (BEP-20)</option>
    </select>
    <input name="wallet_address" placeholder="Wallet Address" required>
    <label>
      <input type="checkbox" name="is_default"> Set as default
    </label>
    <button>Add Wallet</button>
  </form>
</div>
```

## Implementation Timeline

### Week 1: Database & Backend
- **Day 1-2**: Database migration and testing (5 hours)
- **Day 3-5**: Backend API endpoints (8 hours)
  - Add payment method
  - Get payment methods
  - Update payment method
  - Delete payment method
  - Set default method

### Week 2: Frontend & Testing
- **Day 1-3**: Frontend UI development (10 hours)
  - Payment methods page redesign
  - Add fiat method modal
  - Add crypto wallet modal
  - Payment methods list
  - Edit/Delete actions
- **Day 4**: Testing & bug fixes (5 hours)
- **Day 5**: Documentation & deployment (2 hours)

**Total Estimate**: 30 hours

## Testing Checklist

- [ ] Add bank account (valid IBAN)
- [ ] Add bank account (invalid IBAN) - should fail
- [ ] Add crypto wallet (valid address)
- [ ] Add crypto wallet (invalid address) - should fail
- [ ] Set default payment method
- [ ] Edit payment method
- [ ] Delete payment method
- [ ] View all payment methods
- [ ] Use payment method for withdrawal
- [ ] Use payment method for deposit
- [ ] Security: Try to access another user's payment methods
- [ ] Security: Try SQL injection in form fields
- [ ] Security: Try XSS in form fields

## Success Metrics

- Users can add multiple payment methods
- Withdrawal process uses saved methods
- Deposit process uses saved methods
- 90%+ validation accuracy (IBAN, crypto addresses)
- Zero security vulnerabilities
- < 2 second page load time
- Mobile responsive design

## Future Enhancements

1. **Verification System**
   - Send micro-transaction for bank verification
   - Request signed message for crypto wallet verification

2. **Payment Method Analytics**
   - Track usage frequency
   - Recommend most used method

3. **Quick Actions**
   - One-click withdrawal to default method
   - Quick switch between methods

4. **Multi-Currency Support**
   - Different currencies for different methods
   - Auto-conversion rates

## Conclusion

This implementation provides users with a professional payment methods management system supporting both fiat and cryptocurrency. The modular design allows for easy expansion and maintenance.

**Status**: Ready for Implementation 🚀
