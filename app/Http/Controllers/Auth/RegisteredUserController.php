<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role');
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
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
        $user->password = Hash::make($validated['password']);
        $user->role = $role;
        if ($role === 'farmer') {
            $user->olive_type = $validated['olive_type'];
            $user->farm_location = $validated['farm_location'];
            $user->tree_number = $validated['tree_number'];
        } elseif ($role === 'carrier') {
            $user->camion_capacity = $validated['camion_capacity'];
        } elseif ($role === 'mill') {
            $user->mill_name = $validated['mill_name'];
        }
        if ($request->hasFile('profile_picture')) {
            $user->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }
        $user->save();
        Auth::login($user);
        return redirect()->route('home');
    }
}
