# Pre-Deployment Testing Checklist - قائمة الاختبار قبل النشر

## 🎯 Complete Functionality Test

**Date**: October 16, 2025  
**Environment**: Local → Hostinger Production  
**Status**: Pre-Deployment Testing

---

## 1. Authentication & Authorization Testing 🔐

### Registration
- [ ] Visit `/register`
- [ ] Fill all required fields (name, email, password, phone)
- [ ] Select role (Farmer/Carrier/Mill/Packer)
- [ ] Submit form
- [ ] ✅ Verify account created
- [ ] ✅ Verify email validation works
- [ ] ✅ Verify redirect to dashboard
- [ ] ✅ Test with Arabic/French/English

### Login
- [ ] Visit `/login`
- [ ] Enter valid credentials
- [ ] ✅ Verify successful login
- [ ] ✅ Verify redirect to dashboard
- [ ] Test "Remember Me" checkbox
- [ ] Test invalid credentials (should show error)
- [ ] Test rate limiting (5 attempts max)

### Logout
- [ ] Click logout button
- [ ] ✅ Verify session cleared
- [ ] ✅ Verify redirect to home
- [ ] Try accessing `/dashboard` after logout (should redirect)

### Password Reset
- [ ] Click "Forgot Password"
- [ ] Enter email
- [ ] ✅ Check if reset email sent (check logs if mail not configured)
- [ ] Test reset link
- [ ] Set new password
- [ ] Login with new password

---

## 2. Profile Management Testing 👤

### View Profile/Dashboard
- [ ] Login and go to `/dashboard`
- [ ] ✅ Profile card displays at top
- [ ] ✅ Profile picture shows (or default avatar)
- [ ] ✅ Cover photo slideshow works
- [ ] ✅ Auto-rotation every 4 seconds
- [ ] ✅ Navigation arrows work
- [ ] ✅ Dot indicators work
- [ ] ✅ Profile completion percentage shows
- [ ] ✅ Statistics show (active/pending listings)

### Edit Profile - Basic Information
- [ ] Go to `/profile`
- [ ] ✅ Form displays with modern design
- [ ] Update name
- [ ] Update phone
- [ ] ✅ Save changes
- [ ] ✅ Success message appears
- [ ] ✅ Changes persist after refresh

### Edit Profile - Profile Picture
- [ ] Click profile picture upload area
- [ ] Select image (JPEG/PNG, < 5MB)
- [ ] ✅ Preview shows
- [ ] ✅ Upload successful
- [ ] ✅ Image displays on dashboard
- [ ] Test with large file (> 5MB, should reject)
- [ ] Test with invalid format (should reject)

### Edit Profile - Cover Photos
- [ ] Click cover photos upload area
- [ ] Select multiple images (up to 5)
- [ ] ✅ All images upload
- [ ] ✅ Thumbnails show with numbers (#1, #2, etc.)
- [ ] ✅ Delete button works on each photo
- [ ] ✅ Slideshow updates on dashboard
- [ ] Test uploading 6th photo (should reject or replace)
- [ ] Test on iPhone (HEIC should convert to JPEG)

### Edit Profile - Role-Specific Fields

**For Farmer:**
- [ ] Farm location field visible
- [ ] Tree number field visible
- [ ] Olive type dropdown visible
- [ ] ✅ Save farmer details
- [ ] ✅ Data persists

**For Carrier:**
- [ ] Truck capacity field visible
- [ ] ✅ Save carrier details
- [ ] ✅ Data persists

**For Mill:**
- [ ] Mill name field visible
- [ ] ✅ Save mill details
- [ ] ✅ Data persists

**For Packer:**
- [ ] Company name field visible
- [ ] ✅ Save packer details
- [ ] ✅ Data persists

---

## 3. Listing Management Testing 📦

### Create Listing
- [ ] Go to create listing page
- [ ] Fill product details:
  - [ ] Product type (Olive/Oil)
  - [ ] Variety (Chetoui/Chemlali/etc.)
  - [ ] Quantity
  - [ ] Unit (kg/liter/ton)
  - [ ] Price
  - [ ] Description
- [ ] Upload product images
- [ ] ✅ Submit listing
- [ ] ✅ Listing appears on dashboard
- [ ] ✅ Listing shows on marketplace

### View Listing
- [ ] Click on a listing
- [ ] ✅ All details display correctly
- [ ] ✅ Images show in gallery
- [ ] ✅ Seller information displays
- [ ] ✅ Contact button works
- [ ] ✅ Map shows location (if available)

### Edit Listing
- [ ] Go to your listing
- [ ] Click edit
- [ ] Modify details
- [ ] ✅ Changes save
- [ ] ✅ Updates reflect on marketplace

### Delete Listing
- [ ] Click delete on listing
- [ ] ✅ Confirmation prompt shows
- [ ] Confirm deletion
- [ ] ✅ Listing removed from dashboard
- [ ] ✅ Listing removed from marketplace

---

## 4. Marketplace/Homepage Testing 🏪

### Homepage Load
- [ ] Visit `/`
- [ ] ✅ Page loads (check speed - should be < 3s after image optimization)
- [ ] ✅ Navigation bar displays
- [ ] ✅ Price ticker scrolls
- [ ] ✅ Listings grid displays
- [ ] ✅ All images load (with lazy loading)

### Search & Filter
- [ ] Use search bar
- [ ] ✅ Search by product name works
- [ ] ✅ Search by variety works
- [ ] Filter by product type (Olive/Oil)
- [ ] ✅ Filters apply correctly
- [ ] Filter by location
- [ ] ✅ Distance calculation works (if location enabled)
- [ ] Clear filters
- [ ] ✅ Shows all listings again

### Grid/List View Toggle
- [ ] Switch to grid view
- [ ] ✅ Cards display in grid
- [ ] Switch to list view
- [ ] ✅ Listings display in list format

### Listing Cards
- [ ] Check listing cards display:
  - [ ] ✅ Product image (or fallback icon)
  - [ ] ✅ Product name
  - [ ] ✅ Price
  - [ ] ✅ Quantity
  - [ ] ✅ Seller name
  - [ ] ✅ Location (if available)
  - [ ] ✅ Status badge

---

## 5. Price Tracking System Testing 💰

### Local Prices
- [ ] Go to `/prices`
- [ ] ✅ Page displays
- [ ] ✅ Current prices show
- [ ] ✅ Price history chart displays (if data exists)
- [ ] ✅ Filter by olive variety
- [ ] ✅ Date range selection works

### Souk Prices
- [ ] Go to `/prices/souks`
- [ ] ✅ Souk list displays
- [ ] ✅ Prices for each souk show
- [ ] ✅ Can filter by region

### World Prices
- [ ] Go to `/prices/world`
- [ ] ✅ International prices display
- [ ] ✅ Currency conversion works (if implemented)

---

## 6. Language Switching Testing 🌐

### Language Switcher
- [ ] Click language switcher
- [ ] ✅ Dropdown shows (AR/FR/EN)
- [ ] Switch to Arabic
- [ ] ✅ Text changes to Arabic
- [ ] ✅ Layout switches to RTL
- [ ] Switch to French
- [ ] ✅ Text changes to French
- [ ] ✅ Layout switches to LTR
- [ ] Switch to English
- [ ] ✅ Text changes to English
- [ ] ✅ Layout switches to LTR

### Language Persistence
- [ ] Change language
- [ ] Refresh page
- [ ] ✅ Language persists
- [ ] Navigate to different pages
- [ ] ✅ Language remains same
- [ ] Logout and login
- [ ] ✅ User's preferred language loads

### Translation Coverage
- [ ] Check all pages have translations:
  - [ ] ✅ Homepage
  - [ ] ✅ Dashboard
  - [ ] ✅ Profile edit
  - [ ] ✅ Listings
  - [ ] ✅ Prices
  - [ ] ✅ Auth forms (login/register)
  - [ ] ✅ Error messages
  - [ ] ✅ Success messages

---

## 7. Responsive Design Testing 📱

### Mobile View (< 640px)
- [ ] Open on mobile or resize browser
- [ ] ✅ Navigation hamburger menu appears
- [ ] ✅ Menu closed by default (fixed!)
- [ ] ✅ Click hamburger opens menu
- [ ] ✅ Click outside closes menu
- [ ] ✅ All text readable
- [ ] ✅ Buttons easily tappable
- [ ] ✅ Images scale properly
- [ ] ✅ Forms usable
- [ ] ✅ Profile edit responsive (fixed!)
- [ ] ✅ Grid shows 1-2 columns

### Tablet View (640px - 1024px)
- [ ] Resize to tablet size
- [ ] ✅ Navigation shows desktop version
- [ ] ✅ Grid shows 2-3 columns
- [ ] ✅ Spacing appropriate
- [ ] ✅ Cover photos show 3 columns

### Desktop View (> 1024px)
- [ ] Full desktop view
- [ ] ✅ All elements properly spaced
- [ ] ✅ Grid shows 3-4 columns
- [ ] ✅ Cover photos show 4 columns
- [ ] ✅ Sidebar/navigation optimal

---

## 8. Image Upload Testing 📸

### Profile Picture Upload
- [ ] Test JPEG upload
- [ ] Test PNG upload
- [ ] Test GIF upload
- [ ] Test WebP upload
- [ ] Test 5MB file (should accept)
- [ ] Test 6MB file (should reject)
- [ ] ✅ Upload successful
- [ ] ✅ Image displays correctly

### Cover Photos Upload
- [ ] Upload 1 photo
- [ ] ✅ Works
- [ ] Upload 5 photos at once
- [ ] ✅ All upload
- [ ] Try uploading 6th (should limit to 5)
- [ ] Delete one photo
- [ ] ✅ Deletion works
- [ ] Upload replacement
- [ ] ✅ Works

### iPhone Upload Test
- [ ] Open on iPhone Safari
- [ ] Take photo with camera (HEIC format)
- [ ] Upload via profile edit
- [ ] ✅ Converts to JPEG automatically
- [ ] ✅ Upload successful
- [ ] ✅ Image displays

### Listing Images Upload
- [ ] Create/edit listing
- [ ] Upload product images
- [ ] ✅ Multiple images upload
- [ ] ✅ Images show in gallery
- [ ] ✅ Main image displays on card

---

## 9. Performance Testing ⚡

### Page Load Speed
- [ ] Homepage load time: _____ seconds (target: < 2s)
- [ ] Dashboard load time: _____ seconds (target: < 2s)
- [ ] Profile edit load time: _____ seconds (target: < 1s)
- [ ] Listing page load time: _____ seconds (target: < 2s)

### Image Optimization
- [ ] Check homepage images optimized (26MB → 1MB)
- [ ] ✅ Total page size < 2MB
- [ ] ✅ Images lazy load
- [ ] ✅ No layout shift (CLS)

### Database Performance
- [ ] Check for N+1 queries (use Debugbar if installed)
- [ ] ✅ Listings load with eager loading
- [ ] ✅ Profile loads efficiently
- [ ] ✅ No slow queries (< 100ms each)

### Browser DevTools Check
- [ ] Open DevTools (F12)
- [ ] Network tab:
  - [ ] ✅ Total transfer < 2MB
  - [ ] ✅ No failed requests
  - [ ] ✅ All images load
- [ ] Console tab:
  - [ ] ✅ No JavaScript errors
  - [ ] ✅ No 404 errors
- [ ] Lighthouse audit:
  - [ ] Performance: ___/100 (target: > 80)
  - [ ] Accessibility: ___/100 (target: > 90)
  - [ ] Best Practices: ___/100 (target: > 90)
  - [ ] SEO: ___/100 (target: > 90)

---

## 10. Security Testing 🔒

### CSRF Protection
- [ ] Try submitting form without CSRF token
- [ ] ✅ Request rejected
- [ ] Submit with valid token
- [ ] ✅ Request accepted

### Rate Limiting
- [ ] Try logging in 6 times with wrong password
- [ ] ✅ Rate limit triggered
- [ ] Wait 1 minute
- [ ] ✅ Can try again

### File Upload Security
- [ ] Try uploading .php file as image
- [ ] ✅ Upload rejected
- [ ] Try uploading .exe file
- [ ] ✅ Upload rejected
- [ ] Try uploading huge file (> 5MB)
- [ ] ✅ Upload rejected

### Authorization
- [ ] Logout
- [ ] Try accessing `/dashboard` directly
- [ ] ✅ Redirected to login
- [ ] Try accessing another user's profile edit
- [ ] ✅ Access denied or redirected

### XSS Protection
- [ ] Try entering `<script>alert('XSS')</script>` in form
- [ ] ✅ Script not executed
- [ ] ✅ Text escaped/sanitized

---

## 11. Error Handling Testing ⚠️

### 404 Page
- [ ] Visit non-existent page `/nonexistent`
- [ ] ✅ Custom 404 page shows (or default Laravel 404)
- [ ] ✅ Can navigate back to home

### 500 Error
- [ ] Simulate server error (if possible)
- [ ] ✅ Error page displays
- [ ] ✅ Error logged

### Validation Errors
- [ ] Submit empty form
- [ ] ✅ Validation errors display
- [ ] ✅ Error messages in correct language
- [ ] ✅ Form retains valid input

### Network Errors
- [ ] Disconnect internet
- [ ] Try submitting form
- [ ] ✅ Appropriate error message

---

## 12. Database Testing 💾

### Migrations
- [ ] Run `php artisan migrate:fresh`
- [ ] ✅ All tables created
- [ ] ✅ No errors
- [ ] Run `php artisan migrate:rollback`
- [ ] ✅ Tables dropped correctly

### Seeders
- [ ] Run `php artisan db:seed`
- [ ] ✅ Sample data created
- [ ] ✅ Can view seeded data

### Data Integrity
- [ ] Create user
- [ ] Create listing
- [ ] Delete user
- [ ] ✅ Check if cascade delete works (or orphaned listings handled)

---

## 13. Cross-Browser Testing 🌐

### Desktop Browsers
- [ ] **Chrome** (latest):
  - [ ] ✅ All features work
  - [ ] ✅ No visual issues
- [ ] **Firefox** (latest):
  - [ ] ✅ All features work
  - [ ] ✅ No visual issues
- [ ] **Safari** (latest):
  - [ ] ✅ All features work
  - [ ] ✅ No visual issues
- [ ] **Edge** (latest):
  - [ ] ✅ All features work
  - [ ] ✅ No visual issues

### Mobile Browsers
- [ ] **Safari iOS** (iPhone):
  - [ ] ✅ Navigation works
  - [ ] ✅ Forms work
  - [ ] ✅ Image upload works
- [ ] **Chrome Android**:
  - [ ] ✅ Navigation works
  - [ ] ✅ Forms work
  - [ ] ✅ Image upload works

---

## 14. Email Testing 📧

### Email Configuration
- [ ] Check `.env` has email settings
- [ ] Test email with `php artisan tinker`:
  ```php
  Mail::raw('Test', function($msg) {
      $msg->to('test@example.com')->subject('Test');
  });
  ```
- [ ] ✅ Email sent (or logged if using log driver)

### Registration Email
- [ ] Register new account
- [ ] ✅ Welcome email sent (if configured)

### Password Reset Email
- [ ] Request password reset
- [ ] ✅ Reset email sent
- [ ] ✅ Reset link works

---

## 15. SEO & Meta Tags Testing 🔍

### Meta Tags
- [ ] View page source
- [ ] ✅ Title tag present
- [ ] ✅ Description meta tag present
- [ ] ✅ Keywords meta tag present
- [ ] ✅ OG tags for social sharing
- [ ] ✅ Canonical URL set

### Structured Data
- [ ] Check for JSON-LD structured data
- [ ] ✅ WebSite schema present
- [ ] Validate at https://search.google.com/test/rich-results

### Sitemap
- [ ] Check if `/sitemap.xml` exists
- [ ] ✅ Contains all main pages
- [ ] ✅ Valid XML format

### Robots.txt
- [ ] Check `/robots.txt`
- [ ] ✅ Allows search engines
- [ ] ✅ Blocks admin/private pages

---

## 16. API/AJAX Testing 🔌

### Location-based Features
- [ ] Enable location on device
- [ ] ✅ Location permission prompt shows
- [ ] Grant permission
- [ ] ✅ User location saved
- [ ] ✅ Distance calculations work
- [ ] Deny permission
- [ ] ✅ Gracefully handled

### Dynamic Content
- [ ] Test search without page reload
- [ ] ✅ AJAX search works
- [ ] Test filters
- [ ] ✅ Filters update without reload

---

## 17. Accessibility Testing ♿

### Keyboard Navigation
- [ ] Navigate using Tab key
- [ ] ✅ Focus visible on all elements
- [ ] ✅ Can submit forms with Enter
- [ ] ✅ Can close modals with Esc

### Screen Reader
- [ ] Test with screen reader (if available)
- [ ] ✅ Alt text on images
- [ ] ✅ Form labels associated
- [ ] ✅ ARIA labels where needed

### Color Contrast
- [ ] Check text readability
- [ ] ✅ Sufficient contrast (4.5:1 minimum)
- [ ] Test with color blindness simulator
- [ ] ✅ Information not conveyed by color alone

---

## 18. Cache Testing 🗄️

### Config Cache
- [ ] Run `php artisan config:cache`
- [ ] ✅ No errors
- [ ] App still works
- [ ] Run `php artisan config:clear`

### Route Cache
- [ ] Run `php artisan route:cache`
- [ ] ✅ Routes still work
- [ ] Run `php artisan route:clear`

### View Cache
- [ ] Run `php artisan view:cache`
- [ ] ✅ Views still render
- [ ] Run `php artisan view:clear`

---

## 19. File Storage Testing 📁

### Storage Link
- [ ] Check `php artisan storage:link` was run
- [ ] ✅ Symlink exists: `public/storage → storage/app/public`
- [ ] Upload image
- [ ] ✅ Image accessible via URL

### Storage Permissions
- [ ] Check `storage/` permissions
- [ ] ✅ Writable by web server
- [ ] Check `bootstrap/cache/` permissions
- [ ] ✅ Writable by web server

---

## 20. Final Integration Test 🎬

### Complete User Journey
1. [ ] Visit homepage as guest
2. [ ] Browse listings
3. [ ] Click "Register"
4. [ ] Create account
5. [ ] ✅ Redirected to dashboard
6. [ ] Complete profile
7. [ ] Upload profile picture
8. [ ] Upload cover photos
9. [ ] Create new listing
10. [ ] View listing on marketplace
11. [ ] Edit listing
12. [ ] Search for own listing
13. [ ] Change language
14. [ ] Check prices page
15. [ ] Logout
16. [ ] Login again
17. [ ] ✅ All data persists

---

## Testing Summary

### Critical Issues Found:
```
1. ________________________________
2. ________________________________
3. ________________________________
```

### Minor Issues Found:
```
1. ________________________________
2. ________________________________
3. ________________________________
```

### Performance Metrics:
```
Homepage Load Time: _____ seconds
Dashboard Load Time: _____ seconds
Total Page Size: _____ MB
Lighthouse Score: _____ / 100
```

### Browser Compatibility:
```
Chrome: ✅ / ❌
Firefox: ✅ / ❌
Safari: ✅ / ❌
Mobile Safari: ✅ / ❌
Chrome Android: ✅ / ❌
```

---

## Pre-Deployment Checklist

Before creating deployment ZIP:

- [ ] ✅ All tests passed
- [ ] ✅ Images optimized (26MB → 1MB)
- [ ] ✅ No console errors
- [ ] ✅ No PHP errors in logs
- [ ] ✅ All translations working
- [ ] ✅ Mobile responsive
- [ ] ✅ Security tested
- [ ] ✅ Performance acceptable
- [ ] ✅ Database migrations ready
- [ ] ✅ Environment variables documented

---

## Next Steps

1. **Complete this testing checklist**
2. **Fix any issues found**
3. **Optimize remaining images** (if not done)
4. **Proceed to deployment guide** → `DEPLOYMENT_GUIDE.md`

---

**Testing Date**: _________________  
**Tested By**: _________________  
**Ready for Deployment**: ✅ / ❌

