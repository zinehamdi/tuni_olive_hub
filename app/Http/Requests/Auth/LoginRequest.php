<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Hash;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'], // Accept both email and phone
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $input = trim((string) $this->input('email'));
        $password = (string) $this->input('password');
        $cleanDigits = preg_replace('/[^0-9]/', '', $input);
        
        $user = null;

        // 1. If input contains a phone number (Tunisian 8 digits or with +216/00216 prefix)
        if (!empty($cleanDigits) && strlen($cleanDigits) >= 8) {
            $eightDigits = substr($cleanDigits, -8);
            
            $user = \App\Models\User::where(function ($query) use ($eightDigits, $input) {
                $query->where('phone', $eightDigits)
                      ->orWhere('phone', '+216' . $eightDigits)
                      ->orWhere('phone', '+216 ' . $eightDigits)
                      ->orWhere('phone', '216' . $eightDigits)
                      ->orWhere('phone', '00216' . $eightDigits)
                      ->orWhere('phone', $input)
                      ->orWhere('phone', 'like', '%' . $eightDigits);
            })->first();
        }

        // 2. If not found by phone, search by email
        if (!$user) {
            $user = \App\Models\User::where('email', $input)->first();
        }

        // 3. Fallback: search raw input against phone
        if (!$user) {
            $user = \App\Models\User::where('phone', $input)->first();
        }

        // 4. If user found and password matches, log in immediately
        if ($user && Hash::check($password, $user->password)) {
            $remember = $this->boolean('remember') || ($user->role !== 'admin');
            Auth::login($user, $remember);
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // 5. Standard fallback attempt (if any custom provider/hashing applies)
        $credentials = $this->getCredentials();
        $remember = $this->boolean('remember') || ($user && $user->role !== 'admin');
        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($this->throttleKey());
            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Get the credentials from the request.
     * Determines if the input is an email or phone number.
     * Tunisian phone numbers: 8 digits (e.g., 22123123, 98123123, 55123123)
     *
     * @return array
     */
    protected function getCredentials(): array
    {
        $input = trim($this->input('email'));
        
        // Clean the input by removing spaces, +, -, and parentheses
        $cleanInput = preg_replace('/[\s\+\-\(\)]/', '', $input);
        
        // Check if it's a phone number (only digits remaining)
        $isPhone = preg_match('/^\d+$/', $cleanInput);
        
        if ($isPhone) {
            // Handle Tunisian phone numbers
            // If it has country code +216, remove it to get 8 digits
            if (strlen($cleanInput) === 11 && str_starts_with($cleanInput, '216')) {
                // +216 22123123 → 22123123
                $cleanInput = substr($cleanInput, 3);
            }
            
            // Now we should have an 8-digit Tunisian number (e.g., 22123123)
            return [
                'phone' => $cleanInput,
                'password' => $this->input('password'),
            ];
        }
        
        // Otherwise, treat as email
        return [
            'email' => $input,
            'password' => $this->input('password'),
        ];
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
