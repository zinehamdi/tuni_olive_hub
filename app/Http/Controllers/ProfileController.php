<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\ImageOptimizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Profile Controller - متحكم الملف الشخصي
 * 
 * Manages user profile, dashboard, and account settings
 * يدير الملف الشخصي للمستخدم ولوحة التحكم وإعدادات الحساب
 * 
 * @package App\Http\Controllers
 */
class ProfileController extends Controller
{
    protected ImageOptimizationService $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Show the user's profile (dashboard)
     * عرض الملف الشخصي للمستخدم (لوحة التحكم)
     * 
     * Displays user's listings, statistics, and profile completion
     * يعرض عروض المستخدم والإحصائيات ونسبة اكتمال الملف الشخصي
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $user = $request->user();
        
        // Get user's listings with product relationship
        $listings = $user->listings()->with('product')->latest()->paginate(10);
        
        // Calculate statistics
        $activeListings = $user->listings()->where('status', 'active')->count();
        $pendingListings = $user->listings()->where('status', 'pending')->count();
        
        // Calculate profile completion percentage
        $profileCompletion = $this->calculateProfileCompletion($user);
        
        return view('dashboard', compact('user', 'listings', 'activeListings', 'pendingListings', 'profileCompletion'));
    }
    
    /**
     * Calculate profile completion percentage
     * حساب نسبة اكتمال الملف الشخصي
     * 
     * Checks required fields and role-specific fields to calculate
     * how complete the user's profile is
     * يتحقق من الحقول المطلوبة والحقول الخاصة بالدور لحساب
     * مدى اكتمال الملف الشخصي للمستخدم
     * 
     * @param  \App\Models\User  $user  المستخدم
     * @return int  نسبة الاكتمال (0-100)
     */
    private function calculateProfileCompletion($user)
    {
        $fields = ['name', 'email', 'phone', 'profile_picture'];
        $roleFields = [];
        
        if ($user->role === 'farmer') {
            $roleFields = ['farm_location', 'tree_number', 'olive_type'];
        } elseif ($user->role === 'carrier') {
            $roleFields = ['camion_capacity'];
        } elseif ($user->role === 'mill') {
            $roleFields = ['mill_name'];
        }
        
        $allFields = array_merge($fields, $roleFields);
        $filledFields = 0;
        
        foreach ($allFields as $field) {
            if (!empty($user->$field)) {
                $filledFields++;
            }
        }
        
        return round(($filledFields / count($allFields)) * 100);
    }
    
    /**
     * View a public user profile (seller/publisher)
     * عرض الملف الشخصي العام للمستخدم (البائع/الناشر)
     * 
     * Allows anyone to view a user's public profile information
     * This is useful for viewing seller profiles from orders
     * يسمح لأي شخص بمشاهدة معلومات الملف الشخصي العامة
     * هذا مفيد لعرض ملفات البائعين من الطلبات
     * 
     * @param  \App\Models\User  $user  المستخدم المراد عرض ملفه
     * @return \Illuminate\View\View
     */
    public function viewPublicProfile(\App\Models\User $user): View
    {
        // Get user's active listings
        $listings = $user->listings()
            ->with('product')
            ->where('status', 'active')
            ->latest()
            ->paginate(12);
        
        // Get user's addresses for location display
        $addresses = $user->addresses;
        
        // Calculate statistics
        $activeListings = $user->listings()->where('status', 'active')->count();
        $totalListings = $user->listings()->count();
        
        // Get contact information (if available)
        $contactInfo = [
            'phone' => $user->phone,
            'email' => $user->email,
        ];
        
        // Role-specific information
        $roleInfo = [];
        switch ($user->role) {
            case 'farmer':
                $roleInfo = [
                    'farm_name' => $user->farm_name ?? $user->farm_name_ar,
                    'tree_number' => $user->tree_number,
                    'olive_type' => $user->olive_type,
                    'farm_location' => $user->farm_location,
                ];
                break;
            case 'carrier':
                $roleInfo = [
                    'company_name' => $user->company_name,
                    'fleet_size' => $user->fleet_size,
                    'camion_capacity' => $user->camion_capacity,
                ];
                break;
            case 'mill':
                $roleInfo = [
                    'mill_name' => $user->mill_name,
                    'capacity' => $user->capacity,
                ];
                break;
            case 'packer':
                $roleInfo = [
                    'packer_name' => $user->packer_name,
                    'packaging_types' => $user->packaging_types,
                ];
                break;
        }
        
        return view('profile.public', compact(
            'user',
            'listings',
            'addresses',
            'activeListings',
            'totalListings',
            'contactInfo',
            'roleInfo'
        ));
    }
    
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     * تحديث معلومات الملف الشخصي للمستخدم
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Fill validated data first
        $user->fill($request->validated());
        
        // Handle profile picture upload with optimization
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            $this->imageService->deleteImage($user->profile_picture);
            
            // Optimize and store new profile picture
            $user->profile_picture = $this->imageService->optimizeProfilePicture(
                $request->file('profile_picture')
            );
        }

        // Handle multiple cover photos upload with optimization
        if ($request->hasFile('cover_photos')) {
            $coverPhotos = $user->cover_photos ?? [];
            
            // Ensure coverPhotos is an array of strings only
            $coverPhotos = array_filter($coverPhotos, fn($p) => is_string($p) && !empty($p));
            $coverPhotos = array_values($coverPhotos);
            
            // Log for debugging
            \Log::info('Cover photos upload', [
                'existing_count' => count($coverPhotos),
                'new_files_count' => count($request->file('cover_photos')),
            ]);
            
            // Remove selected photos if any
            if ($request->has('remove_cover_photos')) {
                $removeIndices = json_decode($request->input('remove_cover_photos'), true) ?? [];
                foreach ($removeIndices as $index) {
                    if (isset($coverPhotos[$index])) {
                        $this->imageService->deleteImage($coverPhotos[$index]);
                    }
                    unset($coverPhotos[$index]);
                }
                $coverPhotos = array_values($coverPhotos); // Re-index array
            }
            
            // Add new photos with optimization (max 5 total)
            $uploadedCount = 0;
            foreach ($request->file('cover_photos') as $photo) {
                if (count($coverPhotos) < 5) {
                    $coverPhotos[] = $this->imageService->optimizeCoverPhoto($photo);
                    $uploadedCount++;
                }
            }
            
            \Log::info('Cover photos after upload', [
                'total_count' => count($coverPhotos),
                'uploaded_count' => $uploadedCount,
            ]);
            
            $user->cover_photos = $coverPhotos;
        }
        
        // Handle remove cover photos without new uploads
        if ($request->has('remove_cover_photos') && !$request->hasFile('cover_photos')) {
            $coverPhotos = $user->cover_photos ?? [];
            $removeIndices = json_decode($request->input('remove_cover_photos'), true) ?? [];
            foreach ($removeIndices as $index) {
                if (isset($coverPhotos[$index])) {
                    $this->imageService->deleteImage($coverPhotos[$index]);
                }
                unset($coverPhotos[$index]);
            }
            $user->cover_photos = array_values($coverPhotos);
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update a single profile field via AJAX
     */
    public function updateField(Request $request): \Illuminate\Http\JsonResponse
    {
        $field = $request->input('field');
        $value = $request->input('value');
        
        // Allowed fields for inline editing
        $allowedFields = [
            'name', 'email', 'phone',
            'farm_name', 'farm_name_ar', 'company_name', 'mill_name', 'packer_name',
            'tree_number', 'olive_type', 'capacity', 'fleet_size', 'camion_capacity', 'packaging_types'
        ];
        
        if (!in_array($field, $allowedFields)) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid field.')
            ], 400);
        }
        
        // Validate based on field type
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->user()->id,
            'phone' => 'nullable|string|max:20',
            'farm_name' => 'nullable|string|max:255',
            'farm_name_ar' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'mill_name' => 'nullable|string|max:255',
            'packer_name' => 'nullable|string|max:255',
            'tree_number' => 'nullable|integer|min:0',
            'olive_type' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'fleet_size' => 'nullable|integer|min:0',
            'camion_capacity' => 'nullable|numeric|min:0',
            'packaging_types' => 'nullable|string|max:255',
        ];
        
        $validator = \Illuminate\Support\Facades\Validator::make(
            [$field => $value],
            [$field => $rules[$field] ?? 'nullable|string|max:255']
        );
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first($field)
            ], 422);
        }
        
        $user = $request->user();
        $user->$field = $value;
        
        // If email changed, reset verification
        if ($field === 'email' && $user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => __('Updated successfully!')
        ]);
    }

    /**
     * Upload profile or cover photo via AJAX
     */
    public function uploadPhoto(Request $request): \Illuminate\Http\JsonResponse
    {
        $type = $request->input('type');
        
        if (!$request->hasFile('photo')) {
            return response()->json([
                'success' => false,
                'message' => __('No photo uploaded.')
            ], 400);
        }
        
        $request->validate([
            'photo' => 'required|image|max:10240' // 10MB max
        ]);
        
        $user = $request->user();
        
        if ($type === 'profile') {
            // Delete old profile picture
            if ($user->profile_picture) {
                $this->imageService->deleteImage($user->profile_picture);
            }
            
            // Optimize and store new profile picture
            $user->profile_picture = $this->imageService->optimizeProfilePicture(
                $request->file('photo')
            );
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => __('Profile picture updated!'),
                'url' => asset('storage/' . $user->profile_picture)
            ]);
        } elseif ($type === 'cover') {
            $coverPhotos = $user->cover_photos ?? [];
            $coverPhotos = array_filter($coverPhotos, fn($p) => is_string($p) && !empty($p));
            $coverPhotos = array_values($coverPhotos);
            
            if (count($coverPhotos) >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => __('Maximum 5 cover photos allowed.')
                ], 400);
            }
            
            $coverPhotos[] = $this->imageService->optimizeCoverPhoto($request->file('photo'));
            $user->cover_photos = $coverPhotos;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => __('Cover photo added!')
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => __('Invalid photo type.')
        ], 400);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Onboarding dispatcher: routes to correct form or dashboard
     */
    public function onboarding(Request $request)
    {
        $user = $request->user();
        if ($user->onboarding_completed_at) {
            return redirect()->route('dashboard');
        }
        switch ($user->role) {
            case 'farmer':
                return view('onboarding.farmer');
            case 'carrier':
                return view('onboarding.carrier');
            case 'mill':
                return view('onboarding.mill');
            case 'packer':
                return view('onboarding.packer');
            case 'normal':
            default:
                return view('onboarding.normal');
        }
    }

        /**
         * Handle onboarding form submission and mark onboarding as completed
         */
        public function submitOnboarding(Request $request, $role)
        {
            $user = $request->user();
            // Save onboarding info (minimal, can be expanded per role)
            switch ($role) {
                case 'farmer':
                    $user->farm_name_ar = $request->input('farm_name_ar');
                    $user->farm_name_lat = $request->input('farm_name_lat');
                    $user->location = $request->input('location');
                    break;
                case 'carrier':
                    $user->company_name = $request->input('company_name');
                    $user->fleet_size = $request->input('fleet_size');
                    break;
                case 'mill':
                    $user->mill_name = $request->input('mill_name');
                    $user->capacity = $request->input('capacity');
                    break;
                case 'packer':
                    $user->packer_name = $request->input('packer_name');
                    $user->packaging_types = $request->input('packaging_types');
                    break;
                case 'normal':
                default:
                    $user->full_name = $request->input('full_name');
                    break;
            }
            $user->onboarding_completed_at = now();
            $user->save();
            return redirect()->route('dashboard')->with('status', 'onboarding-completed');
        }
}
