@extends('layouts.farmlink')

@section('title', 'Registration | FARMLINK')

@section('content')
    <section class="bg-emerald-50 py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8" x-data="{ role: '{{ old('role', 'user') }}' }">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold text-slate-950">Create Your Account</h1>
                <p class="mt-3 text-slate-600">Join FARMLINK to buy fresh produce or sell directly to local customers</p>
            </div>

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                @csrf

                <!-- Account Type Selection -->
                <div class="mb-6">
                    <x-input-label for="role" :value="__('Account Type')" />
                    <div class="mt-3 flex gap-6">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="role" value="user" x-model="role" class="h-4 w-4 text-green-600" />
                            <span class="text-sm font-medium text-slate-700">I'm a Buyer</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="role" value="farmer" x-model="role" class="h-4 w-4 text-green-600" />
                            <span class="text-sm font-medium text-slate-700">I'm a Farmer</span>
                        </label>
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="name" :value="__('Full Name')" />
                        <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div x-show="role === 'farmer'">
                        <x-input-label for="farm_name" :value="__('Farm Name')" />
                        <x-text-input id="farm_name" class="mt-1 block w-full" type="text" name="farm_name" :value="old('farm_name')" placeholder="Juan Dela Cruz Farm" />
                        <x-input-error :messages="$errors->get('farm_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email Address')" />
                        <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="phone" :value="__('Phone Number')" />
                        <x-text-input id="phone" class="mt-1 block w-full" type="tel" name="phone" :value="old('phone')" placeholder="+63 912 345 6789" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-input-label for="address" :value="__('Address')" />
                        <x-text-input id="address" class="mt-1 block w-full" type="text" name="address" :value="old('address')" placeholder="Your Address" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <!-- ID Upload for Farmers -->
                    <div x-show="role === 'farmer'" class="sm:col-span-2">
                        <x-input-label for="id_file" :value="__('Government ID (for verification)')" />
                        <p class="mt-1 text-sm text-slate-600">Upload a photo of your ID (Driver's License, Passport, NBI, or TIN). Max 5MB.</p>
                        <input id="id_file" type="file" name="id_file" accept="image/*,.pdf" class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2" />
                        <x-input-error :messages="$errors->get('id_file')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <!-- Info Box -->
                <div x-show="role === 'farmer'" class="mt-6 rounded-lg bg-blue-50 p-4 text-sm leading-6 text-blue-800">
                    <strong>Farmer Account Approval:</strong> Your account will be reviewed by FARMLINK within 24-48 hours. We'll verify your ID and contact you to confirm your details before your account is activated.
                </div>

                <div x-show="role === 'user'" class="mt-6 rounded-lg bg-blue-50 p-4 text-sm leading-6 text-blue-800">
                    <strong>Welcome!</strong> After registration, you can immediately browse the marketplace, add produce to your cart, and track your orders.
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a class="text-sm font-semibold text-slate-600 hover:text-slate-900" href="{{ route('login') }}">
                        Already registered?
                    </a>

                    <button type="submit" class="rounded-lg bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
