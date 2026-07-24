<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\Product;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Mail\NewListingNotificationMail;
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
                'packaging' => 'nullable|string|max:64',
                'seller_id' => 'nullable|exists:users,id',
                'price' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|max:8',
                'quantity' => 'required|numeric|min:0.001',
                'unit' => 'nullable|string|max:16',
                'min_order' => 'nullable|numeric|min:0|lte:quantity',
                'status' => 'nullable|string',
                'payment_methods' => 'nullable', // Can be array or JSON string
                'delivery_options' => 'nullable', // Can be array or JSON string
                'location_text' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'governorate' => 'nullable|string',
                'delegation' => 'nullable|string',
                'estimated_oil_yield' => 'nullable|numeric|min:0|max:100',
                'images' => 'required|array|min:1',
                'images.*' => 'required|mimetypes:image/jpeg,image/png,image/webp,image/avif,image/heic,image/heif|mimes:jpeg,jpg,png,webp,avif,heic,heif|max:51200', // Accept any image format/size, will be optimized
            ], [
                'min_order.lte' => app()->getLocale() === 'ar' 
                    ? 'أدنى كمية للطلب لا يمكن أن تكون أكبر من الكمية الإجمالية للمنتج.' 
                    : (app()->getLocale() === 'fr' ? 'La commande minimum ne peut pas être supérieure à la quantité totale.' : 'Minimum order cannot be greater than the total product quantity.'),
                'images.required' => app()->getLocale() === 'ar' 
                    ? 'يرجى إرفاق صورة واحدة على الأقل لمنتجك عند إضافة العرض.' 
                    : (app()->getLocale() === 'fr' ? 'Veuillez joindre au moins une photo pour votre produit.' : 'Please attach at least one photo for your product.'),
                'images.min' => app()->getLocale() === 'ar' 
                    ? 'يرجى إرفاق صورة واحدة على الأقل لمنتجك عند إضافة العرض.' 
                    : (app()->getLocale() === 'fr' ? 'Veuillez joindre au moins une photo pour votre produit.' : 'Please attach at least one photo for your product.'),
            ]);

            // Set seller_id to authenticated user if not provided
            $validated['seller_id'] = $validated['seller_id'] ?? Auth::id();
            
            // Handle price if null (means upon request)
            $validated['price'] = $validated['price'] ?? 0;
            
            // Find or create product based on variety, category, and quality
            $product = Product::firstOrCreate(
                [
                    'variety' => $validated['variety'],
                    'type' => $validated['category'],
                    'seller_id' => $validated['seller_id'],
                    'quality' => $validated['quality'] ?? null
                ],
                [
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

            // Update user phone number if provided
            if ($request->filled('contact_phone')) {
                $user = Auth::user();
                if ($user->phone !== $request->input('contact_phone')) {
                    $user->phone = $request->input('contact_phone');
                    $user->save();
                }
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

            // Send success email to seller
            try {
                \Illuminate\Support\Facades\Mail::to($listing->seller->email)->send(new \App\Mail\ListingCreatedMail($listing));
            } catch (\Exception $e) {
                Log::error('Failed to send listing creation email: ' . $e->getMessage());
            }

            // Notify admin about new listing so they can review and send to subscribers manually
            try {
                \Illuminate\Support\Facades\Mail::to('zinehamdi8@gmail.com')->send(new NewListingNotificationMail($listing));
                Log::info('Admin notified of new listing.', ['listing_id' => $listing->id]);
            } catch (\Exception $e) {
                Log::error('Failed to send new listing admin notification: ' . $e->getMessage());
            }

            // Redirect to dashboard with success message
            return Redirect::route('dashboard')->with('success', __('Listing published successfully! 🎉'))->with('new_listing_id', $listing->id);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation Error:', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            return Redirect::back()->withErrors($e->errors())->withInput()->with('error', __('Please ensure all required fields are filled.'));
            
        } catch (\Exception $e) {
            Log::error('❌ Listing Creation Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id()
            ]);
            return Redirect::back()->withInput()->with('error', __('An error occurred while publishing the listing. Please try again.'));
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
        $listing->load(['product', 'seller.addresses']);
        
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
        if ($listing->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the request
        $validated = $request->validate([
            'category' => 'required|string|in:oil,olive',
            'variety' => 'required|string|max:255',
            'quality' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|in:kg,ton,liter,bottle',
            'price' => 'required|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0|lte:quantity',
            'status' => 'nullable|string',
            'payment_methods' => 'nullable|array',
            'delivery_options' => 'nullable|array',
            'location_text' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'governorate' => 'nullable|string|max:255',
            'delegation' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|max:10240', // 10MB max per image
        ], [
            'min_order.lte' => app()->getLocale() === 'ar' 
                ? 'أدنى كمية للطلب لا يمكن أن تكون أكبر من الكمية الإجمالية للمنتج.' 
                : (app()->getLocale() === 'fr' ? 'La commande minimum ne peut pas être supérieure à la quantité totale.' : 'Minimum order cannot be greater than the total product quantity.'),
        ]);

        // Find or create the product associated with this listing/seller
        $product = Product::updateOrCreate(
            [
                'variety' => $validated['variety'],
                'type' => $validated['category'],
                'seller_id' => $listing->seller_id,
                'quality' => $validated['quality'] ?? null
            ],
            [
                'price' => $validated['price'],
                'stock' => $validated['quantity'],
                'description' => $validated['variety'] . ' - ' . ($validated['category'] === 'olive' ? __('Olives') : __('Olive Oil'))
            ]
        );

        // Update basic listing fields
        $listingData = [
            'product_id' => $product->id,
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'min_order' => $validated['min_order'] ?? 0,
            'status' => $validated['status'] ?? 'active',
            'payment_methods' => $validated['payment_methods'] ?? [],
            'delivery_options' => $validated['delivery_options'] ?? [],
        ];
        
        $listing->update($listingData);

        // Create or update the seller's address if location data provided
        if ($request->has('latitude') && $request->has('longitude') && $request->latitude && $request->longitude) {
            $user = Auth::user();
            $address = $user->addresses()->first();
            
            if ($address) {
                $address->update([
                    'lat' => $request->latitude,
                    'lng' => $request->longitude,
                    'governorate' => $request->governorate,
                    'delegation' => $request->delegation,
                    'label' => $request->location_text ?? 'موقع المنتج',
                ]);
            } else {
                $user->addresses()->create([
                    'lat' => $request->latitude,
                    'lng' => $request->longitude,
                    'governorate' => $request->governorate,
                    'delegation' => $request->delegation,
                    'label' => $request->location_text ?? 'موقع المنتج',
                ]);
            }
        }

        // Handle media management (retaining existing & adding new)
        $existingMedia = is_array($listing->media) ? $listing->media : [];
        $keepMedia = $request->input('keep_media', null);
        
        $finalMedia = [];
        if (is_array($keepMedia)) {
            $finalMedia = array_values(array_intersect($existingMedia, $keepMedia));
        } elseif ($request->has('keep_media_specified')) {
            $finalMedia = [];
        } else {
            $finalMedia = $request->hasFile('images') ? [] : $existingMedia;
        }

        if ($request->hasFile('images')) {
            $imageOptimizer = new ImageOptimizationService();
            foreach ($request->file('images') as $image) {
                $path = $imageOptimizer->optimizeListingImage($image, (string)$listing->id);
                $finalMedia[] = $path;
            }
        }

        $listing->update(['media' => array_values($finalMedia)]);

        // Bust homepage cache
        \Illuminate\Support\Facades\Cache::forget('home_featured_listings');

        // Redirect to dashboard with success message
        return Redirect::route('dashboard')->with('success', __('Listing updated successfully!'));
    }

    /**
     * Quick Upload Media directly from listing card
     */
    public function quickUploadMedia(Request $request, Listing $listing)
    {
        if ($listing->seller_id !== Auth::id() && (!Auth::user() || Auth::user()->role !== 'admin')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'image' => 'required|image|max:10240',
        ]);

        $imageOptimizer = new ImageOptimizationService();
        $path = $imageOptimizer->optimizeListingImage($request->file('image'), (string)$listing->id);

        $currentMedia = is_array($listing->media) ? $listing->media : [];
        $currentMedia[] = $path;

        $listing->update(['media' => array_values($currentMedia)]);
        \Illuminate\Support\Facades\Cache::forget('home_featured_listings');

        return response()->json([
            'success' => true,
            'message' => __('Image uploaded successfully!'),
            'media' => $listing->media,
            'image_url' => \Illuminate\Support\Facades\Storage::disk('public')->url($path),
        ]);
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

        return Redirect::route('dashboard')->with('success', __('Listing deleted successfully!'));
    }
}
