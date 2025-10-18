# 📸 How to Add Activity Pictures - كيفية إضافة صور النشاط

## 🎯 Quick Steps - الخطوات السريعة

### Method 1: Manual Upload (Easiest) - الطريقة اليدوية (الأسهل)

#### Step 1: Prepare Your Images - جهز صورك
Find or take photos for each role:
- **Farm photo** for farmers (olive trees, farm view)
- **Truck photo** for carriers (delivery truck, fleet)
- **Mill photo** for mills (machinery, facility)
- **Packing photo** for packers (bottling line, warehouse)

#### Step 2: Rename Images - أعد تسمية الصور
Rename your images to these **exact names**:
```
farm-activity.jpg
truck-activity.jpg
mill-activity.jpg
packing-activity.jpg
```

#### Step 3: Place in Public Folder - ضعها في مجلد Public
Copy/move the images to:
```
/Users/zinehamdi/Sites/localhost/tuni-olive-hub/public/images/
```

**Terminal Command:**
```bash
# Navigate to your project
cd /Users/zinehamdi/Sites/localhost/tuni-olive-hub

# Create images directory if it doesn't exist
mkdir -p public/images

# Copy your images (example)
cp ~/Downloads/your-farm-photo.jpg public/images/farm-activity.jpg
cp ~/Downloads/your-truck-photo.jpg public/images/truck-activity.jpg
cp ~/Downloads/your-mill-photo.jpg public/images/mill-activity.jpg
cp ~/Downloads/your-packing-photo.jpg public/images/packing-activity.jpg
```

#### Step 4: Verify Files - تحقق من الملفات
```bash
ls -lh public/images/*-activity.jpg
```

You should see:
```
public/images/farm-activity.jpg
public/images/mill-activity.jpg
public/images/packing-activity.jpg
public/images/truck-activity.jpg
```

---

## 🖼️ Image Requirements - متطلبات الصور

### Size - الحجم
- **Minimum**: 400×400 pixels
- **Recommended**: 600×600 pixels or 800×800 pixels
- **Aspect Ratio**: Square (1:1) is best

### Format - الصيغة
- ✅ JPG/JPEG (best for photos)
- ✅ PNG (if you need transparency)
- ✅ WebP (smaller file size)

### File Size - حجم الملف
- **Maximum**: 500KB per image
- **Recommended**: 100-300KB
- Use image compression tools if needed

### Quality - الجودة
- High resolution
- Good lighting
- Professional appearance
- Clear subject

---

## 🎨 Image Optimization Tips - نصائح تحسين الصور

### Online Tools - أدوات أونلاين
1. **TinyPNG.com** - Compress images
2. **Squoosh.app** - Google's image compressor
3. **Canva.com** - Resize and edit

### macOS Built-in - مدمج في macOS
```bash
# Resize image to 600×600
sips -Z 600 input.jpg --out output.jpg

# Convert PNG to JPG
sips -s format jpeg input.png --out output.jpg
```

---

## 📁 File Structure - هيكل الملفات

After adding images:
```
public/
├── images/
│   ├── farm-activity.jpg      ← For farmers 🌱
│   ├── truck-activity.jpg     ← For carriers 🚚
│   ├── mill-activity.jpg      ← For mills ⚙️
│   ├── packing-activity.jpg   ← For packers 📦
│   ├── olive-oil.png          (already exists)
│   └── ... (other images)
```

---

## ✅ Testing - الاختبار

### 1. Check File Permissions
```bash
# Make sure files are readable
chmod 644 public/images/*-activity.jpg
```

### 2. Clear Cache
```bash
# Clear Laravel cache
php artisan cache:clear
php artisan view:clear
```

### 3. Test in Browser
1. Go to your dashboard
2. Look at the profile card at the top
3. The activity picture should show on the right side
4. If not showing, check browser console for errors

---

## 🔍 Troubleshooting - حل المشاكل

### Image Not Showing? - الصورة لا تظهر؟

**Check 1: File exists**
```bash
ls -la public/images/farm-activity.jpg
```

**Check 2: File name is correct**
- Must be **exact**: `farm-activity.jpg` (not `Farm-Activity.JPG`)
- Case-sensitive on some systems
- No spaces in filename

**Check 3: File path in code**
The code checks for:
```php
'images/farm-activity.jpg'     // For farmer role
'images/truck-activity.jpg'    // For carrier role
'images/mill-activity.jpg'     // For mill role
'images/packing-activity.jpg'  // For packer role
```

**Check 4: Role matches**
- Your user role must match the image
- Farmer sees farm-activity.jpg
- Carrier sees truck-activity.jpg
- Mill sees mill-activity.jpg
- Packer sees packing-activity.jpg

---

## 🎯 Role-Specific Mappings - تخصيص حسب الدور

| User Role | Image File | Label |
|-----------|-----------|-------|
| `farmer` | `farm-activity.jpg` | "My Farm" - "مزرعتي" |
| `carrier` | `truck-activity.jpg` | "My Fleet" - "أسطولي" |
| `mill` | `mill-activity.jpg` | "My Mill" - "معصرتي" |
| `packer` | `packing-activity.jpg` | "My Facility" - "منشأتي" |

---

## 📸 Sample Image Ideas - أفكار لصور نموذجية

### Farmer - مزارع 🌱
- Wide shot of olive grove
- Rows of olive trees
- Harvest season photo
- Tractor in the field
- Farm entrance/sign

### Carrier - ناقل 🚚
- Delivery truck with logo
- Fleet of vehicles
- Loading/unloading scene
- Truck on road
- Company branding

### Mill - معصرة ⚙️
- Oil extraction machinery
- Interior of mill facility
- Processing equipment
- Modern production line
- Facility exterior

### Packer - مُعبّئ 📦
- Bottling/packaging line
- Warehouse with products
- Labeling machines
- Quality control area
- Finished product display

---

## 🚀 Quick Test Images - صور اختبار سريعة

If you don't have real photos yet, you can use placeholder images:

### Option 1: Download Free Stock Photos
- **Unsplash.com** - Free high-quality photos
- **Pexels.com** - Free stock images
- **Pixabay.com** - Free images

Search for:
- "olive farm"
- "delivery truck"
- "olive oil mill"
- "packaging facility"

### Option 2: Use Placeholder Service (Temporary)
```bash
# Download placeholder images (400x400)
curl "https://via.placeholder.com/400/6A8F3B/FFFFFF?text=Farm" > public/images/farm-activity.jpg
curl "https://via.placeholder.com/400/3B8F8F/FFFFFF?text=Truck" > public/images/truck-activity.jpg
curl "https://via.placeholder.com/400/8F6A3B/FFFFFF?text=Mill" > public/images/mill-activity.jpg
curl "https://via.placeholder.com/400/6A3B8F/FFFFFF?text=Packing" > public/images/packing-activity.jpg
```

---

## 🔄 Future: Upload from Dashboard - مستقبلاً: رفع من لوحة التحكم

Coming soon features:
- Upload activity picture from profile settings
- Multiple photos gallery
- Crop and resize in browser
- Change photo anytime
- Photo approval system (admin)

---

## 📝 Complete Example Workflow - مثال كامل

```bash
# 1. Navigate to project
cd /Users/zinehamdi/Sites/localhost/tuni-olive-hub

# 2. Create images directory
mkdir -p public/images

# 3. Check current images
ls -lh public/images/

# 4. Copy your prepared images
# (Assume you have photos in ~/Desktop/)
cp ~/Desktop/my-farm.jpg public/images/farm-activity.jpg
cp ~/Desktop/my-truck.jpg public/images/truck-activity.jpg
cp ~/Desktop/my-mill.jpg public/images/mill-activity.jpg
cp ~/Desktop/my-packing.jpg public/images/packing-activity.jpg

# 5. Set correct permissions
chmod 644 public/images/*-activity.jpg

# 6. Verify
ls -lh public/images/*-activity.jpg

# 7. Clear cache
php artisan cache:clear

# 8. Test in browser
# Visit: http://your-domain.com/dashboard
```

---

## ✅ Success Checklist - قائمة النجاح

- [ ] Images are square (or close to square)
- [ ] Images are at least 400×400 pixels
- [ ] Files are named **exactly** as specified
- [ ] Files are in `public/images/` folder
- [ ] File permissions are correct (644)
- [ ] Cache is cleared
- [ ] Browser refreshed (Cmd+Shift+R)
- [ ] User role matches image (farmer sees farm, etc.)

---

## 🎉 Result - النتيجة

After adding images, your dashboard profile card will show:
- **Left**: Your profile picture
- **Center**: Your information and badges
- **Right**: Your activity picture (farm/truck/mill/packing)

This creates a professional, visual identity for your profile!

---

**Need Help?**
If images still don't show:
1. Check browser console (F12) for errors
2. Verify file path: `http://your-domain.com/images/farm-activity.jpg`
3. Check user role in database
4. Ensure Laravel storage is linked: `php artisan storage:link`

---

**Created:** October 16, 2025
**Language:** English & Arabic
**Difficulty:** ⭐ Easy (Beginner friendly)
