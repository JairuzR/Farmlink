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

        if ($request->role === 'farmer') {
            $rules['farm_name']        = ['required', 'string', 'max:255'];
            $rules['farmer_id']        = ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
            $rules['latitude']         = ['nullable', 'numeric', 'between:-90,90'];
            $rules['longitude']        = ['nullable', 'numeric', 'between:-180,180'];
            $rules['bio']              = ['nullable', 'string', 'max:1000'];
            $rules['social_platform']  = ['nullable', 'array'];
            $rules['social_platform.*']= ['nullable', 'string', 'max:50'];
            $rules['social_label']     = ['nullable', 'array'];
            $rules['social_label.*']   = ['nullable', 'string', 'max:100'];
            $rules['social_url']       = ['nullable', 'array'];
            $rules['social_url.*']     = ['nullable', 'url', 'max:500'];
        }

        $request->validate($rules);

        $farmerIdPath = null;
        if ($request->hasFile('farmer_id')) {
            $farmerIdPath = $request->file('farmer_id')->store('farmer-ids', 'private');
        }

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'role'           => $request->role,
            'phone'          => $request->phone,
            'address'        => $request->address,
            'farm_name'      => $request->farm_name,
            'farmer_id_path' => $farmerIdPath,
            'latitude'       => $request->latitude,
            'longitude'      => $request->longitude,
            'bio'            => $request->bio,
            'is_approved'    => $request->role === 'buyer',
        ]);

        // Save social links for farmers
        if ($request->role === 'farmer' && $request->filled('social_url')) {
            $platforms = $request->input('social_platform', []);
            $labels    = $request->input('social_label', []);
            $urls      = $request->input('social_url', []);

            foreach ($urls as $i => $url) {
                if (!empty($url)) {
                    $user->socialLinks()->create([
                        'platform' => $platforms[$i] ?? 'other',
                        'label'    => $labels[$i] ?? null,
                        'url'      => $url,
                    ]);
                }
            }
        }

        event(new Registered($user));
        Auth::login($user);

        if ($user->isFarmer()) {
            return redirect()->route('verification.notice')
                ->with('status', 'Please verify your email. Your account also needs admin approval before you can list products.');
        }

        return redirect()->route('verification.notice');
    }
}