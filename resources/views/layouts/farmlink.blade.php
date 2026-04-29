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
                        <span class="grid h-10 w-10 place-items-center rounded-lg bg-green-600 text-xl text-white">🌾</span>
                        <span class="text-2xl font-bold text-green-800">FARMLINK</span>
                    </a>

                    <nav class="hidden items-center gap-6 text-sm text-slate-700 md:flex">
                        <a class="hover:text-green-700" href="{{ route('home') }}">Home</a>
                        <a class="hover:text-green-700" href="{{ route('marketplace') }}">Marketplace</a>
                        <a class="hover:text-green-700" href="{{ route('farmers') }}">Farmers</a>
                    </nav>
                </div>

                <div class="flex items-center gap-3 text-slate-700">
                    @php($cartCount = array_sum(session('cart', [])))
                    <a href="{{ route('cart') }}" class="relative rounded-full p-2 hover:bg-slate-100" aria-label="Cart">
                        @if ($cartCount > 0)
                            <span class="absolute -right-1 -top-1 grid h-5 w-5 place-items-center rounded-full bg-green-600 text-xs font-semibold text-white">{{ $cartCount }}</span>
                        @endif
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M6 6h15l-2 8H8L6 6Z" />
                            <path d="M6 6 5 3H2" />
                            <path d="M9 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" />
                            <path d="M18 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" />
                        </svg>
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full p-2 hover:bg-slate-100" aria-label="Account">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M20 21a8 8 0 0 0-16 0" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg border border-green-600 px-4 py-2 text-sm font-semibold text-green-700 hover:bg-green-50">Login</a>
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
                        <span class="grid h-10 w-10 place-items-center rounded-lg bg-green-600 text-xl text-white">🌾</span>
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
                        <a href="{{ route('register') }}">Register</a>
                        <a href="{{ route('dashboard') }}">Farmer Dashboard</a>
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
