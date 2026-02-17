# 🚨 CRYPTO DROPDOWN NOT WORKING? START HERE! 🚨

## ⚡ INSTANT FIX (30 seconds)

**STEP 1:** Visit this page in your browser:
```
http://yoursite.com/debug_crypto_system.php
```

**STEP 2:** Look at the results:

### If you see "Table doesn't exist" ❌
```bash
mysql -u username -p database_name < admin/migrations/005_create_crypto_and_network_tables.sql
```
**Done!** Reload page and test.

### If you see "NO cryptocurrencies found" ❌
```
The migration didn't finish. Run the command above again.
```

### If you see "No ACTIVE cryptocurrencies" ❌
```sql
UPDATE cryptocurrencies SET is_active = 1;
UPDATE crypto_networks SET is_active = 1;
```
**Done!** Reload page and test.

### If you see "Unauthorized access" ❌
```
You're not logged in. Log in and try again.
```

### If ALL tests pass but dropdown still empty ✓
```
1. Press F12 (open browser console)
2. Look for error messages in red
3. Take screenshot
4. Check CRYPTO_DROPDOWN_FIX_GUIDE.md
```

---

## 🔍 WHAT TO CHECK (Browser Console)

**Press F12** → **Console Tab** → Look for these messages:

### Good Messages (Working):
```
✓ Loading cryptocurrencies...
✓ AJAX Response: {success: true...}
✓ Cryptocurrencies loaded: 10
```

### Bad Messages (Problem):
```
✗ Failed to load cryptocurrencies
✗ Unauthorized access
✗ Error: Table doesn't exist
```

**If you see bad messages:** They tell you exactly what's wrong!

---

## 📱 QUICK TESTS

### Test User Page:
1. Go to: `payment-methods.php`
2. Click: "Add Crypto Wallet"
3. Dropdown should have: BTC, ETH, USDT, etc.
4. Select BTC
5. Networks should show: Bitcoin

### Test Admin Page:
1. Go to: `admin/admin_crypto_management.php`
2. Should see: List of 10 cryptocurrencies
3. Each should show: Their networks
4. Click: "Add Cryptocurrency"
5. Modal should open

---

## 🆘 STILL NOT WORKING?

### Read This Guide:
```
CRYPTO_DROPDOWN_FIX_GUIDE.md
```

### Get Help With:
1. Screenshot of `debug_crypto_system.php`
2. Screenshot of browser console (F12)
3. What you tried
4. What error messages you see

---

## 💡 MOST COMMON ISSUES

**95% of problems are one of these:**

1. **Migration not run** → Run SQL file
2. **Data is inactive** → Run UPDATE SQL
3. **Not logged in** → Log in first
4. **Browser cache** → Hard reload (Ctrl+Shift+R)

---

## ✅ HOW TO KNOW IT'S FIXED

- [ ] `debug_crypto_system.php` shows all green ✓
- [ ] Console shows "Cryptocurrencies loaded: 10"
- [ ] Admin page displays cryptocurrencies
- [ ] User dropdown has options
- [ ] Networks populate when crypto selected
- [ ] Can add payment methods

**All checked?** → **WORKING!** 🎉

---

## 📚 MORE HELP

**Quick Start:** `QUICK_START_CRYPTO_FIX.md`
**Detailed Fix:** `CRYPTO_DROPDOWN_FIX_GUIDE.md`
**All Problems:** `TROUBLESHOOTING_CRYPTO_ISSUES.md`

---

**Created:** 2026-02-17
**Status:** Active Support Document
**Use:** First line troubleshooting

**REMEMBER:** Run `debug_crypto_system.php` FIRST! It tells you exactly what to do!
