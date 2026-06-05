<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ZERP') }} - Aplikasi Keuangan</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-2">
            {{-- Left: Brand Panel --}}
            <div class="relative hidden lg:flex flex-col justify-between bg-gradient-to-br from-zerp-900 via-zerp-800 to-zerp-950 p-12 overflow-hidden">
                {{-- Decorative circles --}}
                <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-zerp-500/10 blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full bg-zerp-400/10 blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full bg-zerp-600/5 blur-3xl"></div>

                {{-- Grid pattern overlay --}}
                <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, rgba(255,255,255,0.8) 1px, transparent 1px); background-size: 30px 30px;"></div>

                {{-- Top section --}}
                <div class="relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-white/10 backdrop-blur-sm">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-white tracking-wider">ZERP</span>
                    </div>
                </div>

                {{-- Center content --}}
                <div class="relative z-10 max-w-md">
                    <h1 class="text-4xl font-extrabold text-white leading-tight mb-4">
                        Kelola Bisnis<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-zerp-300 to-zerp-100">Lebih Efisien</span>
                    </h1>
                    <p class="text-lg text-zerp-200 leading-relaxed">
                        Platform ERP terintegrasi untuk mengelola keuangan, penjualan, pembelian, dan inventaris bisnis Anda dalam satu sistem.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <div class="flex items-center gap-2 text-sm text-zerp-200">
                            <svg class="w-4 h-4 text-zerp-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Keuangan
                        </div>
                        <div class="flex items-center gap-2 text-sm text-zerp-200">
                            <svg class="w-4 h-4 text-zerp-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Penjualan
                        </div>
                        <div class="flex items-center gap-2 text-sm text-zerp-200">
                            <svg class="w-4 h-4 text-zerp-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Inventaris
                        </div>
                        <div class="flex items-center gap-2 text-sm text-zerp-200">
                            <svg class="w-4 h-4 text-zerp-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Pembelian
                        </div>
                    </div>
                </div>

                {{-- Bottom --}}
                <div class="relative z-10 text-sm text-zerp-400">
                    &copy; {{ date('Y') }} ZERP. All rights reserved.
                </div>
            </div>

            {{-- Right: Auth Form --}}
            <div class="flex items-center justify-center px-6 py-12 bg-gray-50 lg:bg-white">
                <div class="w-full max-w-md">
                    {{-- Mobile logo --}}
                    <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-zerp-600 to-zerp-800">
                            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-800">ZERP</span>
                    </div>

                    <div class="bg-white lg:bg-transparent rounded-2xl lg:rounded-none shadow-xl lg:shadow-none p-8 lg:p-0">
                        {{ $slot }}
                    </div>

                    {{-- Mobile footer --}}
                    <div class="lg:hidden mt-8 text-center text-xs text-gray-400">
                        &copy; {{ date('Y') }} ZERP. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
