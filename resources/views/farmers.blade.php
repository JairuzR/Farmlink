@extends('layouts.farmlink')

@section('title', 'Find Local Farmers | FARMLINK')

@section('content')
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-slate-950">Find Local Farmers</h1>
            <p class="mt-3 text-slate-600">Browse nearby partner farms and connect directly with producers in your area.</p>
        </div>

        @if($farmers->isEmpty())
            <div class="rounded-lg bg-white p-10 text-center shadow-sm ring-1 ring-slate-200">
                <p class="text-slate-500">No farmers with map locations yet. Check back soon!</p>
            </div>
        @else
        <div class="grid gap-6 lg:grid-cols-[1fr_360px]">

            {{-- Map --}}
            <div class="min-h-[620px] rounded-lg overflow-hidden shadow-sm ring-1 ring-slate-200">
                <div id="farmer-map" class="h-full min-h-[620px] w-full"></div>
            </div>

            {{-- Farmer list sidebar --}}
            <aside class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 overflow-y-auto max-h-[620px]">
                <h2 class="text-xl font-bold text-slate-950">Registered Farmers</h2>
                <p class="text-sm text-slate-500 mt-1">{{ $farmers->count() }} farmers on the map</p>

                <div class="mt-4 space-y-3">
                    @foreach($farmers as $farmer)
                    <div class="farmer-card rounded-lg border border-slate-200 p-4 cursor-pointer hover:border-green-400 hover:bg-green-50 transition"
                         data-lat="{{ $farmer->latitude }}"
                         data-lng="{{ $farmer->longitude }}"
                         data-id="{{ $farmer->id }}">
                        <h3 class="font-semibold text-slate-900">{{ $farmer->name }}</h3>
                        @if($farmer->farm_name)
                            <p class="text-sm text-green-700">🌾 {{ $farmer->farm_name }}</p>
                        @endif
                        <div class="mt-2 flex items-center justify-between text-sm">
                            <span class="text-slate-500">
                                {{ $farmer->products_count }} active product{{ $farmer->products_count != 1 ? 's' : '' }}
                            </span>
                            @if($farmer->products_avg_rating)
                                <span class="font-semibold text-yellow-600">
                                    ★ {{ number_format($farmer->products_avg_rating, 1) }}
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('marketplace', ['search' => $farmer->farm_name ?? $farmer->name]) }}"
                           class="mt-2 inline-block text-xs font-semibold text-green-700 hover:underline"
                           onclick="event.stopPropagation()">
                            View produce →
                        </a>
                    </div>
                    @endforeach
                </div>
            </aside>
        </div>
        @endif
    </div>
</section>

{{-- Leaflet CSS + JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>

// Pass farmer data from PHP to JS
const farmers = @json($farmersJson);

// Init map centered on Cagayan de Oro (adjust if needed)
const map = L.map('farmer-map').setView([8.4542, 124.6319], 10);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

// Green pin icon
const greenIcon = L.divIcon({
    html: `<div style="
        background:#16a34a;
        width:32px;height:32px;
        border-radius:50% 50% 50% 0;
        transform:rotate(-45deg);
        border:3px solid #fff;
        box-shadow:0 2px 6px rgba(0,0,0,.3)
    "></div>`,
    className: '',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -36],
});

const markers = {};

farmers.forEach(f => {
    const marker = L.marker([f.lat, f.lng], { icon: greenIcon })
        .addTo(map)
        .bindPopup(`
            <div style="min-width:180px">
                <strong style="font-size:14px">${f.name}</strong><br>
                ${f.farm_name ? `<span style="color:#15803d">🌾 ${f.farm_name}</span><br>` : ''}
                ${f.rating ? `<span style="color:#ca8a04">★ ${f.rating}</span> &nbsp;` : ''}
                <span style="color:#64748b">${f.products} product${f.products != 1 ? 's' : ''}</span><br>
                <a href="${f.url}" style="color:#16a34a;font-weight:600;font-size:13px">View produce →</a>
            </div>
        `);
    markers[f.id] = marker;
});

// Clicking a sidebar card pans to that farmer's marker
document.querySelectorAll('.farmer-card').forEach(card => {
    card.addEventListener('click', () => {
        const id  = parseInt(card.dataset.id);
        const lat = parseFloat(card.dataset.lat);
        const lng = parseFloat(card.dataset.lng);
        map.flyTo([lat, lng], 14, { duration: 1 });
        markers[id]?.openPopup();
    });
});

// If only 1 farmer, open their popup automatically
if (farmers.length === 1) {
    markers[farmers[0].id]?.openPopup();
}
</script>
@endsection