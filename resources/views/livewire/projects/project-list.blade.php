<div
    x-data="{ flash: @entangle('flash') }"
    x-init="$watch('flash', value => { if (value) setTimeout(() => $wire.clearFlash(), 3000) })"
>
    @if ($flash)
        <div x-show="flash" x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            @class(['mb-4 px-4 py-2 text-sm rounded-md border',
                'text-green-800 bg-green-100 border-green-200' => ! $flashIsError,
                'text-red-800 bg-red-100 border-red-200' => $flashIsError])
            role="status" aria-live="polite">{{ $flash }}</div>
    @endif

    {{-- Filter tabs (hidden in widget mode) --}}
    @if ($limit === 0)
        <div class="flex gap-1 mb-4 border-b border-gray-200">
            @foreach (['active' => 'Aktif', 'completed' => 'Selesai', 'archived' => 'Arsip'] as $key => $label)
                <button wire:click="$set('filter', '{{ $key }}')" type="button"
                    @class(['px-4 py-2 text-sm font-medium border-b-2 -mb-px',
                        'border-blue-600 text-blue-600' => $filter === $key,
                        'border-transparent text-gray-500 hover:text-gray-700' => $filter !== $key])>
                    {{ $label }}
                </button>
            @endforeach
        </div>
    @endif

    @if ($projects->isEmpty())
        <div class="py-10 text-center text-sm text-gray-400">
            @if ($filter === 'active') Tidak ada project aktif saat ini.
            @elseif ($filter === 'completed') Belum ada project yang selesai.
            @else Tidak ada project di arsip.
            @endif
        </div>
    @else
        <ul class="divide-y divide-gray-100" role="list">
            @foreach ($projects as $project)
                <li class="py-4" wire:key="project-{{ $project->id }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $project->title }}</p>
                            <p class="mt-0.5 text-xs text-gray-400">
                                {{ $project->tasks_count }} task
                                @if ($project->goal)
                                    · <span class="text-purple-600">{{ $project->goal->title }}</span>
                                @endif
                            </p>

                            {{-- Progress bar --}}
                            <div class="mt-2 flex items-center gap-2">
                                <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full transition-all"
                                        style="width: {{ $project->progress }}%"
                                        role="progressbar"
                                        aria-valuenow="{{ $project->progress }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                                <span class="text-xs text-gray-500 w-8 text-right">{{ $project->progress }}%</span>
                            </div>
                        </div>

                        @if ($limit === 0)
                            <div x-data="{ open: false }" class="relative flex-shrink-0">
                                <button @click="open = !open" @click.outside="open = false" type="button"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                    :aria-expanded="open">
                                    Aksi
                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div x-show="open" x-transition class="absolute right-0 z-10 mt-1 w-44 bg-white border border-gray-200 rounded-md shadow-lg" role="menu">
                                    @if ($project->status->value === 'active')
                                        <button type="button" wire:click="updateStatus('{{ $project->id }}', 'completed')" @click="open=false"
                                            class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50" role="menuitem">Selesaikan</button>
                                    @endif
                                    @if ($project->status->value === 'completed')
                                        <button type="button" wire:click="updateStatus('{{ $project->id }}', 'active')" @click="open=false"
                                            class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50" role="menuitem">Buka Lagi</button>
                                    @endif
                                    @if ($project->status->value !== 'archived')
                                        <div class="border-t border-gray-100"></div>
                                        <button type="button" wire:click="archive('{{ $project->id }}')" @click="open=false"
                                            class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50" role="menuitem">Arsipkan</button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>

        @if ($limit === 0 && method_exists($projects, 'hasPages') && $projects->hasPages())
            <div class="mt-4 border-t border-gray-100 pt-4">{{ $projects->links() }}</div>
        @endif
    @endif
</div>
