@php
    $tabs = ['active' => 'Aktif', 'completed' => 'Selesai', 'archived' => 'Arsip'];

    $emptyMessages = [
        'active'    => 'Tidak ada goal aktif saat ini.',
        'completed' => 'Belum ada goal yang selesai.',
        'archived'  => 'Tidak ada goal di arsip.',
    ];
@endphp

<div
    x-data="{ flash: @entangle('flash') }"
    x-init="$watch('flash', value => { if (value) setTimeout(() => $wire.clearFlash(), 3000) })"
>
    @include('livewire.partials.flash-message')

    @include('livewire.partials.filter-tabs', ['tabs' => $tabs])

    @if ($goals->isEmpty())
        <div class="py-10 text-center text-sm text-gray-400">
            {{ $emptyMessages[$filter] ?? '' }}
        </div>
    @else
        <ul class="divide-y divide-gray-100" role="list">
            @foreach ($goals as $goal)
                <li class="flex items-start justify-between gap-4 py-4" wire:key="goal-{{ $goal->id }}">
                    {{-- Goal info --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-medium text-gray-900">{{ $goal->title }}</span>
                            <span @class([
                                'px-2 py-0.5 text-xs rounded-full',
                                'bg-purple-100 text-purple-700' => $goal->goal_type->value === 'time_bound',
                                'bg-teal-100 text-teal-700'     => $goal->goal_type->value !== 'time_bound',
                            ])>
                                {{ $goal->goal_type->label() }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">
                            {{ $goal->projects_count }} project
                            @if ($goal->target_date)
                                · Tenggat: {{ $goal->target_date->translatedFormat('d M Y') }}
                            @endif
                        </p>
                    </div>

                    {{-- Action dropdown --}}
                    <div x-data="{ open: false }" class="relative flex-shrink-0">
                        @include('livewire.partials.action-dropdown-trigger')

                        <div
                            x-show="open"
                            x-transition
                            class="absolute right-0 z-10 mt-1 w-44 bg-white border border-gray-200 rounded-md shadow-lg"
                            role="menu"
                        >
                            @if ($goal->status->value === 'active')
                                <button type="button" wire:click="updateStatus('{{ $goal->id }}', 'completed')" @click="open = false"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50" role="menuitem">
                                    Selesaikan
                                </button>
                            @endif
                            @if ($goal->status->value === 'completed')
                                <button type="button" wire:click="updateStatus('{{ $goal->id }}', 'active')" @click="open = false"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50" role="menuitem">
                                    Buka Lagi
                                </button>
                            @endif
                            @if ($goal->status->value !== 'archived')
                                <div class="border-t border-gray-100"></div>
                                <button type="button" wire:click="archive('{{ $goal->id }}')" @click="open = false"
                                    class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50" role="menuitem">
                                    Arsipkan
                                </button>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        @if ($goals->hasPages())
            <div class="mt-4 border-t border-gray-100 pt-4">{{ $goals->links() }}</div>
        @endif
    @endif
</div>
