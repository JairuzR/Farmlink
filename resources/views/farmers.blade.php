@extends('layouts.farmlink')

@section('title', 'Find Local Farmers | FARMLINK')

@section('content')
    <section class="bg-slate-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold text-slate-950">Find Local Farmers</h1>
                <p class="mt-3 text-slate-600">Browse nearby partner farms and connect directly with producers in your area.</p>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
                <div class="min-h-[620px] rounded-lg border border-slate-200 bg-emerald-50 p-6 shadow-sm">
                    <div class="relative h-full min-h-[560px] overflow-hidden rounded-lg bg-[linear-gradient(135deg,#dcfce7_25%,#f8fafc_25%,#f8fafc_50%,#dcfce7_50%,#dcfce7_75%,#f8fafc_75%)] bg-[length:56px_56px]">
                        @foreach ([[18,22], [34,38], [45,25], [58,50], [70,33], [78,66], [24,64], [52,75]] as [$left, $top])
                            <span class="absolute grid h-8 w-8 place-items-center rounded-full bg-green-600 text-white shadow-lg ring-4 ring-white" style="left: {{ $left }}%; top: {{ $top }}%;">⌖</span>
                        @endforeach
                    </div>
                </div>

                <aside class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-xl font-bold text-slate-950">Highlighted Farmers</h2>
                    <div class="mt-5 grid gap-4">
                        @foreach (['Juan Dela Cruz Farm', 'Green Valley Farm', 'Bukidnon Highlands', 'Reyes Family Farm'] as $farm)
                            <div class="rounded-lg border border-slate-200 p-4">
                                <h3 class="font-semibold text-slate-950">{{ $farm }}</h3>
                                <p class="mt-1 text-sm text-slate-500">Valencia City, Bukidnon</p>
                                <div class="mt-3 flex items-center justify-between text-sm">
                                    <span class="font-semibold text-green-700">4.9 rating</span>
                                    <a href="{{ route('marketplace') }}" class="font-semibold text-green-700">View produce</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
