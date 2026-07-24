@extends('layouts.app')

@section('content')
@php
  $apiBase = url('/api/v1');
  $address = $listing->seller->addresses->first();
@endphp
<div dir="rtl" class="min-h-screen bg-[#F8F4EC] text-gray-900">
  <header class="sticky top-0 z-50 bg-white/90 border-b">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">← رجوع لوحة التحكم</a>
      <h1 class="text-lg font-bold">تعديل العرض</h1>
    </div>
  </header>

  <main class="max-w-3xl mx-auto px-4 py-8">
    <div class="mx-auto max-w-xl">
      <div class="bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl shadow-xl p-6 lg:p-8 space-y-6">
        <h2 class="text-2xl font-extrabold text-[#1B2A1B]">تعديل العرض الحالي</h2>
        <p class="text-sm text-gray-600">عدل تفاصيل عرضك للحفاظ على دقة معلومات البيع والشحن.</p>
        
        <form id="listingForm" method="POST" action="{{ route('listings.update', $listing->id) }}" class="space-y-6" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <input type="hidden" name="seller_id" value="{{ auth()->id() }}" />
          
          <!-- Category & Product Info -->
          <div class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-4 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-[#1B2A1B] border-b pb-2">تفاصيل المنتج</h3>
            
            <div>
              <label for="category" class="block text-[#C8A356] font-semibold mb-1">نوع المنتج</label>
              <select id="category" name="category" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" required>
                <option value="oil" {{ optional($listing->product)->type === 'oil' ? 'selected' : '' }}>زيت زيتون</option>
                <option value="olive" {{ optional($listing->product)->type === 'olive' ? 'selected' : '' }}>زيتون</option>
              </select>
            </div>

            <div>
              <label for="variety" class="block text-[#C8A356] font-semibold mb-1">النوع / الصنف (Variety)</label>
              <input id="variety" name="variety" type="text" value="{{ optional($listing->product)->variety }}" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" placeholder="مثال: شملالي، ساحلي، شتوي" required/>
            </div>

            <div>
              <label for="quality" class="block text-[#C8A356] font-semibold mb-1">الجودة (Quality) - اختياري</label>
              <input id="quality" name="quality" type="text" value="{{ optional($listing->product)->quality }}" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" placeholder="مثال: بكر ممتاز (Extra Virgin)"/>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label for="quantity" class="block text-[#C8A356] font-semibold mb-1">الكمية المتوفرة</label>
                <input id="quantity" name="quantity" type="number" step="0.01" value="{{ $listing->quantity }}" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" required/>
              </div>
              <div>
                <label for="unit" class="block text-[#C8A356] font-semibold mb-1">الوحدة (Unit)</label>
                <select id="unit" name="unit" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" required>
                  <option value="kg" {{ ($listing->unit === 'kg' || $listing->unit === 'كيلو' || !$listing->unit) ? 'selected' : '' }}>كيلو جرام (kg)</option>
                  <option value="liter" {{ ($listing->unit === 'liter' || $listing->unit === 'لتر') ? 'selected' : '' }}>لتر (liter)</option>
                  <option value="ton" {{ ($listing->unit === 'ton' || $listing->unit === 'طن') ? 'selected' : '' }}>طن (ton)</option>
                  <option value="bottle" {{ ($listing->unit === 'bottle' || $listing->unit === 'قارورة') ? 'selected' : '' }}>قارورة (bottle)</option>
                </select>
              </div>
              <div>
                <label for="price" class="block text-[#C8A356] font-semibold mb-1">السعر (لكل وحدة)</label>
                <input id="price" name="price" type="number" step="0.01" value="{{ $listing->price }}" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" required/>
              </div>
            </div>

            <div>
              <label for="min_order" class="block text-[#C8A356] font-semibold mb-1">أدنى كمية للطلب</label>
              <input id="min_order" name="min_order" type="number" step="any" min="0" value="{{ $listing->formatted_min_order }}" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" placeholder="مثال: 100"/>
            </div>
            
            <div>
              <label for="status" class="block text-[#C8A356] font-semibold mb-1">الحالة</label>
              <select id="status" name="status" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition">
                <option value="draft" {{ $listing->status === 'draft' ? 'selected' : '' }}>مسودة</option>
                <option value="active" {{ $listing->status === 'active' ? 'selected' : '' }}>نشط</option>
                <option value="paused" {{ $listing->status === 'paused' ? 'selected' : '' }}>موقوف مؤقتاً</option>
              </select>
            </div>
          </div>

          <!-- Payment and Delivery -->
          <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-4 shadow-sm">
              <div class="text-[#C8A356] font-semibold mb-2">طرق الدفع</div>
              <div class="space-y-2 border border-[#C7D1C7] rounded-xl p-3">
                @php
                  $pm = is_array($listing->payment_methods) ? $listing->payment_methods : json_decode($listing->payment_methods ?? '[]', true);
                  $pm = is_array($pm) ? $pm : [];
                @endphp
                <label class="flex items-center gap-2"><input type="checkbox" name="payment_methods[]" value="cash" {{ in_array('cash', $pm) ? 'checked' : '' }}> نقداً</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="payment_methods[]" value="bank_transfer" {{ in_array('bank_transfer', $pm) ? 'checked' : '' }}> تحويل بنكي</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="payment_methods[]" value="check" {{ in_array('check', $pm) ? 'checked' : '' }}> شيك</label>
              </div>
            </div>

            <div class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-4 shadow-sm">
              <div class="text-[#C8A356] font-semibold mb-2">خيارات التوصيل</div>
              <div class="space-y-2 border border-[#C7D1C7] rounded-xl p-3">
                @php
                  $do = is_array($listing->delivery_options) ? $listing->delivery_options : json_decode($listing->delivery_options ?? '[]', true);
                  $do = is_array($do) ? $do : [];
                @endphp
                <label class="flex items-center gap-2"><input type="checkbox" name="delivery_options[]" value="pickup" {{ in_array('pickup', $do) ? 'checked' : '' }}> استلام من المصدر</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="delivery_options[]" value="carrier" {{ in_array('carrier', $do) ? 'checked' : '' }}> عبر ناقل</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="delivery_options[]" value="export" {{ in_array('export', $do) ? 'checked' : '' }}> للتصدير</label>
              </div>
            </div>
          </div>

          <!-- Geolocation Section -->
          <div class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-4 shadow-sm space-y-4" x-data="locationPicker()">
            <h3 class="text-lg font-bold text-[#1B2A1B] border-b pb-2">تحديد الموقع</h3>
            
            <div class="flex flex-col gap-2">
              <button type="button" @click="getCurrentLocation($event)" class="w-full min-h-[44px] py-3 px-4 bg-gradient-to-r from-[#6A8F3B] to-[#1B2A1B] text-white font-bold rounded-xl shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                <span x-text="locationSuccess ? '✓ تم تحديد الموقع بنجاح' : '📍 حدد موقعي الحالي'"></span>
              </button>
              <div x-show="locationError" x-text="locationError" class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg p-2"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-[#C8A356] font-semibold mb-1">الولاية</label>
                <select name="governorate" x-model="formData.governorate" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] transition" required>
                  <option value="">اختر الولاية</option>
                  <option value="تونس">تونس</option>
                  <option value="أريانة">أريانة</option>
                  <option value="بن عروس">بن عروس</option>
                  <option value="منوبة">منوبة</option>
                  <option value="نابل">نابل</option>
                  <option value="زغوان">زغوان</option>
                  <option value="بنزرت">بنزرت</option>
                  <option value="باجة">باجة</option>
                  <option value="جندوبة">جندوبة</option>
                  <option value="الكاف">الكاف</option>
                  <option value="سليانة">سليانة</option>
                  <option value="القيروان">القيروان</option>
                  <option value="القصرين">القصرين</option>
                  <option value="سيدي بوزيد">سيدي بوزيد</option>
                  <option value="صفاقس">صفاقس</option>
                  <option value="قفصة">قفصة</option>
                  <option value="توزر">توزر</option>
                  <option value="قبلي">قبلي</option>
                  <option value="مدنين">مدنين</option>
                  <option value="تطاوين">تطاوين</option>
                  <option value="قابس">قابس</option>
                  <option value="المنستير">المنستير</option>
                  <option value="المهدية">المهدية</option>
                  <option value="سوسة">سوسة</option>
                </select>
              </div>
              <div>
                <label class="block text-[#C8A356] font-semibold mb-1">المعتمدية (اختياري)</label>
                <input name="delegation" type="text" x-model="formData.delegation" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] transition" placeholder="مثال: حمام الأنف"/>
              </div>
            </div>

            <div>
              <label class="block text-[#C8A356] font-semibold mb-1">وصف العنوان (مثال: قرب معصرة كذا)</label>
              <input name="location_text" type="text" x-model="formData.location_text" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] transition" placeholder="مثال: قرب المعصرة المركزية"/>
            </div>

            <!-- Hidden Lat / Lng inputs -->
            <input type="hidden" name="latitude" x-model="formData.latitude" />
            <input type="hidden" name="longitude" x-model="formData.longitude" />
          </div>

          <!-- Listing Images Section -->
          <div class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-4 shadow-sm space-y-4" x-data="editMediaManager({{ json_encode($listing->media ?? []) }})">
            <input type="hidden" name="keep_media_specified" value="1">
            <div class="flex items-center justify-between border-b pb-2">
              <h3 class="text-lg font-bold text-[#1B2A1B]">صور المنتج (Product Photos)</h3>
              <span class="text-xs text-gray-500 font-semibold">يمكنك حذف صور سابقة أو إضافة صور جديدة بسهولة</span>
            </div>

            <!-- Existing & New Images Visual Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 pt-2">
              <!-- Kept Existing Images -->
              <template x-for="(img, index) in existingImages" :key="img">
                <div class="relative group rounded-xl overflow-hidden border-2 border-gray-200 bg-black/5 aspect-square shadow-sm">
                  <input type="hidden" name="keep_media[]" :value="img">
                  <img :src="'/storage/' + img" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                  <button type="button" @click="removeExistingImage(index)" 
                          title="حذف هذه الصورة"
                          class="absolute top-2 right-2 w-8 h-8 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center shadow-lg transition transform hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                  <div class="absolute bottom-1 right-1 bg-black/60 text-white text-[10px] px-2 py-0.5 rounded backdrop-blur">حالية</div>
                </div>
              </template>

              <!-- New Image Previews -->
              <template x-for="(preview, index) in newPreviews" :key="index">
                <div class="relative group rounded-xl overflow-hidden border-2 border-emerald-500 bg-emerald-50/50 aspect-square shadow-sm">
                  <img :src="preview.url" class="w-full h-full object-cover">
                  <button type="button" @click="removeNewPreview(index)" 
                          title="إلغاء الصورة الجديدة"
                          class="absolute top-2 right-2 w-8 h-8 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center shadow-lg transition transform hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                  <div class="absolute bottom-1 right-1 bg-emerald-600 text-white text-[10px] px-2 py-0.5 rounded font-bold">جديدة</div>
                </div>
              </template>

              <!-- Dropzone / Add Button -->
              <label class="border-2 border-dashed border-[#6A8F3B] hover:border-[#5a7a2f] rounded-xl flex flex-col items-center justify-center p-3 bg-emerald-50/30 hover:bg-emerald-50/80 cursor-pointer transition aspect-square text-center relative group">
                <input type="file" name="images[]" multiple accept="image/*" @change="handleFileSelect($event)" class="hidden">
                <div class="w-10 h-10 rounded-full bg-[#6A8F3B]/10 text-[#6A8F3B] group-hover:bg-[#6A8F3B] group-hover:text-white flex items-center justify-center transition mb-1">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                </div>
                <span class="text-xs font-bold text-[#6A8F3B] group-hover:text-[#5a7a2f]">إضافة صور</span>
                <span class="text-[10px] text-gray-500">JPG, PNG, WEBP</span>
              </label>
            </div>
          </div>

          @if($errors->any())
            <div class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-xl p-3" role="alert">
              <ul class="list-disc list-inside space-y-1 font-bold">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="sticky bottom-4 flex justify-end gap-3 z-10 bg-[#F8F4EC]/85 p-3 rounded-2xl backdrop-blur-sm border">
            <button type="button" onclick="window.history.back()" class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-[#9B6A4A] to-[#F8F4EC] text-gray-900 shadow-md hover:shadow-lg hover:scale-105 transition">إلغاء</button>
            <button type="submit" class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-[#1B2A1B] to-[#6A8F3B] text-white font-bold shadow-md hover:shadow-lg hover:scale-105 transition focus:ring-2 focus:ring-[#C8A356]">حفظ التعديلات</button>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>

<script>
function editMediaManager(initialImages) {
  return {
    existingImages: Array.isArray(initialImages) ? initialImages : [],
    newPreviews: [],
    removeExistingImage(index) {
      this.existingImages.splice(index, 1);
    },
    removeNewPreview(index) {
      this.newPreviews.splice(index, 1);
    },
    handleFileSelect(event) {
      const files = event.target.files;
      if (!files) return;
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const url = URL.createObjectURL(file);
        this.newPreviews.push({ file, url });
      }
    }
  }
}

// Alpine.js data binding for location parameters inside blade
function locationPicker() {
  return {
    locationError: '',
    locationSuccess: false,
    formData: {
      latitude: '{{ optional($address)->lat }}',
      longitude: '{{ optional($address)->lng }}',
      governorate: '{{ optional($address)->governorate }}',
      delegation: '{{ optional($address)->delegation }}',
      location_text: '{{ optional($address)->label }}'
    },
    
    getCurrentLocation(event) {
      this.locationError = '';
      this.locationSuccess = false;
      
      if (!navigator.geolocation) {
        this.locationError = 'المتصفح لا يدعم تحديد الموقع الجغرافي';
        return;
      }
      
      const button = event ? event.currentTarget : null;
      const originalHTML = button ? button.innerHTML : '';
      
      if (button) {
        button.disabled = true;
        button.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
      }

      const successCallback = (position) => {
        this.formData.latitude = position.coords.latitude.toFixed(6);
        this.formData.longitude = position.coords.longitude.toFixed(6);
        this.locationSuccess = true;
        this.locationError = '';
        if (button) {
          button.disabled = false;
          button.innerHTML = '✓ تم تحديد الموقع بنجاح';
          setTimeout(() => {
            button.innerHTML = originalHTML;
          }, 2000);
        }
      };
      
      // Fallback Geolocation engine (GPS -> Wi-Fi/Cellular -> IP Geolocation)
      navigator.geolocation.getCurrentPosition(
        successCallback,
        (error) => {
          console.warn('High accuracy location failed. Retrying with low accuracy...', error.message);
          
          navigator.geolocation.getCurrentPosition(
            successCallback,
            (fallbackError) => {
              console.warn('Low accuracy location failed. Retrying with IP Geolocation...', fallbackError.message);
              
              fetch('https://ipwho.is/')
                .then(response => response.json())
                .then(data => {
                  if (data.success && data.latitude && data.longitude) {
                    this.formData.latitude = Number(data.latitude).toFixed(6);
                    this.formData.longitude = Number(data.longitude).toFixed(6);
                    
                    if (data.region && !this.formData.governorate) {
                      this.formData.governorate = data.region;
                    }
                    
                    this.locationSuccess = true;
                    this.locationError = '';
                    if (button) {
                      button.disabled = false;
                      button.innerHTML = '✓ تم تحديد الموقع بنجاح';
                      setTimeout(() => {
                        button.innerHTML = originalHTML;
                      }, 2000);
                    }
                  } else {
                    throw new Error('Invalid IP location data');
                  }
                })
                .catch(ipError => {
                  console.error('IP Geolocation failed:', ipError);
                  if (button) {
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                  }
                  
                  switch(fallbackError.code) {
                    case fallbackError.PERMISSION_DENIED:
                      this.locationError = 'تم رفض الإذن بالوصول إلى الموقع. الرجاء السماح للمتصفح بالوصول إلى موقعك.';
                      break;
                    case fallbackError.POSITION_UNAVAILABLE:
                      this.locationError = 'معلومات الموقع غير متوفرة.';
                      break;
                    case fallbackError.TIMEOUT:
                      this.locationError = 'انتهت مهلة طلب الموقع.';
                      break;
                    default:
                      this.locationError = 'حدث خطأ غير معروف في تحديد الموقع.';
                  }
                });
            },
            {
              enableHighAccuracy: false,
              timeout: 5000,
              maximumAge: 60000
            }
          );
        },
        {
          enableHighAccuracy: true,
          timeout: 4000,
          maximumAge: 0
        }
      );
    }
  };
}

// Watch file input changes to show selected count
document.getElementById('newImages').addEventListener('change', function(e) {
  const count = e.target.files.length;
  const label = document.getElementById('uploadLabel');
  if (count > 0) {
    label.textContent = `تم اختيار ${count} صور جديدة للرفع`;
    label.classList.add('text-[#6A8F3B]');
  } else {
    label.textContent = 'اختر صورًا جديدة لتحميلها';
    label.classList.remove('text-[#6A8F3B]');
  }
});

document.getElementById('listingForm').addEventListener('submit', function(e) {
  const btn = this.querySelector('button[type="submit"]');
  if (btn) {
    btn.disabled = true;
    btn.textContent = 'جاري الحفظ...';
  }
});
</script>
@endsection
