<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Services\ImageOptimizationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends ApiController
{
    protected ImageOptimizationService $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Handle API Login (email or phone).
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string'], // Accepts email or phone
            'password' => ['required', 'string'],
        ]);

        $input = trim($request->input('email'));
        
        // Clean phone inputs (remove space, +, -, brackets)
        $cleanInput = preg_replace('/[\s\+\-\(\)]/', '', $input);
        $isPhone = preg_match('/^\d+$/', $cleanInput);
        
        if ($isPhone) {
            if (strlen($cleanInput) === 11 && str_starts_with($cleanInput, '216')) {
                $cleanInput = substr($cleanInput, 3);
            }
            $credentials = ['phone' => $cleanInput, 'password' => $request->input('password')];
            $user = User::where('phone', $cleanInput)->first();
        } else {
            $credentials = ['email' => $input, 'password' => $request->input('password')];
            $user = User::where('email', $input)->first();
        }

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        $token = $user->createToken('zintoop-mobile')->plainTextToken;

        $this->audit('mobile.login', 'user', $user->id);

        return $this->ok([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'profile_picture' => $user->profile_picture,
                'farm_location' => $user->farm_location ?? null,
                'olive_type' => $user->olive_type ?? null,
                'tree_number' => $user->tree_number ?? null,
                'camion_capacity' => $user->camion_capacity ?? null,
                'mill_name' => $user->mill_name ?? null,
            ]
        ]);
    }

    /**
     * Handle API Registration (with role-specific fields).
     */
    public function register(Request $request)
    {
        $role = $request->input('role');
        if (!in_array($role, ['farmer', 'carrier', 'mill', 'packer', 'normal'])) {
            return response()->json(['success' => false, 'error' => 'Invalid role selected.'], 422);
        }

        $rules = [
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
            'profile_picture' => ['nullable', 'image', 'max:20480'],
        ];

        if ($role === 'farmer') {
            $rules['olive_type'] = ['required', 'string', 'max:255'];
            $rules['farm_location'] = ['required', 'string', 'max:255'];
            $rules['tree_number'] = ['required', 'integer', 'min:1'];
        } elseif ($role === 'carrier') {
            $rules['camion_capacity'] = ['required', 'integer', 'min:1'];
        } elseif ($role === 'mill') {
            $rules['mill_name'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->password = Hash::make($validated['password']);
        $user->role = $role;

        if ($role === 'farmer') {
            $user->olive_type = $validated['olive_type'];
            $user->farm_location = $validated['farm_location'];
            $user->tree_number = (int) $validated['tree_number'];
        } elseif ($role === 'carrier') {
            $user->camion_capacity = (int) $validated['camion_capacity'];
        } elseif ($role === 'mill') {
            $user->mill_name = $validated['mill_name'];
        }

        // Optimize Profile Picture if uploaded
        if ($request->hasFile('profile_picture')) {
            try {
                $user->profile_picture = $this->imageService->optimizeProfilePicture(
                    $request->file('profile_picture')
                );
            } catch (\Exception $e) {
                Log::error('API Profile pic optimization error: ' . $e->getMessage());
            }
        }

        $user->save();

        event(new Registered($user));

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeUser($user));
        } catch (\Exception $e) {
            Log::error('API Registration: Failed to send welcome email: ' . $e->getMessage());
        }

        $token = $user->createToken('zintoop-mobile')->plainTextToken;

        return $this->ok([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'profile_picture' => $user->profile_picture,
                'farm_location' => $user->farm_location ?? null,
                'olive_type' => $user->olive_type ?? null,
                'tree_number' => $user->tree_number ?? null,
                'camion_capacity' => $user->camion_capacity ?? null,
                'mill_name' => $user->mill_name ?? null,
            ]
        ], 210);
    }

    /**
     * Handle API Logout.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $this->audit('mobile.logout', 'user', $request->user()->id);
        return $this->ok(true);
    }
}
