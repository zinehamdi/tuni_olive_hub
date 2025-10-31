<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\Product;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\ImageOptimizationService;

/**
 * Listing Controller - متحكم العروض
 * 
 * Manages product listings in the marketplace
 * يدير عروض المنتجات في السوق
 * 
 * @package App\Http\Controllers
 */
class ListingController extends Controller
{
    /**
     * Show the listing creation wizard form
     * عرض نموذج معالج إنشاء العرض
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Use the wizard form (no products needed - using dropdown)
        // استخدام نموذج المعالج (لا حاجة للمنتجات - نستخدم القائمة المنسدلة)
        return view('listings.wizard');
    }

    /**
     * Store a new listing in the database
     * حفظ عرض جديد في قاعدة البيانات
     * 
     * Validates and creates a new product listing with images,
     * location data, and seller information
     * يتحقق من صحة البيانات وينشئ عرض منتج جديد مع الصور
     * والموقع الجغرافي ومعلومات البائع
     * 
     * @param  \Illuminate\Http\Request  $request  البيانات المرسلة من النموذج
     * @return \Illuminate\Http\RedirectResponse  إعادة توجيه إلى لوحة التحكم
     * @throws \Illuminate\Validation\ValidationException  عند وجود خطأ في التحقق
     */
    public function store(Request $request)
    {
        // Log incoming request for debugging
        Log::info('Listing Store Request:', [
            'user_id' => Auth::id(),
            'category' => $request->category,
            'variety' => $request->variety,
            'quality' => $request->quality,
            'has_price' => $request->has('price'),
            'timestamp' => now()->toDateTimeString()
        ]);
        
        try {
        Log::error('🧩 Debug Upload:', [
            'hasFile_images' => $request->hasFile('images'),
            'input_images_type' => gettype($request->input('images')),
            'allFiles' => array_keys($request->allFiles()),
            'image_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
        ]);
            // Validate the request
            $validated = $request->validate([
                'category' => 'required|in:olive,oil',
                'variety' => 'required|string|max:64',
                'quality' => 'nullable|string|max:64',
                'seller_id' => 'nullable|exists:users,id',
                'price' => 'required|numeric|min:0',
                'currency' => 'nullable|string|max:8',
                'quantity' => 'required|numeric|min:0.001',
                'unit' => 'nullable|string|max:16',
                'min_order' => 'nullable|numeric|min:0',
                'status' => 'nullable|string',
                'payment_methods' => 'nullable', // Can be array or JSON string
                'delivery_options' => 'nullable', // Can be array or JSON string
                'location_text' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'governorate' => 'nullable|string',
                'delegation' => 'nullable|string',
                'images.*' => 'nullable|mimetypes:image/jpeg,image/png,image/webp,image/avif,image/heic,image/heif|mimes:jpeg,jpg,png,webp,avif,heic,heif|max:8192', // Accept any image format/size, will be optimized
            ]);

            // Set seller_id to authenticated user if not provided
            $validated['seller_id'] = $validated['seller_id'] ?? Auth::id();
            
            // Find or create product based on variety and category
            $product = Product::firstOrCreate(
                [
                    'variety' => $validated['variety'],
                    'type' => $validated['category'],
                    'seller_id' => $validated['seller_id']
                ],
                [
                    'quality' => $validated['quality'] ?? null,
                    'price' => $validated['price'],
                    'stock' => $validated['quantity'],
                    'description' => $validated['variety'] . ' - ' . ($validated['category'] === 'olive' ? __('Olives') : __('Olive Oil'))
                ]
            );
            
            Log::info('Product Found/Created:', [
                'product_id' => $product->id,
                'variety' => $product->variety,
                'type' => $product->type
            ]);
            
            // Set status to active by default
            $validated['status'] = $validated['status'] ?? 'active';
            
            // Add product_id to validated data
            $validated['product_id'] = $product->id;
            
            // Remove variety, quality, and category from listing data (stored in product)
            unset($validated['variety'], $validated['quality'], $validated['category']);
            
            // Handle JSON strings for arrays
            if (isset($validated['payment_methods']) && is_string($validated['payment_methods'])) {
                $validated['payment_methods'] = json_decode($validated['payment_methods'], true);
            }
            if (isset($validated['delivery_options']) && is_string($validated['delivery_options'])) {
                $validated['delivery_options'] = json_decode($validated['delivery_options'], true);
            }

            // Create or update the seller's address if location data provided
            if ($request->has('latitude') && $request->has('longitude')) {
                $user = Auth::user();
                
                // Check if user already has an address, otherwise create one
                $address = $user->addresses()->first();
                
                if ($address) {
                    // Update existing address
                    $address->update([
                        'lat' => $request->latitude,
                        'lng' => $request->longitude,
                        'governorate' => $request->governorate,
                        'delegation' => $request->delegation,
                        'label' => $request->location_text ?? 'موقع المنتج',
                    ]);
                    Log::info('Address Updated:', ['address_id' => $address->id]);
                } else {
                    // Create new address
                    $address = $user->addresses()->create([
                        'lat' => $request->latitude,
                        'lng' => $request->longitude,
                        'governorate' => $request->governorate,
                        'delegation' => $request->delegation,
                        'label' => $request->location_text ?? 'موقع المنتج',
                    ]);
                    Log::info('Address Created:', ['address_id' => $address->id]);
                }
            }

            // Remove location fields from listing data (they're stored in addresses table)
            unset($validated['location_text'], $validated['latitude'], $validated['longitude'], 
                  $validated['governorate'], $validated['delegation']);

            // Create the listing
            $listing = Listing::create($validated);
            
            // Handle image uploads with optimization
            if ($request->hasFile('images')) {
                $imageOptimizer = new ImageOptimizationService();
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    // Optimize and resize image to WebP format (max 1200px, 85% quality)
                    $path = $imageOptimizer->optimizeListingImage($image, (string)$listing->id);
                    $imagePaths[] = $path;
                }
                // Save optimized image paths to the listing
                $listing->update(['media' => $imagePaths]);
                Log::info('Images Optimized and Saved:', ['paths' => $imagePaths, 'listing_id' => $listing->id]);
            }
            
            Log::info('✅ Listing Created Successfully:', [
                'id' => $listing->id,
                'product_id' => $listing->product_id,
                'seller_id' => $listing->seller_id,
                'price' => $listing->price,
                'quantity' => $listing->quantity,
                'unit' => $listing->unit,
                'status' => $listing->status
            ]);

            // Redirect to dashboard with success message
            return Redirect::route('dashboard')->with('success', 'تم نشر العرض بنجاح! 🎉');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation Error:', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            return Redirect::back()->withErrors($e->errors())->withInput()->with('error', 'الرجاء التأكد من ملء جميع الحقول المطلوبة');
            
        } catch (\Exception $e) {
            Log::error('❌ Listing Creation Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id()
            ]);
            return Redirect::back()->withInput()->with('error', 'حدث خطأ أثناء نشر العرض. الرجاء المحاولة مرة أخرى.');
        }
    }

    /**
     * Display the specified listing
     * عرض تفاصيل العرض المحدد
     * 
     * @param  \App\Models\Listing  $listing  العرض المطلوب عرضه
     * @return \Illuminate\View\View
     */
    public function show(Listing $listing)
    {
        // Load relationships
        // تحميل العلاقات
        $listing->load(['product', 'seller']);
        
        return view('listings.show', compact('listing'));
    }

    /**
     * Show the form for editing the specified listing
     * عرض نموذج تعديل العرض المحدد
     * 
     * @param  \App\Models\Listing  $listing  العرض المراد تعديله
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException  إذا لم يكن المستخدم مالك العرض
     */
    public function edit(Listing $listing)
    {
        // Check if user owns this listing
        // التحقق من أن المستخدم يملك هذا العرض
        if ($listing->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $products = Product::latest()->get(['id', 'variety', 'quality', 'price']);
        
        return view('listings.edit', compact('listing', 'products'));
    }

    /**
     * Update the specified listing in storage
     * تحديث العرض المحدد في قاعدة البيانات
     * 
     * @param  \Illuminate\Http\Request  $request  البيانات المحدثة
     * @param  \App\Models\Listing  $listing  العرض المراد تحديثه
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException  إذا لم يكن المستخدم مالك العرض
     */
    public function update(Request $request, Listing $listing)
    {
        // Check if user owns this listing
        // التحقق من أن المستخدم يملك هذا العرض
        if ($listing->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the request
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'min_order' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'payment_methods' => 'nullable|array',
            'delivery_options' => 'nullable|array',
        ]);

        // Update the listing
        $listing->update($validated);

        // Redirect to dashboard with success message
        return Redirect::route('dashboard')->with('success', 'تم تحديث العرض بنجاح!');
    }

    /**
     * Remove the specified listing from storage
     * حذف العرض المحدد من قاعدة البيانات
     * 
     * @param  \App\Models\Listing  $listing  العرض المراد حذفه
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException  إذا لم يكن المستخدم مالك العرض
     */
    public function destroy(Listing $listing)
    {
        // Check if user owns this listing
        // التحقق من أن المستخدم يملك هذا العرض
        if ($listing->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $listing->delete();

        return Redirect::route('dashboard')->with('success', 'تم حذف العرض بنجاح!');
    }
}
