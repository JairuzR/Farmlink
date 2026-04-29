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
use Illuminate\Validation\ValidationException;
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
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'in:user,farmer'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'farm_name' => ['nullable', 'string', 'max:255'],
            'id_file' => ['nullable', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        // Handle ID file upload for farmers
        $idPath = null;
        if ($request->role === 'farmer' && $request->hasFile('id_file')) {
            $idPath = $request->file('id_file')->store('farmer_ids', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'farm_name' => $request->farm_name,
            'role' => $request->role,
            'farmer_id_path' => $idPath,
            'is_approved' => $request->role === 'farmer' ? false : true, // Farmers need approval
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($request->role === 'farmer') {
            return redirect(route('dashboard', absolute: false))->with('status', 'Your farmer account is pending approval. We will review your ID and contact you within 24-48 hours.');
        }

        return redirect(route('marketplace', absolute: false));
    }
}
