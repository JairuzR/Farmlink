<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\FarmerSocialLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        if ($user->isFarmer()) {
            $user->load('socialLinks');
        }

        return view('profile.edit', compact('user'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Core fields for everyone
        $user->fill([
            'name'    => $data['name'],
            'email'   => $data['email'],
            'phone'   => $data['phone'] ?? $user->phone,
            'address' => $data['address'] ?? $user->address,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Farmer-only fields
        if ($user->isFarmer()) {
            $user->fill([
                'farm_name' => $data['farm_name'] ?? null,
                'bio'       => $data['bio'] ?? null,
                'latitude'  => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
            ]);

            // Rebuild social links — delete all, re-insert
            $user->socialLinks()->delete();

            $platforms = $data['social_platform'] ?? [];
            $labels    = $data['social_label'] ?? [];
            $urls      = $data['social_url'] ?? [];

            foreach ($platforms as $i => $platform) {
                $url = trim($urls[$i] ?? '');
                if ($url === '') continue; // skip empty rows

                FarmerSocialLink::create([
                    'user_id'  => $user->id,
                    'platform' => $platform,
                    'label'    => $labels[$i] ?? $platform,
                    'url'      => $url,
                ]);
            }
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

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
}