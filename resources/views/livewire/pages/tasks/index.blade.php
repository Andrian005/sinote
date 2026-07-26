<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- New Task form --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">
                    Task Baru
                </h3>
                <livewire:tasks.task-form />
            </div>

            {{-- Task list with filter tabs --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <livewire:tasks.task-list />
            </div>

        </div>
    </div>
</x-app-layout>
