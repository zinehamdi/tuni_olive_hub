# Visit Publisher Profile Feature

## 🎯 Feature Overview

**Status**: ✅ Implemented (October 16, 2025)

Users can now **view seller/publisher profiles** directly from orders or anywhere in the system. This allows buyers to:
- See seller's complete profile information
- View seller's active listings
- Check seller's ratings and trust score
- Access contact information
- View seller's location and business details

---

## 📋 What Was Added

### 1. New Route
**File**: `routes/web.php`

```php
// Public user profile - accessible to anyone
Route::middleware('set.locale')->get('/user/{user}', [\App\Http\Controllers\ProfileController::class, 'viewPublicProfile'])->name('user.profile');
```

**Access**: 
- URL: `/user/{user_id}`
- Example: `https://yourdomain.com/user/5`
- Public access (no authentication required)
- Multi-language support (AR/FR/EN)

---

### 2. New Controller Method
**File**: `app/Http/Controllers/ProfileController.php`

**Method**: `viewPublicProfile(User $user)`

**Features**:
- Shows user's public information
- Displays active listings (paginated, 12 per page)
- Shows contact information (phone, email)
- Displays addresses/locations
- Shows statistics (active listings, total listings, trust score)
- Role-specific information (farmer, mill, carrier, packer details)
- Rating display (stars and average)

---

### 3. New View
**File**: `resources/views/profile/public.blade.php`

**Design**:
- ✨ Modern, responsive design
- 🎨 Beautiful gradient backgrounds
- 📱 Mobile-optimized
- 🌍 Multi-language support (AR/FR/EN)
- 🔄 RTL support for Arabic

**Sections**:
1. **Profile Header**
   - Cover photo
   - Profile picture
   - Name and role badge
   - Rating stars
   - Statistics (active listings, total listings, trust score)

2. **Contact Information Card**
   - Phone number (clickable tel: link)
   - Email address (clickable mailto: link)
   - Location/address

3. **Role-Specific Details Card**
   - Farmer: tree count, olive type, farm location
   - Mill: mill name, capacity
   - Carrier: company name, fleet size, truck capacity
   - Packer: packer name, packaging types

4. **Active Listings Grid**
   - Shows all active listings
   - Product details (variety, quality)
   - Prices
   - "View Details" links
   - Pagination

---

### 4. Enhanced API Response
**File**: `app/Http/Resources/OrderResource.php`

**Added to Order API response**:
```json
{
  "seller": {
    "id": 5,
    "name": "Ahmed Farmer",
    "role": "farmer",
    "profile_url": "https://domain.com/user/5",  ← NEW
    "profile_picture": "https://domain.com/storage/..."  ← NEW
  }
}
```

Now when you fetch an order via API, it includes:
- `seller.profile_url` - Direct link to seller's public profile
- `seller.profile_picture` - Seller's profile picture URL
- `buyer.profile_url` - Link to buyer's profile (if needed)

---

## 🚀 How to Use

### From API (Mobile App / Frontend)

When you get an order from the API:

```javascript
// Example API response
GET /api/v1/orders/123

{
  "data": {
    "id": 123,
    "seller": {
      "id": 5,
      "name": "Ahmed Farmer",
      "role": "farmer",
      "profile_url": "https://domain.com/user/5",  // ← Use this
      "profile_picture": "https://domain.com/storage/profile-pictures/xyz.jpg"
    },
    "total": "500.00",
    "status": "pending"
  }
}
```

**Add a button in your order view**:
```html
<a href="{{ seller.profile_url }}" target="_blank">
  View Seller Profile
</a>
```

Or in mobile app:
```javascript
<TouchableOpacity onPress={() => openURL(order.seller.profile_url)}>
  <Text>View Seller Profile</Text>
</TouchableOpacity>
```

---

### From Blade Templates (Web)

If you have a blade template showing order details:

```blade
{{-- Order details page --}}
<div class="order-details">
    <h2>Order #{{ $order->id }}</h2>
    
    {{-- Seller Information --}}
    <div class="seller-info">
        <h3>Seller: {{ $order->seller->name }}</h3>
        
        {{-- NEW: Visit Profile Button --}}
        <a href="{{ route('user.profile', $order->seller->id) }}" 
           class="btn btn-primary">
            @if($locale === 'ar')
                زيارة ملف البائع
            @elseif($locale === 'fr')
                Visiter le profil du vendeur
            @else
                Visit Seller Profile
            @endif
        </a>
    </div>
</div>
```

---

### Example Use Cases

#### 1. Order Confirmation Page
```blade
<div class="order-confirmed">
    <p>Your order has been placed with {{ $order->seller->name }}</p>
    <a href="{{ route('user.profile', $order->seller) }}" class="text-blue-600 hover:underline">
        View seller's profile and contact information →
    </a>
</div>
```

#### 2. Chat/Messaging Feature
```blade
<div class="chat-header">
    <img src="{{ Storage::url($seller->profile_picture) }}" class="w-10 h-10 rounded-full">
    <a href="{{ route('user.profile', $seller) }}">{{ $seller->name }}</a>
</div>
```

#### 3. Listing Card
```blade
<div class="listing-card">
    <h3>{{ $listing->title }}</h3>
    <div class="seller">
        Posted by: 
        <a href="{{ route('user.profile', $listing->seller) }}" class="text-green-600 hover:underline">
            {{ $listing->seller->name }}
        </a>
    </div>
</div>
```

---

## 🎨 Styling (Tailwind CSS)

The public profile page uses your app's design system:

**Color Scheme**:
- **Farmer**: Green (`bg-green-600`)
- **Carrier**: Blue (`bg-blue-600`)
- **Mill**: Amber (`bg-amber-600`)
- **Packer**: Purple (`bg-purple-600`)
- **Default**: Gray (`bg-gray-600`)

**Responsive Breakpoints**:
- Mobile: Full-width cards
- Tablet (md): 2-column listing grid
- Desktop (lg): 3-column layout (sidebar + listings)

---

## 📱 Mobile App Integration

If you're building a mobile app (React Native, Flutter), use the API:

```javascript
// Fetch order
const order = await fetch('/api/v1/orders/123').then(r => r.json());

// Open seller profile in WebView or browser
const openSellerProfile = () => {
  const profileUrl = order.data.seller.profile_url;
  
  // Option 1: In-app WebView
  navigation.navigate('WebView', { url: profileUrl });
  
  // Option 2: External browser
  Linking.openURL(profileUrl);
  
  // Option 3: Native profile screen (fetch user data separately)
  navigation.navigate('UserProfile', { userId: order.data.seller.id });
};
```

---

## 🔒 Privacy & Security

**What's visible**:
- ✅ Name
- ✅ Profile picture
- ✅ Role/badge
- ✅ Active listings
- ✅ Contact info (phone, email)
- ✅ Public addresses
- ✅ Ratings and trust score
- ✅ Role-specific details (farm name, mill capacity, etc.)

**What's NOT visible**:
- ❌ Password (never exposed)
- ❌ Email verification status
- ❌ Banned status
- ❌ Admin-only fields
- ❌ Private messages
- ❌ Order history (unless you're the buyer/seller)

**Access control**:
- Profile page is **public** (no login required)
- Users can view any active seller's profile
- Contact info is displayed (sellers expect to be contacted)

---

## 🌍 Multi-Language Support

The profile page automatically adapts to the user's language:

**Arabic (ar)**:
```
العروض النشطة
معلومات الاتصال
تفاصيل إضافية
```

**French (fr)**:
```
Offres actives
Coordonnées
Détails supplémentaires
```

**English (en)**:
```
Active Listings
Contact Information
Additional Details
```

**Language detection**:
- Reads from `app()->getLocale()`
- Supports RTL layout for Arabic
- Automatically translates all UI elements

---

## 🧪 Testing

### Manual Testing

1. **Create a test order**:
   ```bash
   php artisan tinker
   
   $order = \App\Models\Order::first();
   echo "Seller ID: " . $order->seller_id;
   ```

2. **Visit seller profile**:
   - Open: `http://localhost:8000/user/{seller_id}`
   - Example: `http://localhost:8000/user/5`

3. **Test from API**:
   ```bash
   curl http://localhost:8000/api/v1/orders/1 \
     -H "Authorization: Bearer YOUR_TOKEN" \
     | jq '.data.seller.profile_url'
   ```

### Automated Testing (Optional)

```php
// tests/Feature/UserProfileTest.php
public function test_public_profile_is_accessible()
{
    $user = User::factory()->create();
    
    $response = $this->get(route('user.profile', $user));
    
    $response->assertStatus(200);
    $response->assertSee($user->name);
}

public function test_order_includes_seller_profile_url()
{
    $order = Order::factory()->create();
    
    $response = $this->actingAs($order->buyer)
        ->getJson("/api/v1/orders/{$order->id}");
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            'seller' => ['id', 'name', 'profile_url', 'profile_picture']
        ]
    ]);
}
```

---

## 📊 Example Profile Views

### Farmer Profile
```
🌾 Ahmed's Olive Farm
⭐⭐⭐⭐⭐ 4.8 (24 reviews)

📊 Statistics:
- 12 Active Listings
- 45 Total Listings
- 95% Trust Score

🌾 Farm Details:
- Tree Count: 500
- Olive Type: Chemlali
- Location: Sfax, Mahres

📞 Contact:
- Phone: +216 XX XXX XXX
- Email: ahmed@example.com
```

### Mill Profile
```
🏭 Sfax Premium Mill
⭐⭐⭐⭐☆ 4.5 (18 reviews)

📊 Statistics:
- 8 Active Listings
- 32 Total Listings
- 90% Trust Score

🏭 Mill Details:
- Mill Name: Sfax Premium Mill
- Capacity: 5000 kg/day

📞 Contact:
- Phone: +216 XX XXX XXX
- Email: mill@example.com
```

---

## 🐛 Troubleshooting

### Profile page shows 404
- **Cause**: User ID doesn't exist
- **Fix**: Check that user exists in database
- **Query**: `SELECT * FROM users WHERE id = ?`

### Profile picture not showing
- **Cause**: Storage symlink not created
- **Fix**: Run `php artisan storage:link`
- **Verify**: Check `public/storage` folder exists

### Contact info not visible
- **Cause**: User hasn't filled phone/email
- **Fix**: Update user profile with contact details
- **Query**: `UPDATE users SET phone = '+216...', email = '...' WHERE id = ?`

### Listings not showing
- **Cause**: No active listings
- **Check**: `SELECT * FROM listings WHERE user_id = ? AND status = 'active'`
- **Fix**: Create listings or change status to 'active'

---

## 🔄 Future Enhancements (Optional)

Potential additions you could make:

1. **Reviews Section**
   - Show user reviews/testimonials
   - Rating breakdown (5★, 4★, 3★, etc.)

2. **Social Links**
   - Facebook, WhatsApp, Instagram links
   - Share profile feature

3. **Map Integration**
   - Show seller location on map
   - Distance calculator

4. **Message Button**
   - Direct chat with seller
   - WhatsApp quick link

5. **Follow/Favorite**
   - Allow users to follow sellers
   - Get notifications on new listings

6. **Badge System**
   - Verified seller badge
   - Top-rated seller badge
   - New seller badge

---

## ✅ Deployment Checklist

Before deploying to production:

- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Test on mobile devices
- [ ] Verify storage symlink exists
- [ ] Check all profile images load
- [ ] Test with users who have no profile pictures
- [ ] Test with users who have no listings
- [ ] Verify multi-language works (AR/FR/EN)
- [ ] Test RTL layout for Arabic
- [ ] Check page load speed (should be < 2s)

---

## 📚 Related Files

**Backend**:
- `routes/web.php` - Route definition
- `app/Http/Controllers/ProfileController.php` - Controller method
- `app/Http/Resources/OrderResource.php` - API response enhancement
- `app/Models/User.php` - User model (already has addresses relationship)

**Frontend**:
- `resources/views/profile/public.blade.php` - Main profile view

**Documentation**:
- `IDE_DIAGNOSTICS_EXPLAINED.md` - Explains IDE false positives
- `VISIT_PUBLISHER_PROFILE_FEATURE.md` - This file

---

## 🎉 Summary

**What you can do now**:
1. ✅ View any seller's public profile
2. ✅ See seller's contact info (phone, email, location)
3. ✅ Browse seller's active listings
4. ✅ Check seller's ratings and trust score
5. ✅ Access from orders, listings, or direct URL
6. ✅ API includes profile URLs for easy integration
7. ✅ Works in AR/FR/EN with RTL support

**Example URL**: `https://yourdomain.com/user/5`

**API Enhancement**: All order responses now include `seller.profile_url` and `buyer.profile_url`

---

*Feature implemented: October 16, 2025*
*Laravel Version: 12.30.1*
*Compatible with existing deployment plan*
