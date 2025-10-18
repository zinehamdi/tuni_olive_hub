# Troubleshooting Upload Blocking Issue 🔧

## Issue
Upload is "blocking" when trying to upload a large image - the page seems to freeze or hang.

## Possible Causes

### 1. **Browser Timeout** ⏱️
Large uploads (15-50MB) can take 30-60 seconds on slower connections. The browser might appear frozen but is actually uploading.

**Solution**: Be patient and wait for the upload to complete.

**Check**: Open browser's Network tab (F12 → Network) to see upload progress.

### 2. **Server Not Using Custom Config** ⚙️
The server might not be using the `php-custom.ini` configuration.

**Verify**:
```bash
# Visit this URL in browser:
http://localhost:8000/phpinfo-test.php

# Should show:
# upload_max_filesize: 100M
# post_max_size: 100M
# memory_limit: 256M
```

**If it shows old values (2M/8M)**, restart the server:
```bash
pkill -f "php artisan serve"
php -c php-custom.ini artisan serve --host=0.0.0.0 --port=8000
```

### 3. **JavaScript Blocking** 🚫
Form validation or JavaScript might be preventing the upload.

**Test**:
1. Open browser console (F12 → Console)
2. Try uploading
3. Look for error messages in red

**Common errors**:
- "File size too large"
- "Invalid file type"
- CORS errors

### 4. **Network/Firewall Issue** 🔒
Your firewall or network might be blocking large POST requests.

**Test**:
```bash
# Try uploading via command line
curl -X POST http://localhost:8000/register \
  -F "profile_picture=@/path/to/large-image.jpg" \
  -F "name=Test" \
  -F "email=test@example.com"
```

### 5. **Session Timeout** 🕐
If you're on the registration page too long, the session might expire.

**Solution**:
1. Refresh the page
2. Fill form quickly
3. Upload immediately

---

## Step-by-Step Debugging

### Step 1: Verify Server Configuration

Visit: http://localhost:8000/phpinfo-test.php

**Expected output:**
```
upload_max_filesize: 100M
post_max_size: 100M
memory_limit: 256M
```

**If wrong**, restart server:
```bash
./start-server.sh
```

### Step 2: Check Browser Console

1. Press F12 (or Cmd+Option+I on Mac)
2. Go to Console tab
3. Try uploading
4. Look for errors

### Step 3: Check Network Tab

1. Press F12 → Network tab
2. Try uploading
3. Look for the POST request
4. Check:
   - Status (should be 200 or 302)
   - Time (might take 30-60 seconds)
   - Size (should match your file)

### Step 4: Test with Smaller File First

1. Try uploading a 1MB image first
2. If it works, the configuration is correct
3. Then try 5MB
4. Then try 15MB

This helps identify if it's a size-specific issue.

### Step 5: Check Storage Permissions

```bash
# Ensure storage directory is writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## Quick Fixes

### Fix 1: Restart Server with Correct Config

```bash
pkill -f "php artisan serve"
php -c php-custom.ini artisan serve --host=0.0.0.0 --port=8000
```

### Fix 2: Clear Browser Cache

- Press Cmd+Shift+R (Mac) or Ctrl+Shift+R (Windows/Linux)
- This forces a hard refresh

### Fix 3: Increase Timeout Further

Edit `php-custom.ini`:
```ini
max_execution_time = 300  ; 5 minutes
max_input_time = 300
```

Then restart server.

### Fix 4: Disable Form Validation Temporarily

In your browser console, paste:
```javascript
document.querySelector('form').removeAttribute('onsubmit');
```

This removes JavaScript validation to test if that's the issue.

---

## Still Not Working?

### Check Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

Try uploading while watching the logs. Any errors will appear here.

### Enable Debug Mode

Edit `.env`:
```env
APP_DEBUG=true
```

This will show detailed error messages on the page.

### Test Upload Directly

Create a test route to isolate the issue:

**File**: `routes/web.php`
```php
Route::post('/test-upload', function(Request $request) {
    if ($request->hasFile('test_file')) {
        $file = $request->file('test_file');
        return response()->json([
            'size' => $file->getSize(),
            'type' => $file->getMimeType(),
            'name' => $file->getClientOriginalName(),
        ]);
    }
    return 'No file uploaded';
});
```

**Test it**:
```bash
curl -X POST http://localhost:8000/test-upload \
  -F "test_file=@/path/to/image.jpg"
```

---

## Common Symptoms & Solutions

### "Page just spins/freezes"
- **Cause**: Upload is in progress, taking time
- **Solution**: Wait 60 seconds, check Network tab for progress

### "413 Request Entity Too Large"
- **Cause**: Server limits still too low
- **Solution**: Verify php-custom.ini is being used

### "500 Internal Server Error"
- **Cause**: PHP memory limit or timeout
- **Solution**: Increase memory_limit to 512M

### "Nothing happens when I click upload"
- **Cause**: JavaScript error
- **Solution**: Check browser console for errors

### "Upload works for small files but not large ones"
- **Cause**: Timeout or memory issue
- **Solution**: Increase max_execution_time and memory_limit

---

## Prevention Checklist

- [ ] Always start server with: `./start-server.sh`
- [ ] Verify config loaded: Check phpinfo-test.php
- [ ] Clear browser cache after config changes
- [ ] Monitor browser Network tab during upload
- [ ] Check Laravel logs for errors
- [ ] Test with progressively larger files

---

## Contact Info

If none of these solutions work, gather this info:

1. Output of: `php -c php-custom.ini -r "phpinfo();" | grep -E "upload|post_max"`
2. Browser console errors (screenshot)
3. Laravel log errors: `tail storage/logs/laravel.log`
4. File size you're trying to upload
5. Browser and version

This will help diagnose the exact issue.

---

**Last Updated**: October 17, 2025  
**Status**: Debugging in progress  
**Server**: Running with php-custom.ini ✅
