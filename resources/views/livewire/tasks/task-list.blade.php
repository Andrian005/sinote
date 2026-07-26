<div
    x-data="{ flash: @entangle('flash') }"
    x-init="$watch('flash', value => { if (value) setTimeout(() => $wire.clearFlash(), 3000) })"
>
    {{-- Flash message --}}
    @if ($flash)
        <div
            x-show="flash"
            x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @class([
                'mb-4 px-4 py-2 text-sm rounded-md border',
                'text-green-800 bg-green-100 border-green-200' => ! $flashIsError,
                'text-red-800 bg-red-100 border-red-200' => $flashIsError,
            ])
            role="status"
            aria-live="polite"
        >
            {{ $flash }}
        </div>
    @endif

    {{-- Filter tabs (hidden when used as dashboard widget) --}}
    @if ($limit === 0)
        <div class="flex gap-1 mb-4 border-b border-gray-200">
            @foreach (['active' => 'Aktif', 'done' => 'Selesai', 'archived' => 'Arsip'] as $key => $label)
                <button
                    wire:click="$set('filter', '{{ $key }}')"
                    @class([
                        'px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors',
                        'border-blue-600 text-blue-600' => $filter === $key,
                        'border-transparent text-gray-500 hover:text-gray-700' => $filter !== $key,
                    ])
                    type="button"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    @endif

    {{-- Empty state --}}
    @if ($tasks->isEmpty())
        <div class="py-10 text-center">
            <p class="text-sm text-gray-400">
                @if ($filter === 'active') Tidak ada task aktif saat ini.
                @elseif ($filter === 'done') Belum ada task yang selesai.
                @else Tidak ada task di arsip.
                @endif
            </p>
        </div>
    @else
        <ul class="divide-y divide-gray-100" role="list">
            @foreach ($tasks as $task)
                <li class="flex items-start justify-between gap-4 py-4" wire:key="task-{{ $task->id }}">
                    {{-- Task info --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-medium text-gray-900">{{ $task->title }}</span>

                            {{-- Priority badge --}}
                            <span @class([
                                'inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full',
                                $task->priority->badgeClass(),
                            ])>
                                {{ $task->priority->label() }}
                            </span>

                            {{-- Status badge --}}
                            <span @class([
                                'inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full',
                                'bg-blue-100 text-blue-700' => $task->status->value === 'in_progress',
                                'bg-green-100 text-green-700' => $task->status->value === 'done',
                                'bg-gray-100 text-gray-600' => in_array($task->status->value, ['todo', 'archived']),
                            ])>
                                {{ match($task->status->value) {
                                    'todo' => 'Belum mulai',
                                    'in_progress' => 'Sedang dikerjakan',
                                    'done' => 'Selesai',
                                    'archived' => 'Arsip',
                                } }}
                            </span>
                        </div>

                        {{-- Due date --}}
                        @if ($task->due_date)
                            <p @class([
                                'mt-1 text-xs',
                                'text-red-500 font-medium' => $task->due_date->isPast() && ! in_array($task->status->value, ['done', 'archived']),
                                'text-gray-400' => ! ($task->due_date->isPast() && ! in_array($task->status->value, ['done', 'archived'])),
                            ])>
                                {{ $task->due_date->isPast() && ! in_array($task->status->value, ['done', 'archived']) ? 'Terlambat: ' : 'Tenggat: ' }}
                                {{ $task->due_date->translatedFormat('d M Y') }}
                            </p>
                        @endif
                    </div>

                    {{-- Action dropdown --}}
                    <div x-data="{ open: false }" class="relative flex-shrink-0">
                        <button
                            @click="open = !open"
                            @click.outside="open = false"
                            type="button"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :aria-expanded="open"
                        >
                            Aksi
                            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition
                            class="absolute right-0 z-10 mt-1 w-44 bg-white border border-gray-200 rounded-md shadow-lg"
                            role="menu"
                        >
                            @if ($task->status->value === 'todo')
                                <button type="button" wire:click="updateStatus('{{ $task->id }}', 'in_progress')" @click="open=false"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50" role="menuitem">
                                    Mulai
                                </button>
                            @endif

                            @if ($task->status->value === 'in_progress')
                                <button type="button" wire:click="updateStatus('{{ $task->id }}', 'todo')" @click="open=false"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50" role="menuitem">
                                    Tunda
                                </button>
                                <button type="button" wire:click="updateStatus('{{ $task->id }}', 'done')" @click="open=false"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50" role="menuitem">
                                    Selesai
                                </button>
                            @endif

                            @if ($task->status->value === 'done')
                                <button type="button" wire:click="updateStatus('{{ $task->id }}', 'todo')" @click="open=false"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50" role="menuitem">
                                    Buka Lagi
                                </button>
                            @endif

                            @if ($task->status->value !== 'archived')
                                <div class="border-t border-gray-100"></div>
                                <button type="button" wire:click="archive('{{ $task->id }}')" @click="open=false"
                                    class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50" role="menuitem">
                                    Arsipkan
                                </button>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Pagination (only when not in widget mode) --}}
        @if ($limit === 0 && method_exists($tasks, 'hasPages') && $tasks->hasPages())
            <div class="mt-6 border-t border-gray-100 pt-4">
                {{ $tasks->links() }}
            </div>
        @endif
    @endif
</div>
