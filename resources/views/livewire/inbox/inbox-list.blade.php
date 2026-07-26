<div
    x-data="{ flash: @entangle('flash') }"
    x-init="$watch('flash', value => { if (value) setTimeout(() => $wire.clearFlash(), 3000) })"
>
    @include('livewire.partials.flash-message')

    @if ($inboxItems->isEmpty())
        <div class="py-16 text-center">
            <svg class="mx-auto mb-4 h-12 w-12 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-base font-medium text-gray-700">Inbox kosong — semua sudah tertata!</p>
            <p class="mt-1 text-sm text-gray-400">Gunakan Quick Capture untuk mencatat ide baru.</p>
        </div>
    @else
        <ul class="divide-y divide-gray-100" role="list" aria-label="Daftar Inbox">
            @foreach ($inboxItems as $item)
                <li class="flex items-start justify-between gap-4 py-4" wire:key="inbox-{{ $item->id }}">
                    {{-- Content --}}
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-900 leading-relaxed">
                            {{ Str::limit($item->content, 200) }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400">
                            {{ $item->created_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Action dropdown --}}
                    <div x-data="{ open: false }" class="relative flex-shrink-0">
                        @include('livewire.partials.action-dropdown-trigger')

                        <div
                            x-show="open"
                            x-transition
                            class="absolute right-0 z-10 mt-1 w-48 bg-white border border-gray-200 rounded-md shadow-lg"
                            role="menu"
                            aria-orientation="vertical"
                        >
                            <button type="button" wire:click="triage('{{ $item->id }}', 'task')" @click="open = false"
                                class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 focus:bg-gray-50 focus:outline-none" role="menuitem">
                                Jadikan Task
                            </button>
                            <button type="button" wire:click="triage('{{ $item->id }}', 'note')" @click="open = false"
                                class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 focus:bg-gray-50 focus:outline-none" role="menuitem">
                                Jadikan Note
                            </button>
                            <div class="border-t border-gray-100"></div>
                            <button type="button" wire:click="discard('{{ $item->id }}')" @click="open = false"
                                class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 focus:bg-red-50 focus:outline-none" role="menuitem">
                                Hapus
                            </button>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        @if ($inboxItems->hasPages())
            <div class="mt-6 border-t border-gray-100 pt-4">
                {{ $inboxItems->links() }}
            </div>
        @endif
    @endif
</div>
