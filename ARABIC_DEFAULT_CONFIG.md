# Arabic as Default Language Configuration

## ✅ Completed Configuration

### 1. Environment Variables (.env)
```env
APP_LOCALE=ar
APP_FALLBACK_LOCALE=ar
```

**Purpose**: Sets Arabic as the primary and fallback language for the entire application.

### 2. Configuration File (config/app.php)
```php
'locale' => env('APP_LOCALE', 'ar'),
'fallback_locale' => env('APP_FALLBACK_LOCALE', 'ar'),
```

**Purpose**: Ensures Arabic is used even if .env variables are missing.

### 3. SetLocale Middleware (app/Http/Middleware/SetLocale.php)
```php
public function handle(Request $request, Closure $next)
{
    $supported = ['ar', 'fr', 'en'];
    $defaultLocale = 'ar'; // Always default to Arabic
    
    // Check for language in query parameter
    $lang = $request->query('lang');
    if ($lang && in_array($lang, $supported, true)) {
        Session::put('locale', $lang);
    }

    // Get locale from session, or use Arabic as default
    $locale = Session::get('locale', $defaultLocale);
    
    // Validate locale is supported, otherwise use Arabic
    if (! in_array($locale, $supported, true)) {
        $locale = $defaultLocale;
    }

    AppFacade::setLocale($locale);

    return $next($request);
}
```

**Features**:
- ✅ Explicitly defaults to Arabic ('ar')
- ✅ Supports language switching via query parameter (?lang=ar|fr|en)
- ✅ Stores language preference in session
- ✅ Falls back to Arabic for any unsupported language

### 4. Layout Direction (RTL Support)
All layouts dynamically set direction based on locale:

**layouts/app.blade.php:**
```blade
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
```

**components/guest-layout.blade.php:**
```blade
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
```

### 5. Language Switcher (home_marketplace.blade.php)
```blade
<div class="flex items-center gap-1 border-2 border-gray-200 rounded-lg overflow-hidden">
    <a href="{{ route('lang.switch', 'ar') }}" 
       class="px-3 py-1.5 text-sm font-bold transition {{ app()->getLocale() === 'ar' ? 'bg-[#6A8F3B] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        AR
    </a>
    <a href="{{ route('lang.switch', 'fr') }}" 
       class="px-3 py-1.5 text-sm font-bold transition {{ app()->getLocale() === 'fr' ? 'bg-[#6A8F3B] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        FR
    </a>
    <a href="{{ route('lang.switch', 'en') }}" 
       class="px-3 py-1.5 text-sm font-bold transition {{ app()->getLocale() === 'en' ? 'bg-[#6A8F3B] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        EN
    </a>
</div>
```

**Features**:
- ✅ Shows AR as active by default (green background)
- ✅ Allows users to switch to French or English
- ✅ Remembers preference in session
- ✅ Visual feedback for active language

### 6. Route Configuration (routes/web.php)
```php
Route::get('/lang/{locale}', function (string $locale) {
    $supported = ['ar','fr','en'];
    if (in_array($locale, $supported, true)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');
```

## 🎯 How It Works

### First Visit (New User)
1. User opens the website
2. No session exists → Middleware sets Arabic from config
3. Page renders in Arabic with RTL layout
4. Language switcher shows AR as active

### Switching Languages
1. User clicks FR or EN in language switcher
2. Route stores preference in session: `session(['locale' => 'fr'])`
3. Page reloads with new language
4. RTL automatically switches to LTR for FR/EN

### Returning User
1. User returns to website
2. Session contains saved preference
3. Middleware loads saved language
4. If session expired → Falls back to Arabic

### Language Persistence
```
User Action               Session Value    Display Language
─────────────────────────────────────────────────────────────
First visit              null             Arabic (default)
Click FR                 'fr'             French
Close browser            'fr' (kept)      French
Reopen (same session)    'fr'             French
Session expires          null             Arabic (default)
Click EN                 'en'             English
Click AR                 'ar'             Arabic
```

## 🌍 Supported Languages

| Code | Language | Direction | Status |
|------|----------|-----------|--------|
| ar   | العربية  | RTL       | ✅ Default |
| fr   | Français | LTR       | ✅ Available |
| en   | English  | LTR       | ✅ Available |

## 🔧 Configuration Hierarchy

The application determines language in this order:

1. **Session** (if user switched language)
2. **.env** file (`APP_LOCALE=ar`)
3. **Config** file (`config/app.php` → `'locale' => 'ar'`)
4. **Hardcoded** default in middleware (`$defaultLocale = 'ar'`)

Result: **Arabic is ALWAYS the default** ✅

## 📝 Testing Instructions

### Test 1: Fresh Visit
```bash
# Clear all sessions and cache
php artisan cache:clear
php artisan session:flush

# Visit homepage
open http://localhost:8000

# Expected: Page in Arabic, AR button highlighted
```

### Test 2: Language Switching
```bash
# Click FR button
# Expected: Page switches to French, FR button highlighted

# Click EN button  
# Expected: Page switches to English, EN button highlighted

# Click AR button
# Expected: Page switches back to Arabic, AR button highlighted
```

### Test 3: Session Persistence
```bash
# Click FR button (switches to French)
# Reload page (F5)
# Expected: Still in French

# Close browser
# Reopen and visit site
# Expected: Still in French (if session still valid)
```

### Test 4: Direct Language Links
```bash
# Visit: http://localhost:8000?lang=en
# Expected: English

# Visit: http://localhost:8000?lang=fr
# Expected: French

# Visit: http://localhost:8000?lang=ar
# Expected: Arabic (default)

# Visit: http://localhost:8000?lang=invalid
# Expected: Arabic (fallback)
```

## 🚀 Deployment Notes

### Production Checklist
- [ ] Verify .env has `APP_LOCALE=ar`
- [ ] Verify .env has `APP_FALLBACK_LOCALE=ar`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Test language switcher on production
- [ ] Verify RTL layout works correctly
- [ ] Check Arabic text displays properly

### CDN/Caching Considerations
If using CDN:
- Cache language-specific pages separately
- Use `Accept-Language` header for cache key
- Consider `Vary: Cookie` for session-based language

## 🎨 Arabic Typography

The platform uses proper Arabic fonts and styling:
- RTL text direction
- Proper Arabic number formatting
- Arabic date formatting (where applicable)
- Right-aligned text
- Arabic-friendly font family (system fonts)

## 📊 Analytics Recommendations

Track language usage:
```javascript
// Google Analytics example
gtag('event', 'language_change', {
  'event_category': 'Language',
  'event_label': 'ar|fr|en',
  'value': 1
});
```

Monitor:
- % of users in each language
- Language switching patterns
- Session duration by language
- Conversion rates by language

## ⚠️ Important Notes

1. **Arabic is Enforced**: Even if config files are misconfigured, the middleware has a hardcoded Arabic default
2. **Session-Based**: Language preference persists across page loads (until session expires)
3. **SEO Ready**: HTML `lang` attribute dynamically changes based on active language
4. **Fully Reversible**: Users can switch to any supported language at any time

---

**Configuration Date**: October 13, 2025  
**Version**: 1.0  
**Status**: ✅ Production Ready  
**Default Language**: 🇹🇳 Arabic (العربية)
