@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-xl">
  <div class="bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl shadow-xl p-6 lg:p-8 space-y-8">
    <h2 class="text-2xl font-extrabold text-[#1B2A1B]">إنشاء عرض جديد</h2>
    <form id="listingForm" method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data" onsubmit="return handleListingSubmit(event)" class="space-y-8">
      @csrf
      <input type="hidden" name="seller_id" value="{{ auth()->id() ?? 1 }}">
      <!-- تنبيه الأخطاء -->
      @if ($errors->any())
        <div id="listingErrors" class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl p-3" role="alert" aria-live="assertive">
          <ul class="list-disc ps-5">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @else
        <div id="listingErrors" class="hidden text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl p-3" role="alert" aria-live="assertive"></div>
      @endif
      <!-- القسم 1: معلومات أساسية -->
      <div id="formStep1">
        <section class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-5 shadow-sm space-y-5">
          <h3 class="text-[#1B2A1B] font-bold">المعلومات الأساسية</h3>
          <div class="grid md:grid-cols-2 gap-5">
            <div>
              <label for="product_id" class="block text-[#C8A356] font-semibold mb-1">المنتج <span class="text-red-600">*</span></label>
              <select id="product_id" name="product_id" required class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                <option value="">— اختر —</option>
                @foreach($products as $product)
                  <option value="{{ $product->id }}">
                    {{ $product->variety }}
                    @if($product->quality) - {{ $product->quality }} @endif
                    @if($product->type === 'olive_oil') زيت زيتون @elseif($product->type === 'olive') زيتون @endif
                  </option>
                @endforeach
              </select>
              <p class="text-xs text-[#6B7280] mt-1">مثال: زيت زيتون بكر ممتاز / زيتون شملاّلي خام.</p>
            </div>
            <div>
              <label for="variety" class="block text-[#C8A356] font-semibold mb-1">الصنف / النوع</label>
              <select id="variety" name="variety" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                <option value="">— اختر —</option>
                <option value="chemlali">شملاّلي</option>
                <option value="chetoui">شتوي</option>
                <option value="blend">مزيج (Blend)</option>
              </select>
            </div>
            <div id="harvestYearDiv">
              <label for="harvest_year" class="block text-[#C8A356] font-semibold mb-1">سنة الجني</label>
              <input id="harvest_year" name="harvest_year" type="number" min="2000" max="2100" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="2024/2025">
            </div>
          </div>
        </section>
        <section class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-5 shadow-sm space-y-5">
          <h3 class="text-[#1B2A1B] font-bold">الكمية والتسعير والتغليف</h3>
          <div class="grid md:grid-cols-3 gap-5">
            <div>
              <label for="available_qty" class="block text-[#C8A356] font-semibold mb-1">الكمية المتاحة <span class="text-red-600">*</span></label>
              <input id="available_qty" name="available_qty" type="number" step="0.001" min="0" required class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="مثال: 200">
            </div>
            <div>
              <label for="qty_unit" class="block text-[#C8A356] font-semibold mb-1">الوحدة <span class="text-red-600">*</span></label>
              <input id="qty_unit" name="qty_unit" type="text" readonly class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" value="">
            </div>
            <div>
              <label for="min_order" class="block text-[#C8A356] font-semibold mb-1">أدنى كمية للطلب</label>
              <input id="min_order" name="min_order" type="number" step="0.001" min="0" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="مثال: 50">
            </div>
          </div>
          <div class="grid md:grid-cols-3 gap-5">
            <div>
              <label for="price_per_unit" class="block text-[#C8A356] font-semibold mb-1">السعر للوحدة <span class="text-red-600">*</span></label>
              <input id="price_per_unit" name="price_per_unit" type="number" step="0.001" min="0" required class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="مثال: 18.5">
            </div>
            <div>
              <label for="currency" class="block text-[#C8A356] font-semibold mb-1">العملة</label>
              <select id="currency" name="currency" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                <option value="TND" selected>TND</option>
              </select>
            </div>
            <div>
              <label for="quality_grade" class="block text-[#C8A356] font-semibold mb-1">الجودة</label>
              <select id="quality_grade" name="quality_grade" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                <option value="">— اختر —</option>
                <option value="extra_virgin">بكر ممتاز (Extra Virgin)</option>
                <option value="virgin">بكر (Virgin)</option>
                <option value="lampante">صناعي (Lampante)</option>
              </select>
            </div>
          </div>
          <div class="space-y-3">
            <div id="packagingSection">
              <div class="text-[#C8A356] font-semibold">خيارات التغليف</div>
              <div class="flex flex-wrap gap-2 border border-[#C7D1C7] rounded-xl p-3">
                <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border bg-white shadow-sm">
                  <input type="checkbox" name="packaging[]" value="bulk"> Bulk / صهريج
                </label>
                <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border bg-white shadow-sm">
                  <input type="checkbox" name="packaging[]" value="1l_tin"> 1L Tin
                </label>
                <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border bg-white shadow-sm">
                  <input type="checkbox" name="packaging[]" value="3l_tin"> 3L Tin
                </label>
                <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border bg-white shadow-sm">
                  <input type="checkbox" name="packaging[]" value="5l_tin"> 5L Tin
                </label>
                <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border bg-white shadow-sm">
                  <input type="checkbox" name="packaging[]" value="20l"> 20L Jerrycan
                </label>
              </div>
              <p class="text-xs text-[#6B7280]">اختَر كل ما ينطبق. وضّح لاحقاً إن لزم قياسات خاصة.</p>
            </div>
          </div>
        </section>
        <div class="flex justify-end gap-3 pt-4">
          <button type="button" onclick="nextStep(2)" class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-[#1B2A1B] to-[#6A8F3B] text-white font-bold shadow-lg hover:shadow-2xl hover:scale-105 transition">التالي</button>
        </div>
      </div>

      <!-- القسم 3: الموقع والشهادات -->
      <section class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-5 shadow-sm space-y-5">
        <h3 class="text-[#1B2A1B] font-bold">المصدر والشهادات</h3>
        <div class="grid md:grid-cols-3 gap-5">
          <div>
            <label for="governorate" class="block text-[#C8A356] font-semibold mb-1">الولاية</label>
            <select id="governorate" name="governorate" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
              <option value="">اختر الولاية</option>
              <!-- Options populated by JS -->
            </select>
          </div>
          <div>
            <label for="origin_city" class="block text-[#C8A356] font-semibold mb-1">المدينة / المعتمدية</label>
            <select id="origin_city" name="origin_city" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
              <option value="">اختر المدينة</option>
            </select>
          </div>
          <div>
            <label for="farm_name" class="block text-[#C8A356] font-semibold mb-1">اسم المزرعة (اختياري)</label>
            <input id="farm_name" name="farm_name" type="text" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="مثال: ضيعة السالمي">
          </div>
          <div id="aciditySection">
            <label for="acidity" class="block text-[#C8A356] font-semibold mb-1">الحموضة % (إن وجد)</label>
            <input id="acidity" name="acidity" type="number" step="0.01" min="0" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="≤ 0.8 للأكسترا فيرجن">
          </div>
        </div>
        <div>
          <div id="certsSection">
            <div class="text-[#C8A356] font-semibold mb-2">الشهادات</div>
            <div class="flex flex-wrap gap-2 border border-[#C7D1C7] rounded-xl p-3">
              <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border bg-white shadow-sm">
                <input type="checkbox" name="certs[]" value="bio"> Bio / عضوي
              </label>
              <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border bg-white shadow-sm">
                <input type="checkbox" name="certs[]" value="iso22000"> ISO 22000
              </label>
              <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border bg-white shadow-sm">
                <input type="checkbox" name="certs[]" value="halal"> حلال
              </label>
              <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border bg-white shadow-sm">
                <input type="checkbox" name="certs[]" value="kosher"> Kosher
              </label>
            </div>
          </div>
        </div>
      </section>
        <div id="formStep2" class="hidden">
          <section class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-5 shadow-sm space-y-5">
            <h3 class="text-[#1B2A1B] font-bold">المصدر والشهادات</h3>
            <div class="grid md:grid-cols-3 gap-5">
              <div>
                <label for="origin_city" class="block text-[#C8A356] font-semibold mb-1">المدينة / المعتمدية</label>
                <input id="origin_city" name="origin_city" type="text" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="سوسة / القيروان / صفاقس...">
                <!-- Latitude/Longitude will be set automatically on submit using map location -->
              </div>
              <div>
                <label for="farm_name" class="block text-[#C8A356] font-semibold mb-1">اسم المزرعة (اختياري)</label>
                <input id="farm_name" name="farm_name" type="text" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="مثال: ضيعة السالمي">
              </div>
              <div>
                <label for="acidity" class="block text-[#C8A356] font-semibold mb-1">الحموضة % (إن وجد)</label>
                <input id="acidity" name="acidity" type="number" step="0.01" min="0" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="≤ 0.8 للأكسترا فيرجن">
              </div>
            </div>
            <div>
              <div class="text-[#C8A356] font-semibold mb-2">الشهادات</div>
              <!-- Certifications removed as requested -->
          </section>
          <section class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-5 shadow-sm space-y-5">
            <h3 class="text-[#1B2A1B] font-bold">التوصيل والدفع</h3>
            ...existing code for section 4...
          </section>
          <div class="flex justify-between gap-3 pt-4">
            <button type="button" onclick="prevStep(1)" class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-[#9B6A4A] to-[#F8F4EC] text-gray-900 shadow-lg hover:shadow-2xl hover:scale-105 transition">السابق</button>
            <button type="button" onclick="nextStep(3)" class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-[#1B2A1B] to-[#6A8F3B] text-white font-bold shadow-lg hover:shadow-2xl hover:scale-105 transition">التالي</button>
          </div>
        </div>

      <!-- القسم 4: التوصيل والدفع -->
      <section class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-5 shadow-sm space-y-5">
        <h3 class="text-[#1B2A1B] font-bold">التوصيل والدفع</h3>
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <div class="text-[#C8A356] font-semibold mb-2">طرق الدفع</div>
            <div class="space-y-2 border border-[#C7D1C7] rounded-xl p-3">
              <label class="flex items-center gap-2"><input type="checkbox" name="payment_methods[]" value="cod"> دفع عند التسليم (COD)</label>
              <label class="flex items-center gap-2"><input type="checkbox" name="payment_methods[]" value="flouci"> Flouci</label>
              <label class="flex items-center gap-2"><input type="checkbox" name="payment_methods[]" value="d17"> D17</label>
              <label class="flex items-center gap-2"><input type="checkbox" name="payment_methods[]" value="stripe"> بطاقة بنكية</label>
              <label class="flex items-center gap-2"><input type="checkbox" name="payment_methods[]" value="bank_lc"> اعتماد مستندي (LC)</label>
            </div>
          </div>
          <div>
            <div class="text-[#C8A356] font-semibold mb-2">خيارات التوصيل</div>
            <div class="space-y-2 border border-[#C7D1C7] rounded-xl p-3">
              <label class="flex items-center gap-2"><input type="checkbox" name="delivery_options[]" value="pickup" onchange="toggleExport(false)"> استلام من المصدر</label>
              <label class="flex items-center gap-2"><input type="checkbox" name="delivery_options[]" value="carrier" onchange="toggleExport(false)"> عبر ناقل</label>
              <label class="flex items-center gap-2"><input type="checkbox" name="delivery_options[]" value="export" onchange="toggleExport(this.checked)"> للتصدير (Export)</label>
            </div>
          </div>
        </div>
        <!-- حقول التصدير الديناميكية -->
        <div id="exportFields" class="hidden space-y-4 border border-[#C7D1C7] rounded-2xl p-4 bg-white">
          <div class="grid md:grid-cols-3 gap-4">
            <div>
              <label for="incoterm" class="block text-[#C8A356] font-semibold mb-1">Incoterm <span class="text-red-600">*</span></label>
              <select id="incoterm" name="incoterm" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                <option value="">— اختر —</option>
                <option value="EXW">EXW</option>
                <option value="FOB">FOB</option>
                <option value="CIF">CIF</option>
              </select>
            </div>
            <div>
              <label for="export_port" class="block text-[#C8A356] font-semibold mb-1">ميناء الشحن</label>
              <input id="export_port" name="export_port" type="text" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="رادس / صفاقس...">
            </div>
            <div>
              <label for="payment_term_export" class="block text-[#C8A356] font-semibold mb-1">شرط الدفع للتصدير</label>
              <select id="payment_term_export" name="payment_term_export" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                <option value="">— اختر —</option>
                <option value="LC">LC</option>
                <option value="TT">TT</option>
                <option value="ESCROW">Escrow</option>
              </select>
            </div>
          </div>
          <p class="text-xs text-[#6B7280]">ملاحظة: اختر Incoterm المناسب حسب مسؤوليات الشحن والتأمين.</p>
        </div>
        <!-- نافذة التسليم -->
        <div class="grid md:grid-cols-2 gap-5">
          <!-- Delivery window removed as requested -->
        </div>
      </section>

      <!-- القسم 5: الصور والتفاصيل -->
      <section class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-5 shadow-sm space-y-5">
        <h3 class="text-[#1B2A1B] font-bold">الصور والتفاصيل</h3>
        <div class="grid md:grid-cols-2 gap-5">
          <div>
            <label for="photos" class="block text-[#C8A356] font-semibold mb-1">صور المنتج (حتى 4)</label>
            <input id="photos" name="photos[]" type="file" accept="image/*" multiple class="w-full rounded-xl border border-dashed border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
            <p class="text-xs text-[#6B7280] mt-1">حمّل صور واضحة للتعبئة / اللون / اللزوجة.</p>
          </div>
          <div>
            <label for="notes" class="block text-[#C8A356] font-semibold mb-1">تفاصيل إضافية</label>
            <textarea id="notes" name="notes" rows="4" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="اذكر أي مواصفات أو شروط خاصة..."></textarea>
          </div>
        </div>
      </section>
        <div id="formStep3" class="hidden">
          <section class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-5 shadow-sm space-y-5">
            <h3 class="text-[#1B2A1B] font-bold">الصور والتفاصيل</h3>
            <div class="grid md:grid-cols-2 gap-5">
              <div>
                <label for="photos" class="block text-[#C8A356] font-semibold mb-1">صور المنتج (حتى 4)</label>
                <input id="photos" name="photos[]" type="file" accept="image/*" multiple class="w-full rounded-xl border border-dashed border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                <p class="text-xs text-[#6B7280] mt-1">حمّل صور واضحة للتعبئة / اللون / اللزوجة.</p>
              </div>
              <div>
                <label for="notes" class="block text-[#C8A356] font-semibold mb-1">تفاصيل إضافية</label>
                <textarea id="notes" name="notes" rows="4" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="اذكر أي مواصفات أو شروط خاصة..."></textarea>
              </div>
            </div>
          </section>
          <section class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-5 shadow-sm space-y-5">
            <h3 class="text-[#1B2A1B] font-bold">التواصل والموافقة</h3>
            <div class="grid md:grid-cols-3 gap-5">
              <div>
                <label for="contact_phone" class="block text-[#C8A356] font-semibold mb-1">رقم الهاتف <span class="text-red-600">*</span></label>
                <input id="contact_phone" name="contact_phone" type="tel" required class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="+216 25 777 926">
              </div>
              <div>
                <label for="contact_whatsapp" class="block text-[#C8A356] font-semibold mb-1">واتساب</label>
                <input id="contact_whatsapp" name="contact_whatsapp" type="tel" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="+216 …">
              </div>
              <div>
                <label for="contact_email" class="block text-[#C8A356] font-semibold mb-1">البريد الإلكتروني</label>
                <input id="contact_email" name="contact_email" type="email" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="example@email.com">
              </div>
            </div>
            <label class="flex items-start gap-3">
              <input type="checkbox" id="agree" name="agree" class="mt-1">
              <span class="text-sm text-[#1B2A1B]">أوافق على <a href="/terms" class="underline text-[#6A8F3B]">الشروط والأحكام</a> وسياسة الجودة.</span>
            </label>
          </section>
          <div class="flex justify-between gap-3 pt-4">
            <button type="button" onclick="prevStep(2)" class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-[#9B6A4A] to-[#F8F4EC] text-gray-900 shadow-lg hover:shadow-2xl hover:scale-105 transition">السابق</button>
            <button type="submit" onclick="return handleListingSubmit(event)" class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-[#1B2A1B] to-[#6A8F3B] text-white font-bold shadow-lg hover:shadow-2xl hover:scale-105 transition focus:ring-2 focus:ring-[#C8A356]">حفظ</button>
          </div>
        </div>

      <!-- القسم 6: التواصل والموافقة -->
      <section class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-5 shadow-sm space-y-5">
        <h3 class="text-[#1B2A1B] font-bold">التواصل والموافقة</h3>
        <div class="grid md:grid-cols-3 gap-5">
          <div>
            <label for="contact_phone" class="block text-[#C8A356] font-semibold mb-1">رقم الهاتف <span class="text-red-600">*</span></label>
            <input id="contact_phone" name="contact_phone" type="tel" required class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="+216 25 777 926">
          </div>
          <div>
            <label for="contact_whatsapp" class="block text-[#C8A356] font-semibold mb-1">واتساب</label>
            <input id="contact_whatsapp" name="contact_whatsapp" type="tel" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="+216 …">
          </div>
          <div>
            <label for="contact_email" class="block text-[#C8A356] font-semibold mb-1">البريد الإلكتروني</label>
            <input id="contact_email" name="contact_email" type="email" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]" placeholder="example@email.com">
          </div>
        </div>
        <label class="flex items-start gap-3">
          <input type="checkbox" id="agree" name="agree" class="mt-1">
          <span class="text-sm text-[#1B2A1B]">أوافق على <a href="/terms" class="underline text-[#6A8F3B]">الشروط والأحكام</a> وسياسة الجودة.</span>
        </label>
      </section>
    </form>
      <!-- أزرار -->
      <!-- Step navigation will be added in next patch -->
    </form>
    <script>
    // Multi-step form navigation
    function nextStep(step) {
      // Hide all step divs
      document.getElementById('formStep1')?.classList.add('hidden');
      document.getElementById('formStep2')?.classList.add('hidden');
      document.getElementById('formStep3')?.classList.add('hidden');
      // Show requested step
      document.getElementById('formStep' + step)?.classList.remove('hidden');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    function prevStep(step) {
      // Hide all step divs
      document.getElementById('formStep1')?.classList.add('hidden');
      document.getElementById('formStep2')?.classList.add('hidden');
      document.getElementById('formStep3')?.classList.add('hidden');
      // Show requested step
      document.getElementById('formStep' + step)?.classList.remove('hidden');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
      function updateProductFields() {
        const productSelect = document.getElementById('product_id');
        const selectedValue = productSelect.value;
        // Hide harvest year if olive
        document.getElementById('harvestYearDiv').style.display = selectedValue === 'olive' ? 'none' : '';
        // Set unit
        let unit = '';
        if (selectedValue === 'olive_oil') {
          unit = 'لتر';
        } else if (selectedValue === 'olive') {
          unit = 'كغ';
        }
        document.getElementById('qty_unit').value = unit;
        // Show packaging section only for olive oil
        const packagingSection = document.getElementById('packagingSection');
        if (packagingSection) {
          packagingSection.style.display = selectedValue === 'olive_oil' ? '' : 'none';
        }
        // Show acidity and certs only for olive oil
        const aciditySection = document.getElementById('aciditySection');
        if (aciditySection) {
          aciditySection.style.display = selectedValue === 'olive_oil' ? '' : 'none';
        }
        const certsSection = document.getElementById('certsSection');
        if (certsSection) {
          certsSection.style.display = selectedValue === 'olive_oil' ? '' : 'none';
        }
      }
      document.getElementById('product_id').addEventListener('change', updateProductFields);
      // Run on page load
      updateProductFields();

      // Currency auto-conversion for foreign users (placeholder logic)
      // You can enhance this with actual geolocation/IP detection
      if (navigator.language && !navigator.language.startsWith('ar') && !navigator.language.startsWith('fr')) {
        document.getElementById('currency').value = 'USD';
      }

      // Latitude/Longitude will be set automatically on submit (placeholder)
      function handleListingSubmit(e) {
        // ...existing code...
        // Example: set lat/lng from map API
        // form.latitude.value = mapLat;
        // form.longitude.value = mapLng;
        // ...existing code...
      }
      function toggleExport(checked) {
        const box = document.getElementById('exportFields');
        box.classList.toggle('hidden', !checked);
        document.getElementById('incoterm').required = !!checked;
      }

      // Tunisian governorates and major olive-producing cities
      const govCities = {
        "Sfax": ["Sfax", "El Amra", "Mahrès", "Agareb", "Jebiniana"],
        "Sousse": ["Sousse", "Msaken", "Kalaa Kebira", "Kalaa Sghira"],
        "Kairouan": ["Kairouan", "Bouhajla", "Chebika", "Sbikha"],
        "Mahdia": ["Mahdia", "El Jem", "Bou Merdes"],
        "Zaghouan": ["Zaghouan", "El Fahs", "Zriba"],
        "Monastir": ["Monastir", "Jemmal", "Beni Hassen"],
        "Gabes": ["Gabes", "Mareth", "El Hamma"],
        "Beja": ["Beja", "Testour", "Teboursouk"],
        "Nabeul": ["Nabeul", "Grombalia", "Menzel Temime"],
        "Siliana": ["Siliana", "Bou Arada", "El Krib"],
        "Kasserine": ["Kasserine", "Foussana", "Sbeitla"],
        "Bizerte": ["Bizerte", "Mateur", "Ras Jebel"],
        "Medenine": ["Medenine", "Ben Gardane", "Zarzis"],
        "Tunis": ["Tunis", "La Marsa", "Carthage"],
        "Ariana": ["Ariana", "Soukra", "Raoued"],
        "Ben Arous": ["Ben Arous", "Mégrine", "Hammam Lif"],
        "Manouba": ["Manouba", "Oued Ellil", "Douar Hicher"]
      };
      document.addEventListener('DOMContentLoaded', function() {
        const govSelect = document.getElementById('governorate');
        if (govSelect) {
          govSelect.innerHTML = '<option value="">اختر الولاية</option>' + Object.keys(govCities).map(gov => `<option value="${gov}">${gov}</option>`).join('');
          govSelect.addEventListener('change', function() {
            const citySelect = document.getElementById('origin_city');
            if (citySelect) {
              const cities = govCities[this.value] || [];
              citySelect.innerHTML = '<option value="">اختر المدينة</option>' + cities.map(c => `<option value="${c}">${c}</option>`).join('');
            }
          });
        }
      });

      function handleListingSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const errors = [];

        // Required basics
        if (!form.product_id.value) errors.push('الرجاء اختيار المنتج.');
        if (!form.available_qty.value || Number(form.available_qty.value) <= 0) errors.push('الرجاء تحديد الكمية المتاحة (> 0).');
        if (!form.price_per_unit.value || Number(form.price_per_unit.value) <= 0) errors.push('الرجاء تحديد السعر للوحدة (> 0).');
        if (!form.contact_phone.value) errors.push('الرجاء إدخال رقم الهاتف.');
        if (!form.agree.checked) errors.push('يجب الموافقة على الشروط والأحكام.');

        // Export constraints
        const exportChecked = [...form.querySelectorAll('input[name="delivery_options[]"]')]
          .some(el => el.checked && el.value === 'export');
        if (exportChecked && !form.incoterm.value) errors.push('للتصدير: الرجاء اختيار Incoterm.');

        // Delivery window validation
        if (form.delivery_start.value && form.delivery_end.value) {
          if (new Date(form.delivery_start.value) > new Date(form.delivery_end.value)) {
            errors.push('نافذة التسليم: تاريخ البداية يجب أن يسبق تاريخ النهاية.');
          }
        }

        const errBox = document.getElementById('listingErrors');
        if (errors.length) {
          errBox.innerHTML = '<ul class="list-disc ps-5">' + errors.map(e => `<li>${e}</li>`).join('') + '</ul>';
          errBox.classList.remove('hidden');
          window.scrollTo({ top: errBox.offsetTop - 80, behavior: 'smooth' });
          return false;
        } else {
          errBox.classList.add('hidden');
        }

        // TODO: استبدل بـ fetch('/listings', { method:'POST', body: FormData }) أو submit عادي
        form.submit();
        return true;
      }
    </script>
  </div>
</div>
@endsection
