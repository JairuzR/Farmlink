<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'FARMLINK')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white font-sans text-slate-900 antialiased">
        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        {{-- <span class="grid h-10 w-10 place-items-center rounded-lg bg-green-600 text-xl text-white">🌾</span> --}}
                        <img src="{{ asset('build/assets/images/logo.jpg') }}" alt="FARMLINK Logo" class="h-10 w-10 rounded-lg object-cover">
                        <span class="text-2xl font-bold text-green-800">FARMLINK</span>
                    </a>

                    <nav class="hidden items-center gap-6 text-sm text-slate-700 md:flex">
                        <a class="hover:text-green-700" href="{{ route('home') }}">Home</a>
                        <a class="hover:text-green-700" href="{{ route('marketplace') }}">Marketplace</a>
                        <a class="hover:text-green-700" href="{{ route('farmers') }}">Farmers</a>
                    </nav>
                </div>

                <div class="flex items-center gap-3 text-slate-700">

                    @auth
                        {{-- Cart icon (buyers only) --}}
                        @if(auth()->user()->isBuyer())
                            @php($cartCount = auth()->user()->cartItems()->count())
                            <a href="{{ route('cart') }}" class="relative rounded-full p-2 hover:bg-slate-100" aria-label="Cart">
                                @if($cartCount > 0)
                                    <span class="absolute -right-1 -top-1 grid h-5 w-5 place-items-center rounded-full bg-green-600 text-xs font-semibold text-white">{{ $cartCount }}</span>
                                @endif
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M6 6h15l-2 8H8L6 6Z" />
                                    <path d="M6 6 5 3H2" />
                                    <path d="M9 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" />
                                    <path d="M18 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" />
                                </svg>
                            </a>
                        @endif

                        @if(auth()->user()->isFarmer())
                            <a href="{{ route('orders.incoming') }}" class="hover:text-green-700">Orders</a>
                        @endif

                        {{-- User dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center gap-2 rounded-full p-2 hover:bg-slate-100"
                                aria-label="Account menu">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M20 21a8 8 0 0 0-16 0" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                <span class="hidden text-sm font-medium md:block">{{ auth()->user()->name }}</span>
                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute right-0 mt-2 w-64 rounded-xl border border-slate-200 bg-white py-1 shadow-lg z-50">

                                {{-- Role badge --}}
                                <div class="border-b border-slate-100 px-4 py-3">
                                    <p class="text-xs text-slate-500">Signed in as</p>
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                                    <span class="mt-1 inline-block rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">
                                        {{ ucfirst(auth()->user()->role) }}
                                    </span>
                                </div>

                                <div class="py-1">
                                    <a href="{{ route('dashboard') }}"
                                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                        </svg>
                                        Dashboard
                                    </a>
                                    <a href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        Profile Settings
                                    </a>
                                </div>

                                {{-- Buyer: prompt to also become a farmer --}}
                                @if(auth()->user()->isBuyer())
                                    <div class="border-t border-slate-100 py-1">
                                        <a href="{{ route('register') }}"
                                        class="flex items-center gap-3 px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                            Register as Farmer
                                        </a>
                                    </div>
                                @endif

                                {{-- Logout --}}
                                <div class="border-t border-slate-100 py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex w-full items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-400 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                                            </svg>
                                            Log Out
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>

                    @else
                        {{-- Logged out: show Login + Register --}}
                        <a href="{{ route('login') }}"
                        class="rounded-lg border border-green-600 px-4 py-2 text-sm font-semibold text-green-700 hover:bg-green-50">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main>
            @if (session('status'))
                <div class="border-b border-green-200 bg-green-50 px-4 py-3 text-center text-sm font-semibold text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="bg-slate-950 text-slate-300">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-4 lg:px-8">
                <div>
                    <div class="mb-4 flex items-center gap-2">
                        {{-- <span class="grid h-10 w-10 place-items-center rounded-lg bg-green-600 text-xl text-white">🌾</span> --}}
                        <img src="{{ asset('build/assets/images/logo.jpg') }}" alt="FARMLINK Logo" class="h-10 w-10 rounded-lg object-cover">
                        <span class="text-xl font-bold text-white">FARMLINK</span>
                    </div>
                    <p class="text-sm leading-6 text-slate-400">Connecting Filipino farmers directly to consumers for a sustainable future.</p>
                </div>
                <div>
                    <h3 class="mb-4 font-semibold text-white">Quick Links</h3>
                    <div class="grid gap-2 text-sm">
                        <a href="{{ route('home') }}">Home</a>
                        <a href="{{ route('marketplace') }}">Marketplace</a>
                        <a href="{{ route('farmers') }}">Farmers</a>
                        @guest
                            <a href="{{ route('register') }}">Register</a>
                        @endguest
                        @auth
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        @endauth
                    </div>
                </div>
                <div>
                    <h3 class="mb-4 font-semibold text-white">Support</h3>
                    <div class="grid gap-2 text-sm">
                        <span>Help Center</span>
                        <span>Seller Guide</span>
                        <span>Buyer Guide</span>
                        <span>FAQs</span>
                    </div>
                </div>
                <div>
                    <h3 class="mb-4 font-semibold text-white">Contact Us</h3>
                    <div class="grid gap-3 text-sm">
                        <span>farmlink@gmail.com</span>
                        <span>+63 123 456 7890</span>
                        <span>Valencia City, Bukidnon</span>
                    </div>
                </div>
            </div>
            <div class="border-t border-slate-800 py-6 text-center text-sm text-slate-500">
                © 2026 FARMLINK. All rights reserved. Built to empower Filipino farmers.
            </div>
        </footer>
    </body>
</html>
