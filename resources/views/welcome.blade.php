<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SINOTE - Smart Notes</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
            <nav class="container mx-auto px-6 py-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        SINOTE
                    </h1>
                    @if (Route::has('login'))
                        <div class="space-x-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 text-gray-700 font-medium hover:text-blue-600 transition-colors">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="px-6 py-2.5 text-gray-700 font-medium hover:text-blue-600 transition-colors">
                                    Masuk
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/30">
                                        Daftar
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </nav>

            <main class="container mx-auto px-6 py-20">
                <div class="max-w-4xl mx-auto text-center">
                    <h2 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                        Kelola Catatan Anda dengan <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Lebih Mudah</span>
                    </h2>
                    <p class="text-xl text-gray-600 mb-12 max-w-2xl mx-auto">
                        Sistem manajemen catatan yang membantu Anda mengorganisir ide, tugas, dan proyek dengan efisien.
                    </p>
                    <div class="flex gap-4 justify-center">
                        @guest
                            <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/30 text-lg">
                                Mulai Sekarang
                            </a>
                            <a href="{{ route('login') }}" class="px-8 py-4 bg-white text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all border border-gray-200 text-lg">
                                Masuk
                            </a>
                        @else
                            <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/30 text-lg">
                                Buka Dashboard
                            </a>
                        @endguest
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-8 mt-24 max-w-5xl mx-auto">
                    <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl border border-white/20 shadow-xl">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Organisasi Mudah</h3>
                        <p class="text-gray-600">Kategorikan dan kelola catatan Anda dengan sistem yang intuitif.</p>
                    </div>

                    <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl border border-white/20 shadow-xl">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Pencarian Cepat</h3>
                        <p class="text-gray-600">Temukan catatan yang Anda butuhkan dengan pencarian yang powerful.</p>
                    </div>

                    <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl border border-white/20 shadow-xl">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Aman & Private</h3>
                        <p class="text-gray-600">Data Anda tersimpan dengan aman dan tetap privat.</p>
                    </div>
                </div>
            </main>

            <footer class="container mx-auto px-6 py-8 mt-20 border-t border-gray-200">
                <div class="text-center text-gray-600">
                    <p>&copy; {{ date('Y') }} SINOTE. All rights reserved.</p>
                </div>
            </footer>
        </div>
    </body>
</html>
