<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    // Show QR code setup page
    public function setup(Request $request)
    {
        $user = $request->user();
        $google2fa = new Google2FA();

        if (!$user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => $secret]);
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->two_factor_secret
        );

        return view('auth.2fa-setup', compact('qrCodeUrl'));
    }

    // Confirm and enable 2FA
    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $user = $request->user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid code. Try again.']);
        }

        $user->update(['two_factor_confirmed' => true]);

        return redirect()->route('profile.edit')->with('status', '2FA enabled successfully!');
    }

    // Disable 2FA
    public function disable(Request $request)
    {
        $request->user()->update([
            'two_factor_secret'    => null,
            'two_factor_confirmed' => false,
        ]);

        return back()->with('status', '2FA disabled.');
    }

    // Show OTP prompt after login
    public function challenge()
    {
        return view('auth.2fa-challenge');
    }

    // Verify OTP after login
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $user = $request->user();
        $google2fa = new Google2FA();

        if (!$google2fa->verifyKey($user->two_factor_secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid code.']);
        }

        session(['2fa_verified' => true]);

        return redirect()->intended(route('dashboard'));
    }
}