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

                        <!-- Location via map -->
                        <div>
                            <x-input-label :value="__('Farm Location (optional)')" />
                            <p class="text-xs text-gray-400 mb-2">Click the map to drop a pin on your farm's location. You can also set this later from your profile.</p>

                            {{-- Hidden inputs that get filled when user clicks the map --}}
                            <input type="hidden" name="latitude"  id="latitude"  value="{{ old('latitude') }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                            <div id="register-map" class="mt-1 rounded-lg border border-gray-300 overflow-hidden" style="height: 280px;"></div>

                            <p id="pin-status" class="mt-1 text-xs text-gray-400">No location selected.</p>
                        </div>
                        <p class="text-xs text-gray-400">You can set your exact location later from your profile.</p>

                        <!-- Social Links (dynamic) -->
                        <div>
                            <x-input-label :value="__('Social Media Links (optional)')" />
                            <p class="text-xs text-gray-400 mb-2">Add your Facebook, Instagram, TikTok, or any link.</p>

                            <div id="social-links-container" class="space-y-3">
                                <div class="social-link-row flex gap-2 items-center">
                                    <select name="social_platform[]"
                                        class="border-gray-300 rounded-md shadow-sm text-sm w-36 h-10">
                                        <option value="facebook">Facebook</option>
                                        <option value="instagram">Instagram</option>
                                        <option value="tiktok">TikTok</option>
                                        <option value="youtube">YouTube</option>
                                        <option value="x">X (Twitter)</option>
                                        <option value="shopee">Shopee</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <input type="text" name="social_label[]" placeholder="Label (optional)"
                                        class="border-gray-300 rounded-md shadow-sm text-sm h-10 w-32" />
                                    <input type="url" name="social_url[]" placeholder="https://..."
                                        class="border-gray-300 rounded-md shadow-sm text-sm h-10 flex-1" />
                                    <button type="button" onclick="removeSocialRow(this)"
                                        class="text-red-400 hover:text-red-600 text-lg leading-none px-1">✕</button>
                                </div>
                            </div>

                            <button type="button" onclick="addSocialRow()"
                                class="mt-2 text-sm text-emerald-600 hover:underline font-medium">+ Add another link</button>

                            <x-input-error :messages="$errors->get('social_url.*')" class="mt-2" />
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

                    function addSocialRow() {
                        const container = document.getElementById('social-links-container');
                        const row = document.createElement('div');
                        row.className = 'social-link-row flex gap-2 items-center';
                        row.innerHTML = `
                            <select name="social_platform[]"
                                class="border-gray-300 rounded-md shadow-sm text-sm w-36 h-10">
                                <option value="facebook">Facebook</option>
                                <option value="instagram">Instagram</option>
                                <option value="tiktok">TikTok</option>
                                <option value="youtube">YouTube</option>
                                <option value="x">X (Twitter)</option>
                                <option value="shopee">Shopee</option>
                                <option value="other">Other</option>
                            </select>
                            <input type="text" name="social_label[]" placeholder="Label (optional)"
                                class="border-gray-300 rounded-md shadow-sm text-sm h-10 w-32" />
                            <input type="url" name="social_url[]" placeholder="https://..."
                                class="border-gray-300 rounded-md shadow-sm text-sm h-10 flex-1" />
                            <button type="button" onclick="removeSocialRow(this)"
                                class="text-red-400 hover:text-red-600 text-lg leading-none px-1">✕</button>
                        `;
                        container.appendChild(row);
                    }

                    function removeSocialRow(btn) {
                        const rows = document.querySelectorAll('.social-link-row');
                        if (rows.length > 1) {
                            btn.closest('.social-link-row').remove();
                        }
                    }
                </script>

                {{-- Leaflet for registration map --}}
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

                <script>
                // Only init the map when farmer fields are visible
                function initRegisterMap() {
                    if (window._registerMapInit) return;
                    window._registerMapInit = true;

                    // Default center: Cagayan de Oro — adjust if you want
                    const map = L.map('register-map').setView([8.4542, 124.6319], 11);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    let marker = null;

                    // If old() values exist (validation failed, repopulating), restore the pin
                    const oldLat = parseFloat('{{ old('latitude') }}');
                    const oldLng = parseFloat('{{ old('longitude') }}');
                    if (oldLat && oldLng) {
                        marker = L.marker([oldLat, oldLng]).addTo(map);
                        map.setView([oldLat, oldLng], 14);
                        document.getElementById('pin-status').textContent = `📍 ${oldLat.toFixed(5)}, ${oldLng.toFixed(5)}`;
                    }

                    map.on('click', function (e) {
                        const { lat, lng } = e.latlng;

                        // Move existing marker or create new one
                        if (marker) {
                            marker.setLatLng([lat, lng]);
                        } else {
                            marker = L.marker([lat, lng]).addTo(map);
                        }

                        // Fill the hidden inputs
                        document.getElementById('latitude').value  = lat.toFixed(7);
                        document.getElementById('longitude').value = lng.toFixed(7);
                        document.getElementById('pin-status').textContent = `📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                    });
                }

                // Hook into the existing toggleFarmerFields function
                const _originalToggle = window.toggleFarmerFields;
                window.toggleFarmerFields = function(show) {
                    _originalToggle(show);
                    if (show) {
                        // Leaflet needs the container to be visible before it can init
                        setTimeout(initRegisterMap, 50);
                    }
                };

                // If farmer is pre-selected on page load (e.g. old() after validation fail)
                if (document.querySelector('input[name="role"][value="farmer"]')?.checked) {
                    setTimeout(initRegisterMap, 50);
                }
                </script>

        </div>
    </section>
@endsection