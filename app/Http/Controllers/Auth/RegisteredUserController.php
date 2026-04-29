<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'in:farmer,buyer'],
            'phone'    => ['required', 'string', 'max:20'],
            'address'  => ['required', 'string', 'max:500'],
        ];

        // Extra validation for farmers
        if ($request->role === 'farmer') {
            $rules['farm_name']    = ['required', 'string', 'max:255'];
            $rules['farmer_id']    = ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
            $rules['latitude']     = ['nullable', 'numeric', 'between:-90,90'];
            $rules['longitude']    = ['nullable', 'numeric', 'between:-180,180'];
            $rules['facebook_url'] = ['nullable', 'url', 'max:255'];
            $rules['bio']          = ['nullable', 'string', 'max:1000'];
        }

        $request->validate($rules);

        // Handle farmer ID upload
        $farmerIdPath = null;
        if ($request->hasFile('farmer_id')) {
            $farmerIdPath = $request->file('farmer_id')->store('farmer-ids', 'private');
        }

        $user = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'role'            => $request->role,
            'phone'           => $request->phone,
            'address'         => $request->address,
            'farm_name'       => $request->farm_name,
            'farmer_id_path'  => $farmerIdPath,
            'latitude'        => $request->latitude,
            'longitude'       => $request->longitude,
            'facebook_url'    => $request->facebook_url,
            'bio'             => $request->bio,
            'is_approved'     => $request->role === 'buyer', // buyers auto-approved, farmers need admin approval
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role
        if ($user->isFarmer()) {
            return redirect()->route('verification.notice')
                ->with('status', 'Please verify your email. Your account also needs admin approval before you can list products.');
        }

        return redirect()->route('verification.notice');
    }
}