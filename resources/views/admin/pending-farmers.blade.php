@extends('layouts.farmlink')

@section('title', 'Pending Farmers | Admin')

@section('content')
<section class="py-12 bg-emerald-50 min-h-screen">
    <div class="mx-auto max-w-5xl px-4">
        <h1 class="text-3xl font-bold text-slate-900 mb-6">Pending Farmer Approvals</h1>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if($farmers->isEmpty())
            <p class="text-gray-500">No pending farmers. All caught up! 🎉</p>
        @else
            <div class="space-y-4">
                @foreach($farmers as $farmer)
                <div class="bg-white rounded-xl shadow p-6 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex-1">
                        <h2 class="text-lg font-semibold text-slate-800">{{ $farmer->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $farmer->email }} · {{ $farmer->phone }}</p>
                        <p class="text-sm text-gray-600 mt-1"><span class="font-medium">Farm:</span> {{ $farmer->farm_name }}</p>
                        <p class="text-sm text-gray-600"><span class="font-medium">Address:</span> {{ $farmer->address }}</p>
                        @if($farmer->bio)
                            <p class="text-sm text-gray-600 mt-1">{{ $farmer->bio }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">Registered {{ $farmer->created_at->diffForHumans() }}</p>

                        @if($farmer->farmer_id_path)
                            <a href="{{ route('admin.farmers.id', $farmer) }}"
                               target="_blank"
                               class="inline-block mt-2 text-sm text-emerald-600 hover:underline">
                                View Government ID ↗
                            </a>
                        @endif
                    </div>

                    <div class="flex gap-3 shrink-0">
                        <form method="POST" action="{{ route('admin.farmers.approve', $farmer) }}">
                            @csrf @method('PATCH')
                            <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700">
                                Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.farmers.reject', $farmer) }}"
                              onsubmit="return confirm('Reject and delete {{ $farmer->name }}?')">
                            @csrf @method('DELETE')
                            <button class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600">
                                Reject
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