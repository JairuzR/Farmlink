@extends('layouts.farmlink')

@section('title', 'Login | FARMLINK')

@section('content')
<section class="bg-emerald-50 min-h-screen flex flex-col items-center justify-center py-12 px-4">

    {{-- Header --}}
    <div class="mb-6 text-center w-full max-w-md">
        <h1 class="text-3xl font-bold text-slate-900">Welcome back</h1>
        <p class="mt-2 text-sm text-slate-600">Sign in to your FARMLINK account</p>
    </div>

    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200">

        {{-- Session status --}}
        @if (session('status'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm font-medium text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input id="email" type="email" name="email"
                       value="{{ old('email') }}"
                       required autofocus autocomplete="username"
                       class="block w-full rounded-lg border-slate-300 text-sm shadow-sm
                              focus:border-green-500 focus:ring-green-500
                              @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-xs text-green-700 hover:text-green-900 font-medium">
                            Forgot password?
                        </a>
                    @endif
                </div>
                <input id="password" type="password" name="password"
                       required autocomplete="current-password"
                       class="block w-full rounded-lg border-slate-300 text-sm shadow-sm
                              focus:border-green-500 focus:ring-green-500
                              @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="flex items-center gap-2">
                <input id="remember_me" type="checkbox" name="remember"
                       class="rounded border-slate-300 text-green-600 shadow-sm focus:ring-green-500">
                <label for="remember_me" class="text-sm text-slate-600">Remember me</label>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full rounded-lg bg-green-600 px-5 py-3 font-semibold text-sm text-white
                       hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500
                       focus:ring-offset-2 transition">
                Sign In
            </button>
        </form>

        {{-- Divider --}}
        <div class="my-5 flex items-center gap-3">
            <div class="h-px flex-1 bg-slate-200"></div>
            <span class="text-xs text-slate-400 font-medium">or</span>
            <div class="h-px flex-1 bg-slate-200"></div>
        </div>

        <a href="{{ route('register') }}"
           class="block w-full rounded-lg border border-slate-300 bg-white px-5 py-3 text-center
                  text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition">
            Create an Account
        </a>
    </div>

    {{-- Footer note --}}
    <p class="mt-6 text-center text-xs text-slate-400">
        By signing in, you agree to FARMLINK's terms of service and privacy policy.
    </p>

</section>
@endsection