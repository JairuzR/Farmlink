@extends('layouts.farmlink')

@section('title', 'Registration | FARMLINK')

@section('content')
    <section class="bg-emerald-50 py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8" x-data="{ role: '{{ old('role', 'user') }}' }">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold text-slate-950">Create Your Account</h1>
                <p class="mt-3 text-slate-600">Join FARMLINK to buy fresh produce or sell directly to local customers</p>
            </div>

                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Full Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div class="mt-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Phone -->
                    <div class="mt-4">
                        <x-input-label for="phone" :value="__('Phone Number')" />
                        <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <!-- Address -->
                    <div class="mt-4">
                        <x-input-label for="address" :value="__('Address')" />
                        <textarea id="address" name="address" rows="2"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('address') }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <!-- Role Selection -->
                    <div class="mt-4">
                        <x-input-label :value="__('I am registering as a...')" />
                        <div class="flex gap-4 mt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="role" value="buyer"
                                    {{ old('role', 'buyer') === 'buyer' ? 'checked' : '' }}
                                    onchange="toggleFarmerFields(false)" />
                                <span>Buyer</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="role" value="farmer"
                                    {{ old('role') === 'farmer' ? 'checked' : '' }}
                                    onchange="toggleFarmerFields(true)" />
                                <span>Farmer</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <!-- Farmer-only Fields -->
                    <div id="farmer-fields" class="{{ old('role') === 'farmer' ? '' : 'hidden' }} mt-4 space-y-4 border-t pt-4">
                        <p class="text-sm text-gray-500 font-medium">Farmer Details</p>

                        <!-- Farm Name -->
                        <div>
                            <x-input-label for="farm_name" :value="__('Farm Name')" />
                            <x-text-input id="farm_name" class="block mt-1 w-full" type="text" name="farm_name" :value="old('farm_name')" />
                            <x-input-error :messages="$errors->get('farm_name')" class="mt-2" />
                        </div>

                        <!-- Government ID Upload -->
                        <div>
                            <x-input-label for="farmer_id" :value="__('Government ID (jpg, png, or pdf)')" />
                            <input id="farmer_id" type="file" name="farmer_id" accept=".jpg,.jpeg,.png,.pdf"
                                class="block mt-1 w-full text-sm text-gray-600" />
                            <x-input-error :messages="$errors->get('farmer_id')" class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="latitude" :value="__('Latitude (optional)')" />
                                <x-text-input id="latitude" class="block mt-1 w-full" type="number" step="any" name="latitude" :value="old('latitude')" />
                                <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="longitude" :value="__('Longitude (optional)')" />
                                <x-text-input id="longitude" class="block mt-1 w-full" type="number" step="any" name="longitude" :value="old('longitude')" />
                                <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                            </div>
                        </div>
                        <p class="text-xs text-gray-400">You can set your exact location later from your profile.</p>

                        <!-- Facebook -->
                        <div>
                            <x-input-label for="facebook_url" :value="__('Facebook Page URL (optional)')" />
                            <x-text-input id="facebook_url" class="block mt-1 w-full" type="url" name="facebook_url" :value="old('facebook_url')" />
                            <x-input-error :messages="$errors->get('facebook_url')" class="mt-2" />
                        </div>

                        <!-- Bio -->
                        <div>
                            <x-input-label for="bio" :value="__('Short Bio (optional)')" />
                            <textarea id="bio" name="bio" rows="3"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('bio') }}</textarea>
                            <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4">
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                            {{ __('Already registered?') }}
                        </a>
                        <x-primary-button class="ms-4">
                            {{ __('Register') }}
                        </x-primary-button>
                    </div>
                </form>

                <script>
                    function toggleFarmerFields(show) {
                        document.getElementById('farmer-fields').classList.toggle('hidden', !show);
                    }
                </script>

        </div>
    </section>
@endsection
