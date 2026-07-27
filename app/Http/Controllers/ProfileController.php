<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected ImageOptimizationService $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * GET /dashboard
     */
    public function show(Request $request)
    {
        $user = $request->user();
        // compute coverUrl safely
        $coverUrl = null;
        try {
            $flat = collect($user->cover_photos ?? [])->flatten()->filter(function($v){ return is_string($v) && trim($v) !== ""; })->values();
            $firstPath = $flat->first();
            if ($firstPath) {
                $coverUrl = \Illuminate\Support\Facades\Storage::disk("public")->url($firstPath);
            }
        } catch (\Throwable $e) {
            $coverUrl = null;
        }

        $listings = $user->listings()->with('product')->latest()->paginate(10);
        $activeListings  = $user->listings()->where('status', 'active')->count();
        $pendingListings = $user->listings()->where('status', 'pending')->count();
        $profileCompletion = $this->calculateProfileCompletion($user);
        $assignedLoads = $user->role === 'carrier' ? $user->assignedLoads()->latest()->paginate(10) : collect();
        $tanks = in_array($user->role, ['farmer', 'mill']) ? $user->tanks()->latest()->get() : collect();
        $myStories = \App\Models\Story::where('user_id', $user->id)->where('status', 'active')->latest()->get();

        return view('dashboard', compact('user','listings','activeListings','pendingListings','profileCompletion','coverUrl','assignedLoads', 'tanks', 'myStories'));
    }
    public function viewPublicProfile(\App\Models\User $user)
    {
        if (!headers_sent()) {
            header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('X-LiteSpeed-Control: no-cache');
        }

        if (auth()->guest()) {
            return redirect()->route('register')->with('info', __('يرجى التسجيل أو تسجيل الدخول لمشاهدة الملف الشخصي الكامل.'));
        }
        // compute coverUrl safely
        $coverUrl = null;
        try {
            $flat = collect($user->cover_photos ?? [])->flatten()->filter(function($v){ return is_string($v) && trim($v) !== ""; })->values();
            $first = $flat->first();
            if ($first) {
                $coverUrl = \Illuminate\Support\Facades\Storage::disk("public")->url($first);
            }
        } catch (\Throwable $e) {
            $coverUrl = null;
        }
        // Normalize media paths and filter missing files for public display
        $normalizePath = function ($path) {
            if (!$path) return null;
            if (str_starts_with($path, 'http')) return $path;
            $cleaned = ltrim(preg_replace('/^storage\//', '', $path), '/');
            return \Illuminate\Support\Facades\Storage::disk('public')->url($cleaned);
        };
        $existsOnDisk = function ($path) {
            if (!$path || str_starts_with($path, 'http')) return true;
            $cleaned = ltrim(preg_replace('/^storage\//', '', $path), '/');
            return \Illuminate\Support\Facades\Storage::disk('public')->exists($cleaned);
        };

        $rawCover = collect($user->cover_photos ?? []);
        $coverPhotos = $rawCover->map(function ($p) use ($normalizePath, $existsOnDisk) {
            $candidate = is_array($p) ? ($p['path'] ?? $p['url'] ?? ($p[0] ?? null)) : $p;
            if (!$existsOnDisk($candidate)) return null;
            return $normalizePath($candidate);
        })->filter()->values();

        $profilePhotoUrl = null;
        if ($user->profile_picture) {
            if (!$existsOnDisk($user->profile_picture)) {
                // Check raw relative path as stored (fallback)
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_picture)) {
                    $profilePhotoUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_picture);
                }
            } else {
                $profilePhotoUrl = $normalizePath($user->profile_picture);
            }
        }

        // Addresses and role-specific info
        $addresses = $user->addresses()->get();
        $roleInfo = [];
        if ($user->role === 'farmer') {
            $roleInfo = [
                'olive_type' => $user->olive_type,
                'farm_location' => $user->farm_location,
                'tree_number' => $user->tree_number,
            ];
        } elseif ($user->role === 'carrier') {
            $roleInfo = [
                'camion_capacity' => $user->camion_capacity,
            ];
        } elseif ($user->role === 'mill') {
            $roleInfo = [
                'mill_name' => $user->mill_name,
            ];
        }
        // Paginate active listings for public view to support links()
        $listings = $user->listings()->with('product')->where('status','active')->latest()->paginate(10);
        $totalListings   = $user->listings()->count();
        $activeListings  = $user->listings()->where('status', 'active')->count();
        $pendingListings = $user->listings()->where('status', 'pending')->count();
        $profileCompletion = $this->calculateProfileCompletion($user);
        $showContact = (bool) ($user->show_contact_info ?? false);
        $showAddress = (bool) ($user->show_address ?? false);

        if (!$showContact) {
            $contactInfo = ['phone' => null, 'email' => null];
        }

        if (!$showAddress) {
            $addresses = collect();
        }

        return view('profile.public', compact(
            'user',
            'coverUrl',
            'coverPhotos',
            'profilePhotoUrl',
            'addresses',
            'roleInfo',
            'listings',
            'totalListings',
            'activeListings',
            'pendingListings',
            'profileCompletion',
            'showContact',
            'showAddress'
        ));
    }
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * PATCH /profile
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        $data = $request->validated();
        $data['show_contact_info'] = $request->boolean('show_contact_info');
        $data['show_address'] = $request->boolean('show_address');

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $this->imageService->optimizeProfilePicture(
                $request->file('profile_picture')
            );
        }

        // Handle cover photos removal
        $currentCovers = $user->cover_photos ?? [];
        if ($request->filled('remove_cover_photos')) {
            $toRemove = json_decode($request->input('remove_cover_photos'), true) ?? [];
            $currentCovers = array_values(array_filter($currentCovers, fn($p) => !in_array($p, $toRemove)));
        }

        // Handle cover photos upload
        if ($request->hasFile('cover_photos')) {
            foreach ($request->file('cover_photos') as $photo) {
                if ($photo->isValid() && count($currentCovers) < 5) {
                    $currentCovers[] = $this->imageService->optimizeCoverPhoto($photo);
                }
            }
        }
        $data['cover_photos'] = $currentCovers;

        $user->fill($data);
        $user->show_contact_info = $data['show_contact_info'];
        $user->show_address = $data['show_address'];

        if (in_array($user->role, ['carrier', 'mill', 'packer', 'transiteur', 'comptable', 'service_bureau', 'agri_equipment', 'agri_materials', 'agri_study_office'])) {
            $services = [];
            if ($request->filled('services')) {
                $decoded = json_decode($request->input('services'), true);
                if (is_array($decoded)) {
                    foreach ($decoded as $item) {
                        $services[] = [
                            'title' => strip_tags((string)($item['title'] ?? '')),
                            'price' => isset($item['price']) && is_numeric($item['price']) ? (float)$item['price'] : null,
                            'price_type' => in_array($item['price_type'] ?? 'fixed', ['fixed', 'quote']) ? $item['price_type'] : 'fixed',
                            'description' => strip_tags((string)($item['description'] ?? '')),
                            'image' => $item['image'] ?? null,
                        ];
                    }
                }
            }
            $user->meta_data = array_merge($user->meta_data ?? [], [
                'provider_type' => $request->input('provider_type'),
                'service_description' => $request->input('service_description'),
                'price_type' => $request->input('price_type'),
                'service_price' => $request->input('service_price'),
                'services' => $services,
            ]);
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * DELETE /profile
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Manually cascade delete associated listings, products, and addresses
        try {
            $user->listings()->delete();
            $user->products()->delete();
            $user->addresses()->delete();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to clean up user records during profile deletion: ' . $e->getMessage());
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * PATCH /profile/field
     */
    public function updateField(Request $request)
    {
        $request->validate([
            'field' => ['required', 'string', 'in:name,phone,email,olive_type,farm_name,farm_location,tree_number,camion_capacity,company_name,mill_name,packer_name'],
            'value' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $field = $request->input('field');
        $value = $request->input('value');

        // Check if the field is actually allowed to be updated through this method
        $allowedFields = [
            'name', 'phone', 'email', 'olive_type', 'farm_name', 'farm_location', 
            'tree_number', 'camion_capacity', 'company_name', 'mill_name', 'packer_name'
        ];

        if (in_array($field, $allowedFields)) {
            $user->$field = $value;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => __('Updated successfully!'),
                'value' => $value
            ]);
        }

        return response()->json(['success' => false, 'message' => __('Invalid field.')], 400);
    }

    /**
     * POST /profile/photo
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', 'in:profile,cover'],
            'photo' => ['required', 'image', 'max:5120'], // 5MB max
        ]);

        $user = $request->user();
        $type = $request->input('type');
        $photo = $request->file('photo');

        try {
            if ($type === 'profile') {
                $path = $this->imageService->optimizeProfilePicture($photo);
                $user->profile_picture = $path;
                $user->save();
            } elseif ($type === 'cover') {
                $path = $this->imageService->optimizeCoverPhoto($photo);
                
                $covers = $user->cover_photos ?? [];
                if (!is_array($covers)) {
                    $covers = [];
                }
                
                // Add new cover, keeping max 5
                if (count($covers) >= 5) {
                    array_shift($covers); // remove oldest
                }
                $covers[] = $path;
                
                $user->cover_photos = $covers;
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => __('Photo uploaded successfully!'),
                'path' => $path
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Photo upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Failed to upload photo. Please try again.')
            ], 500);
        }
    }
    /**
     * POST /profile/service-card
     */
    public function addServiceCard(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'price_type' => ['required', 'string', 'in:fixed,quote'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'max:5120'], // Max 5MB
        ]);

        $user = $request->user();
        
        if (!in_array($user->role, ['carrier', 'mill', 'packer', 'transiteur', 'comptable', 'service_bureau', 'agri_equipment', 'agri_materials', 'agri_study_office'])) {
            return back()->with('error', __('Only service providers can add services.'));
        }

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                $imagePath = 'storage/' . $this->imageService->optimizeServicePhoto($request->file('image'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Service card image upload failed: ' . $e->getMessage());
            }
        }

        $meta = $user->meta_data ?? [];
        $services = $meta['services'] ?? [];

        $services[] = [
            'title' => strip_tags((string)$request->input('title')),
            'price' => $request->input('price_type') === 'fixed' && $request->filled('price') ? (float)$request->input('price') : null,
            'price_type' => $request->input('price_type'),
            'description' => strip_tags((string)$request->input('description', '')),
            'image' => $imagePath,
        ];

        $meta['services'] = $services;
        $user->meta_data = $meta;
        $user->save();

        return back()->with('success', __('Service card added successfully!'));
    }

    /**
     * POST /profile/service-card/update
     */
    public function updateServiceCard(Request $request)
    {
        $request->validate([
            'index' => ['required', 'integer', 'min:0'],
            'title' => ['required', 'string', 'max:255'],
            'price_type' => ['required', 'string', 'in:fixed,quote'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $user = $request->user();
        $index = (int)$request->input('index');

        $meta = $user->meta_data ?? [];
        $services = $meta['services'] ?? [];

        if (!isset($services[$index])) {
            return back()->with('error', __('Service not found.'));
        }

        $imagePath = $services[$index]['image'] ?? null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                $imagePath = 'storage/' . $this->imageService->optimizeServicePhoto($request->file('image'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Service card image upload failed: ' . $e->getMessage());
            }
        }

        $services[$index] = [
            'title' => strip_tags((string)$request->input('title')),
            'price' => $request->input('price_type') === 'fixed' && $request->filled('price') ? (float)$request->input('price') : null,
            'price_type' => $request->input('price_type'),
            'description' => strip_tags((string)$request->input('description', '')),
            'image' => $imagePath,
        ];

        $meta['services'] = $services;
        $user->meta_data = $meta;
        $user->save();

        return back()->with('success', __('Service card updated successfully!'));
    }

    /**
     * POST /profile/lab-analysis
     */
    public function uploadLabAnalysis(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'lab_name' => 'nullable|string|max:255',
            'analysis_date' => 'nullable|date',
            'pdf_file' => 'required|mimes:pdf|max:20480', // max 20MB
        ], [
            'pdf_file.required' => app()->getLocale() === 'ar' ? 'يرجى اختيار ملف التحليل بصيغة PDF.' : 'Please select a PDF file.',
            'pdf_file.mimes' => app()->getLocale() === 'ar' ? 'يجب أن يكون الملف بصيغة PDF فقط.' : 'File must be a PDF document.',
            'pdf_file.max' => app()->getLocale() === 'ar' ? 'حجم الملف يجب ألا يتجاوز 20 ميغابايت.' : 'PDF file size must not exceed 20MB.',
        ]);

        $user = $request->user();
        $file = $request->file('pdf_file');

        $filename = 'lab_' . time() . '_' . \Illuminate\Support\Str::random(6) . '.pdf';
        $path = $file->storeAs('lab_analyses/' . $user->id, $filename, 'public');

        $analyses = $user->lab_analyses ?? [];
        $analysisId = 'lab_' . \Illuminate\Support\Str::random(8);

        $newAnalysis = [
            'id' => $analysisId,
            'title' => strip_tags($request->input('title')),
            'lab_name' => strip_tags($request->input('lab_name', '')),
            'analysis_date' => $request->input('analysis_date'),
            'file_path' => $path,
            'file_size' => round($file->getSize() / 1024 / 1024, 2) . ' MB',
            'uploaded_at' => now()->toDateTimeString(),
        ];

        $analyses[] = $newAnalysis;
        $user->lab_analyses = $analyses;
        $user->save();

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم إضافة ملف التحليل المخبري بنجاح! 🎉' : 'Lab analysis PDF added successfully!');
    }

    /**
     * DELETE /profile/lab-analysis/{id}
     */
    public function deleteLabAnalysis(Request $request, string $id)
    {
        $user = $request->user();
        $analyses = collect($user->lab_analyses ?? []);

        $target = $analyses->firstWhere('id', $id);

        if ($target && !empty($target['file_path'])) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($target['file_path']);
        }

        $filtered = $analyses->reject(fn($item) => ($item['id'] ?? '') === $id)->values()->all();
        $user->lab_analyses = $filtered;
        $user->save();

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم حذف ملف التحليل المخبري بنجاح.' : 'Lab analysis PDF deleted successfully.');
    }

    /**
     * نسبة اكتمال البروفايل
     */
    private function calculateProfileCompletion(User $user): int
    {
        $baseFields = ['name', 'email', 'phone', 'profile_picture'];
        $have = 0;
        $total = count($baseFields);

        foreach ($baseFields as $f) {
            $v = data_get($user, $f);
            if (is_string($v)) $v = trim($v);
            if (!empty($v)) $have++;
        }

        return (int) floor(($have / max(1, $total)) * 100);
    }
}
