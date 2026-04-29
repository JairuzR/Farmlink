<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
        <p class="mt-1 text-sm text-gray-600">
            Update your account details{{ auth()->user()->isFarmer() ? ', farm info, and social links' : '' }}.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Name --}}
        <div>
            <x-input-label for="name" value="Full Name" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-gray-800">
                        Your email is unverified.
                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900">
                            Resend verification email.
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">Verification link sent!</p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Phone --}}
        <div>
            <x-input-label for="phone" value="Phone Number" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                :value="old('phone', $user->phone)" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        {{-- Address --}}
        <div>
            <x-input-label for="address" value="Address" />
            <textarea id="address" name="address" rows="2"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('address', $user->address) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        {{-- ── FARMER ONLY ── --}}
        @if($user->isFarmer())
            <hr class="border-gray-200">
            <p class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Farm Details</p>

            <div>
                <x-input-label for="farm_name" value="Farm Name" />
                <x-text-input id="farm_name" name="farm_name" type="text" class="mt-1 block w-full"
                    :value="old('farm_name', $user->farm_name)" />
                <x-input-error class="mt-2" :messages="$errors->get('farm_name')" />
            </div>

            <div>
                <x-input-label for="bio" value="Short Bio" />
                <textarea id="bio" name="bio" rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('bio', $user->bio) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="latitude" value="Latitude" />
                    <x-text-input id="latitude" name="latitude" type="number" step="any"
                        class="mt-1 block w-full" :value="old('latitude', $user->latitude)" />
                    <x-input-error class="mt-2" :messages="$errors->get('latitude')" />
                </div>
                <div>
                    <x-input-label for="longitude" value="Longitude" />
                    <x-text-input id="longitude" name="longitude" type="number" step="any"
                        class="mt-1 block w-full" :value="old('longitude', $user->longitude)" />
                    <x-input-error class="mt-2" :messages="$errors->get('longitude')" />
                </div>
            </div>
            <p class="text-xs text-gray-400 -mt-4">
                Find your coordinates at <a href="https://www.latlong.net/" target="_blank" class="underline">latlong.net</a>.
            </p>

            {{-- Social Links --}}
            <hr class="border-gray-200">
            <p class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Social Links</p>
            <p class="text-xs text-gray-500 -mt-4">Add Facebook, Instagram, TikTok, or any page buyers can reach you on.</p>

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
                    <div class="social-row flex gap-2 items-start">
                        <select name="social_platform[{{ $i }}]"
                            class="mt-1 border-gray-300 rounded-md shadow-sm text-sm w-36 shrink-0">
                            @foreach(['Facebook','Instagram','TikTok','Twitter/X','YouTube','LinkedIn','Website','Other'] as $p)
                                <option value="{{ $p }}"
                                    {{ ($link['platform'] ?? $link->platform ?? '') === $p ? 'selected' : '' }}>
                                    {{ $p }}
                                </option>
                            @endforeach
                        </select>
                        <x-text-input type="text" name="social_label[{{ $i }}]"
                            placeholder="Label (e.g. Official Page)"
                            class="mt-1 w-full text-sm"
                            value="{{ $link['label'] ?? $link->label ?? '' }}" />
                        <x-text-input type="url" name="social_url[{{ $i }}]"
                            placeholder="https://..."
                            class="mt-1 w-full text-sm"
                            value="{{ $link['url'] ?? $link->url ?? '' }}" />
                        <button type="button" onclick="this.closest('.social-row').remove()"
                            class="mt-1 text-red-400 hover:text-red-600 text-lg leading-none px-1">✕</button>
                    </div>
                @endforeach
            </div>

            <button type="button" id="add-social-link"
                class="text-sm text-green-600 hover:text-green-800 font-medium">
                + Add another link
            </button>
        @endif
        {{-- ── END FARMER ONLY ── --}}

        <div class="flex items-center gap-4">
            <x-primary-button>Save</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-gray-600">Saved.</p>
            @endif
        </div>
    </form>
</section>

@if(auth()->user()->isFarmer())
<script>
    let rowIndex = {{ count($existingLinks ?? []) }};

    document.getElementById('add-social-link').addEventListener('click', () => {
        const container = document.getElementById('social-links-container');
        const platforms = ['Facebook','Instagram','TikTok','Twitter/X','YouTube','LinkedIn','Website','Other'];
        const options = platforms.map(p => `<option value="${p}">${p}</option>`).join('');

        container.insertAdjacentHTML('beforeend', `
            <div class="social-row flex gap-2 items-start">
                <select name="social_platform[${rowIndex}]"
                    class="mt-1 border-gray-300 rounded-md shadow-sm text-sm w-36 shrink-0">
                    ${options}
                </select>
                <input type="text" name="social_label[${rowIndex}]"
                    placeholder="Label (e.g. Official Page)"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" />
                <input type="url" name="social_url[${rowIndex}]"
                    placeholder="https://..."
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" />
                <button type="button" onclick="this.closest('.social-row').remove()"
                    class="mt-1 text-red-400 hover:text-red-600 text-lg leading-none px-1">✕</button>
            </div>
        `);
        rowIndex++;
    });
</script>
@endif