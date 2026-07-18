<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ImageOptimizationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ServiceProviderRegisterController extends Controller
{
    protected ImageOptimizationService $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function create(): View
    {
        return view('auth.register_provider');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255', 'unique:users',
                function ($attribute, $value, $fail) {
                    if (app()->environment('production')) {
                        $domain = substr(strrchr($value, "@"), 1);
                        if ($domain && !checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
                            $fail(__('الرجاء إدخال بريد إلكتروني حقيقي وصالح.'));
                        }
                    }
                }
            ],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:carrier,mill,packer,transiteur,comptable,service_bureau,agri_equipment,agri_materials,agri_study_office'],
            'provider_type' => ['required', 'in:freelancer,bureau,societe'],
            'governorate' => ['required', 'string'],
            'service_description' => ['required', 'string', 'max:1000'],
            'price_type' => ['required', 'in:fixed,quote'],
            'service_price' => ['nullable', 'numeric', 'min:0'],
            'profile_picture' => ['nullable', 'image', 'max:20480'],
            'cover_photos' => ['nullable', 'array', 'max:1'],
            'cover_photos.*' => ['nullable', 'image', 'max:20480'],
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];

        // Store description, price, and price type in meta_data
        $user->meta_data = [
            'service_description' => $validated['service_description'],
            'price_type' => $validated['price_type'],
            'service_price' => $validated['service_price'] ?? null,
            'provider_type' => $validated['provider_type'],
        ];

        // Optimize and save profile picture/logo
        if ($request->hasFile('profile_picture')) {
            $user->profile_picture = $this->imageService->optimizeProfilePicture(
                $request->file('profile_picture')
            );
        }

        // Optimize and save cover photo/service image
        if ($request->hasFile('cover_photos')) {
            $coverPhotos = [];
            foreach ($request->file('cover_photos') as $photo) {
                if (count($coverPhotos) < 1) {
                    $coverPhotos[] = $this->imageService->optimizeCoverPhoto($photo);
                }
            }
            $user->cover_photos = $coverPhotos;
        }

        $user->save();

        // Clear home page service providers cache
        \Illuminate\Support\Facades\Cache::forget('home_service_providers');

        // Create the user address
        $user->addresses()->create([
            'governorate' => $validated['governorate'],
            'label' => 'المركز الرئيسي',
        ]);

        event(new Registered($user));

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeUser($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send welcome email to service provider: ' . $e->getMessage());
        }

        Auth::login($user, true);

        return redirect()->route('dashboard')->with('success', __('Registration successful! Welcome to Zintoop.'));
    }
}
