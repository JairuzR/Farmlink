@extends('layouts.farmlink')

@section('title', 'Farmer Approvals | Admin')

@section('content')
<section class="bg-slate-50 min-h-screen py-10">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-950">Admin Panel</h1>
                <p class="mt-1 text-slate-500">Farmer approval requests</p>
            </div>
            <span class="rounded-full bg-green-100 px-4 py-1.5 text-sm font-semibold text-green-700">
                {{ $farmers->count() }} pending
            </span>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if($farmers->isEmpty())
            <div class="rounded-xl bg-white py-16 text-center shadow-sm ring-1 ring-slate-200">
                <h2 class="mt-4 text-xl font-bold text-slate-800">All caught up!</h2>
                <p class="mt-2 text-slate-500">No pending farmer applications right now.</p>
            </div>
        @else
            <div class="space-y-5">
                @foreach($farmers as $farmer)
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">

                    {{-- Farmer info --}}
                    <div class="p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">{{ $farmer->name }}</h2>
                                <p class="mt-0.5 text-sm text-slate-500">
                                    {{ $farmer->email }}
                                    @if($farmer->phone) · {{ $farmer->phone }} @endif
                                </p>
                            </div>
                            <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                Pending Review
                            </span>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2 text-sm">
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Farm Name</p>
                                <p class="font-semibold text-slate-800">{{ $farmer->farm_name ?? '—' }}</p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Address</p>
                                <p class="font-semibold text-slate-800">{{ $farmer->address ?? '—' }}</p>
                            </div>
                            @if($farmer->bio)
                            <div class="rounded-lg bg-slate-50 p-3 sm:col-span-2">
                                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Bio</p>
                                <p class="text-slate-700">{{ $farmer->bio }}</p>
                            </div>
                            @endif
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-4">
                            <span class="text-xs text-slate-400">
                                Applied {{ $farmer->created_at->diffForHumans() }}
                            </span>
                            @if($farmer->farmer_id_path)
                                <a href="{{ route('admin.farmers.id', $farmer) }}" target="_blank"
                                   class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    View Government ID ↗
                                </a>
                            @else
                                <span class="text-xs text-red-400">⚠ No ID uploaded</span>
                            @endif
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
                        <form method="POST" action="{{ route('admin.farmers.reject', $farmer) }}"
                              onsubmit="return confirm('Reject and permanently delete {{ addslashes($farmer->name) }}\'s application?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="rounded-lg border border-red-200 bg-white px-5 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">
                                ✕ Reject
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.farmers.approve', $farmer) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="rounded-lg bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                ✓ Approve Farmer
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection