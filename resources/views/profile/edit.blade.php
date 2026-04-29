@extends('layouts.farmlink')

@section('title', 'Profile Settings | FARMLINK')

@section('content')
<section class="bg-slate-50 min-h-screen py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-950">Profile Settings</h1>
            <p class="mt-1 text-slate-500">Manage your account information{{ auth()->user()->isFarmer() ? ', farm details, and social links' : '' }}.</p>
        </div>

        @if(session('status') === 'profile-updated')
            <div class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Profile saved successfully.
            </div>
        @endif

        <div class="grid gap-8 {{ auth()->user()->isFarmer() ? 'lg:grid-cols-[1fr_380px]' : 'max-w-2xl' }}">

            {{-- LEFT: main form --}}
            <div class="space-y-6">

                {{-- Basic Info --}}
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold text-slate-900 mb-5">Account Information</h2>

                    <form method="POST" action="{{ route('profile.update') }}" id="profile-form" class="mt-6 space-y-6">
                        @csrf @method('PATCH')

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <x-input-label for="name" value="Full Name" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    :value="old('name', $user->name)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div>
                                <x-input-label for="phone" value="Phone Number" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                                    :value="old('phone', $user->phone)" />
                                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="email" value="Email Address" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email', $user->email)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <p class="mt-2 text-sm text-amber-600">
                                    Your email is unverified.
                                    <button form="send-verification" class="underline hover:text-amber-800">Resend verification.</button>
                                </p>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-1 text-sm text-green-600 font-medium">Verification link sent!</p>
                                @endif
                            @endif
                        </div>

                        <div>
                            <x-input-label for="address" value="Address" />
                            <textarea id="address" name="address" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">{{ old('address', $user->address) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>

                        {{-- ── FARMER FIELDS ── --}}
                        @if($user->isFarmer())
                        <div class="border-t border-slate-100 pt-6 space-y-6">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 -mb-2">Farm Details</p>

                            <div>
                                <x-input-label for="farm_name" value="Farm Name" />
                                <x-text-input id="farm_name" name="farm_name" type="text" class="mt-1 block w-full"
                                    :value="old('farm_name', $user->farm_name)" />
                                <x-input-error class="mt-2" :messages="$errors->get('farm_name')" />
                            </div>

                            <div>
                                <x-input-label for="bio" value="Short Bio" />
                                <textarea id="bio" name="bio" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">{{ old('bio', $user->bio) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                            </div>

                            <input type="hidden" name="latitude"  id="latitude"  value="{{ old('latitude', $user->latitude) }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $user->longitude) }}">
                        </div>

                        {{-- Social Links --}}
                        <div class="border-t border-slate-100 pt-6 space-y-6">
                            <div class="-mb-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Social Links</p>
                                <p class="text-xs text-slate-500 mt-1">Facebook, Instagram, TikTok — anywhere buyers can reach you.</p>
                            </div>

                            <div id="social-links-container" class="space-y-3">
                                @php
                                    $existingLinks = old('social_platform')
                                        ? collect(array_keys(old('social_platform')))->map(fn($i) => [
                                            'platform' => old("social_platform.$i"),
                                            'label'    => old("social_label.$i"),
                                            'url'      => old("social_url.$i"),
                                        ])
                                        : $user->socialLinks;
                                @endphp

                                @foreach($existingLinks as $i => $link)
                                <div class="social-row flex gap-2 items-center">
                                    <select name="social_platform[{{ $i }}]"
                                        class="border-gray-300 rounded-md shadow-sm text-sm h-10 w-36 shrink-0">
                                        @foreach(['Facebook','Instagram','TikTok','Twitter/X','YouTube','Shopee','Website','Other'] as $p)
                                            <option value="{{ $p }}" {{ ($link['platform'] ?? $link->platform ?? '') === $p ? 'selected' : '' }}>
                                                {{ $p }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="social_label[{{ $i }}]"
                                        placeholder="Label (optional)"
                                        value="{{ $link['label'] ?? $link->label ?? '' }}"
                                        class="border-gray-300 rounded-md shadow-sm text-sm h-10 w-28 shrink-0" />
                                    <input type="url" name="social_url[{{ $i }}]"
                                        placeholder="https://..."
                                        value="{{ $link['url'] ?? $link->url ?? '' }}"
                                        class="border-gray-300 rounded-md shadow-sm text-sm h-10 flex-1" />
                                    <button type="button" onclick="this.closest('.social-row').remove()"
                                        class="text-slate-400 hover:text-red-500 shrink-0 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>

                            <button type="button" id="add-social-link"
                                class="text-sm font-medium text-green-600 hover:underline flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add another link
                            </button>
                        </div>
                        @endif
                        {{-- ── END FARMER FIELDS ── --}}

                        <div class="flex items-center gap-4 border-t border-slate-100 pt-6">
                            <x-primary-button>Save Changes</x-primary-button>
                        </div>
                    </form>

                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>
                </div>

                {{-- Change Password --}}
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    @include('profile.partials.update-password-form')
                </div>

                {{-- Delete Account --}}
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    @include('profile.partials.delete-user-form')
                </div>

            </div>

            {{-- RIGHT: map column (farmers only) --}}
            @if($user->isFarmer())
            <div class="sticky top-6 space-y-5">
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        Farm Location
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">Click the map to update your farm's pin. Buyers use this to find you on the Farmers page.</p>

                    <div id="profile-map" class="mt-4 rounded-xl overflow-hidden border border-slate-200" style="height: 380px;"></div>

                    <p id="pin-status" class="mt-3 text-sm text-center text-slate-500 flex items-center justify-center gap-1">
                        @if($user->latitude && $user->longitude)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            {{ number_format($user->latitude, 5) }}, {{ number_format($user->longitude, 5) }}
                        @else
                            No location set yet.
                        @endif
                    </p>

                    @if($user->latitude && $user->longitude)
                    <button type="button" id="clear-pin"
                        class="mt-2 w-full text-center text-xs text-red-400 hover:text-red-600 flex items-center justify-center gap-1"
                        onclick="clearPin()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Clear location
                    </button>
                    @else
                    <button type="button" id="clear-pin"
                        class="hidden mt-2 w-full text-center text-xs text-red-400 hover:text-red-600 flex items-center justify-center gap-1"
                        onclick="clearPin()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Clear location
                    </button>
                    @endif
                </div>

                {{-- 2FA status card --}}
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        Two-Factor Auth
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        @if(auth()->user()->two_factor_confirmed)
                            2FA is <span class="font-semibold text-green-600">enabled</span> on your account.
                        @else
                            2FA is <span class="font-semibold text-slate-400">not enabled</span>.
                        @endif
                    </p>
                    <a href="{{ route('2fa.setup') }}"
                       class="mt-4 inline-block rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        {{ auth()->user()->two_factor_confirmed ? 'Manage 2FA' : 'Enable 2FA' }}
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
</section>

@if($user->isFarmer())
{{-- Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // ── Social link rows ──
    let rowIndex = {{ $existingLinks->count() }};

    document.getElementById('add-social-link').addEventListener('click', () => {
        const platforms = ['Facebook','Instagram','TikTok','Twitter/X','YouTube','Shopee','Website','Other'];
        const options = platforms.map(p => `<option value="${p}">${p}</option>`).join('');
        document.getElementById('social-links-container').insertAdjacentHTML('beforeend', `
            <div class="social-row flex gap-2 items-center">
                <select name="social_platform[${rowIndex}]"
                    class="border-gray-300 rounded-md shadow-sm text-sm h-10 w-36 shrink-0">
                    ${options}
                </select>
                <input type="text" name="social_label[${rowIndex}]"
                    placeholder="Label (optional)"
                    class="border-gray-300 rounded-md shadow-sm text-sm h-10 w-28 shrink-0" />
                <input type="url" name="social_url[${rowIndex}]"
                    placeholder="https://..."
                    class="border-gray-300 rounded-md shadow-sm text-sm h-10 flex-1" />
                <button type="button" onclick="this.closest('.social-row').remove()"
                    class="text-slate-400 hover:text-red-500 shrink-0 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        `);
        rowIndex++;
    });

    // ── Location map ──
    const existingLat = {{ $user->latitude ?? 'null' }};
    const existingLng = {{ $user->longitude ?? 'null' }};

    const map = L.map('profile-map').setView(
        existingLat && existingLng ? [existingLat, existingLng] : [8.4542, 124.6319],
        existingLat && existingLng ? 14 : 11
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    let marker = null;

    if (existingLat && existingLng) {
        marker = L.marker([existingLat, existingLng], { draggable: true }).addTo(map);
        marker.on('dragend', e => setCoords(e.target.getLatLng().lat, e.target.getLatLng().lng));
    }

    map.on('click', e => placePin(e.latlng.lat, e.latlng.lng));

    function placePin(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', e => setCoords(e.target.getLatLng().lat, e.target.getLatLng().lng));
        }
        setCoords(lat, lng);
    }

    function setCoords(lat, lng) {
        document.getElementById('latitude').value  = lat.toFixed(7);
        document.getElementById('longitude').value = lng.toFixed(7);
        document.getElementById('pin-status').innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
            </svg>
            ${lat.toFixed(5)}, ${lng.toFixed(5)}
        `;
        document.getElementById('clear-pin').classList.remove('hidden');
    }

    function clearPin() {
        if (marker) { map.removeLayer(marker); marker = null; }
        document.getElementById('latitude').value  = '';
        document.getElementById('longitude').value = '';
        document.getElementById('pin-status').textContent = 'No location set yet.';
        document.getElementById('clear-pin').classList.add('hidden');
    }
</script>
@endif
@endsection