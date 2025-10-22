<?php $__env->startSection('title', 'أسعار اليوم'); ?>
<?php $__env->startSection('content'); ?>


<div class="max-w-6xl mx-auto p-6 space-y-8">

  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">أسعار اليوم</h1>
    <?php if(!empty($marketTrend)): ?>
      <span class="text-sm px-3 py-1 rounded-full
        <?php if($marketTrend === __('Rising')): ?> bg-green-100 text-green-700
        <?php elseif($marketTrend === __('Falling')): ?> bg-red-100 text-red-700
        <?php else: ?> bg-gray-100 text-gray-700 <?php endif; ?>">
        <?php echo e($marketTrend); ?>

      </span>
    <?php endif; ?>
  </div>

  <!-- KPIs -->
  <div class="prices-kpi grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="card p-5">
      <div class="text-xs text-gray-500 mb-2">متوسط تونس (آخر 7 أيام)</div>
      <div class="flex flex-wrap gap-2">
        <span class="px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">زيت: <?php echo e(isset($tunisianAvg) ? number_format((float)$tunisianAvg, 2) : '—'); ?> TND</span>
        <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold">زيتون: <?php echo e(isset($tunisianOliveAvg) ? number_format((float)$tunisianOliveAvg, 2) : '—'); ?> TND</span>
      </div>
    </div>
    <div class="card p-5">
      <div class="text-xs text-gray-500">متوسط عالمي (آخر 7 أيام)</div>
      <div class="kpi mt-1"><?php echo e(isset($worldAvg) ? number_format((float)$worldAvg, 2) : '—'); ?> EUR/kg</div>
    </div>
  </div>

  <!-- Latest Tunisian Souk Prices as cards -->
  <section class="space-y-3">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-bold">أسعار الأسواق التونسية (آخر إدخالات)</h2>
      <a href="<?php echo e(route('prices.souks')); ?>" class="text-sm text-[#6A8F3B] hover:underline">عرض الكل</a>
    </div>

    <?php $souks = $soukPrices ?? collect(); ?>
    <?php if($souks->count()): ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php $__currentLoopData = $souks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $souk     = $row->souk_name ?? $row->souk ?? $row->market ?? $row->location ?? $row->city ?? '—';
            $product  = $row->product_type ?? ($row->variety ? 'olive' : 'oil'); // fallback
            $variety  = $row->variety ?? '';
            $quality  = $row->quality ?? '';
            $avg      = $row->price_avg ?? $row->price ?? null;
            $price    = isset($avg) ? number_format((float)$avg, 2) : '—';
            $currency = $row->currency ?? 'TND';
            $unit     = $row->unit ?? 'kg';
            $dateRaw  = $row->date ?? $row->priced_at ?? $row->created_at ?? null;
            try { $date = \Carbon\Carbon::parse($dateRaw)->format('Y-m-d'); } catch (\Throwable $e) { $date = ''; }
            $isOil    = strtolower((string)$product) === 'oil';
            $headBg   = $isOil ? '#C8A356' : '#6A8F3B';
            $icon     = $isOil ? '<img src="'.asset('images/olive-oil.png').'" alt="Oil" class="w-4 h-4 object-contain">' : '🫒';
            $productLabel = $isOil ? 'زيت' : 'زيتون';
          ?>

          <div class="souk-card overflow-hidden">
            <div class="souk-head text-white" style="background: <?php echo e($headBg); ?>">
              <div class="font-bold flex items-center gap-2">
                <span class="text-lg"><?php echo $icon; ?></span>
                <span><?php echo e($souk); ?></span>
                <?php if($variety): ?>
                  <span class="pill bg-white/20"><?php echo e($variety); ?></span>
                <?php endif; ?>
              </div>
              <span class="pill bg-white/20"><?php echo e($date); ?></span>
            </div>

            <div class="souk-body space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">المنتج</span>
                <span class="font-semibold"><?php echo e($productLabel); ?></span>
              </div>
              <?php if($quality): ?>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">الجودة</span>
                  <span class="font-semibold"><?php echo e($quality); ?></span>
                </div>
              <?php endif; ?>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">المعدل</span>
                <span class="text-lg font-extrabold"><?php echo e($price); ?> <span class="text-sm font-bold"><?php echo e($currency); ?>/<?php echo e($unit); ?></span></span>
              </div>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php else: ?>
      <p class="text-gray-600">لا توجد بيانات أسواق حاليا.</p>
    <?php endif; ?>
  </section>

  <!-- Latest World Prices as simple cards -->
  <section class="space-y-3">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-bold">أسعار عالمية (آخر إدخالات)</h2>
      <a href="<?php echo e(route('prices.world')); ?>" class="text-sm text-[#6A8F3B] hover:underline">عرض الكل</a>
    </div>

    <?php $world = $worldPrices ?? collect(); ?>
    <?php if($world->count()): ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php $__currentLoopData = $world; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $country  = $row->country ?? '—';
            $variety  = $row->variety ?? '';
            $quality  = $row->quality ?? '';
            $price    = isset($row->price) ? number_format((float)$row->price, 2) : '—';
            $dateRaw  = $row->date ?? $row->created_at ?? null;
            try { $date = \Carbon\Carbon::parse($dateRaw)->format('Y-m-d'); } catch (\Throwable $e) { $date = ''; }
          ?>

          <div class="souk-card overflow-hidden">
            <div class="souk-head text-white" style="background:#3b82f6">
              <div class="font-bold flex items-center gap-2">
                <span class="text-lg">🌍</span>
                <span><?php echo e($country); ?></span>
                <?php if($variety): ?>
                  <span class="pill bg-white/20"><?php echo e($variety); ?></span>
                <?php endif; ?>
              </div>
              <span class="pill bg-white/20"><?php echo e($date); ?></span>
            </div>
            <div class="souk-body space-y-2">
              <?php if($quality): ?>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">الجودة</span>
                  <span class="font-semibold"><?php echo e($quality); ?></span>
                </div>
              <?php endif; ?>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">السعر</span>
                <span class="text-lg font-extrabold"><?php echo e($price); ?> <span class="text-sm font-bold">EUR/kg</span></span>
              </div>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php else: ?>
      <p class="text-gray-600">لا توجد بيانات عالمية حاليا.</p>
    <?php endif; ?>
  </section>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u346640129/laravel_app/resources/views/prices/index.blade.php ENDPATH**/ ?>