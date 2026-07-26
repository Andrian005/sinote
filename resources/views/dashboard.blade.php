<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Today') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Quick Capture widget — always visible at the top of Dashboard --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                        Tangkap Ide Cepat
                    </h3>
                    <a
                        href="{{ route('inbox.index') }}"
                        class="text-xs text-blue-600 hover:text-blue-700 hover:underline"
                        wire:navigate
                    >
                        Buka Inbox →
                    </a>
                </div>
                <livewire:inbox.quick-capture />
            </div>

            {{-- Tasks widget — Tugas Hari Ini --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                        Tugas Hari Ini
                    </h3>
                    <a
                        href="{{ route('tasks.index') }}"
                        class="text-xs text-blue-600 hover:text-blue-700 hover:underline"
                        wire:navigate
                    >
                        Lihat Semua →
                    </a>
                </div>
                <livewire:tasks.task-list :limit="5" />
            </div>

            {{-- Projects widget — Projects & Goals --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                        Projects
                    </h3>
                    <a href="{{ route('projects.index') }}" class="text-xs text-blue-600 hover:text-blue-700 hover:underline" wire:navigate>
                        Lihat Semua →
                    </a>
                </div>
                <livewire:projects.project-list :limit="3" />
            </div>

            {{-- Placeholder for Habits and other modules --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-500 text-sm">
                    Agenda hari ini akan ditampilkan di sini setelah modul Habits selesai.
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
