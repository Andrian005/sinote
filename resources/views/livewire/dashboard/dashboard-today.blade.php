<div>
    {{-- Stats bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-blue-700">{{ $stats['tasks'] }}</p>
            <p class="text-xs text-blue-500 mt-1 uppercase tracking-wide">Task Aktif</p>
        </div>
        <div class="bg-purple-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-purple-700">{{ $stats['projects'] }}</p>
            <p class="text-xs text-purple-500 mt-1 uppercase tracking-wide">Project Aktif</p>
        </div>
        <div class="bg-amber-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-amber-700">{{ $stats['inbox'] }}</p>
            <p class="text-xs text-amber-500 mt-1 uppercase tracking-wide">Di Inbox</p>
        </div>
        <div class="bg-orange-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-orange-700">{{ $remindersCount }}</p>
            <p class="text-xs text-orange-500 mt-1 uppercase tracking-wide">Reminder Aktif</p>
        </div>
    </div>

    {{-- Active Reminders widget --}}
    @if ($remindersCount > 0)
        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Reminder Aktif</h3>
                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-orange-100 text-orange-700">
                    {{ $remindersCount }} pending
                </span>
            </div>
            <livewire:notification.reminder-list :limit="5" />
        </div>
    @endif

    {{-- Quick Capture --}}
    <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Tangkap Ide Cepat</h3>
            <a href="{{ route('inbox.index') }}" class="text-xs text-blue-600 hover:underline" wire:navigate>
                Buka Inbox →
            </a>
        </div>
        <livewire:inbox.quick-capture />
    </div>

    {{-- Today's Tasks --}}
    <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Tugas Hari Ini</h3>
            <a href="{{ route('tasks.index') }}" class="text-xs text-blue-600 hover:underline" wire:navigate>
                Lihat Semua →
            </a>
        </div>

        @if ($todayTasks->isEmpty())
            <div class="py-8 text-center">
                <p class="text-2xl mb-2" aria-hidden="true">🎉</p>
                <p class="text-sm font-medium text-gray-600">Tidak ada task hari ini!</p>
                <p class="text-xs text-gray-400 mt-1">Semua task sudah selesai atau belum ada yang jatuh tempo.</p>
            </div>
        @else
            <ul class="divide-y divide-gray-100" role="list">
                @foreach ($todayTasks as $task)
                    <li class="py-3 flex items-center justify-between gap-3" wire:key="today-task-{{ $task->id }}">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm text-gray-900">{{ $task->title }}</span>

                                <span @class([
                                    'inline-flex px-1.5 py-0.5 text-xs font-medium rounded-full',
                                    $task->priority->badgeClass(),
                                ])>
                                    {{ $task->priority->label() }}
                                </span>

                                @if ($task->status->value === 'in_progress')
                                    <span class="inline-flex px-1.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                        Dikerjakan
                                    </span>
                                @endif
                            </div>

                            @if ($task->due_date)
                                @php $overdue = $task->due_date->isPast(); @endphp
                                <p @class([
                                    'mt-0.5 text-xs',
                                    'text-red-500 font-medium' => $overdue,
                                    'text-gray-400'            => ! $overdue,
                                ])>
                                    {{ $overdue ? 'Terlambat: ' : 'Hari ini: ' }}{{ $task->due_date->translatedFormat('d M Y') }}
                                </p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Active Projects --}}
    <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Projects</h3>
            <a href="{{ route('projects.index') }}" class="text-xs text-blue-600 hover:underline" wire:navigate>
                Lihat Semua →
            </a>
        </div>
        <livewire:projects.project-list :limit="3" />
    </div>

    {{-- Habits (placeholder) --}}
    <div class="bg-white shadow-sm sm:rounded-lg p-6">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Habits</h3>
        <div class="flex items-center gap-3 py-4 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714a2.25 2.25 0 01-.659 1.591L14.25 14.5m-4.5 0l-4.5 4.5M14.25 14.5l4.5 4.5M9.75 14.5h4.5" />
            </svg>
            <div>
                <p class="text-sm font-medium text-gray-500">Fitur Habit akan segera hadir</p>
                <p class="text-xs text-gray-400 mt-0.5">Tracking kebiasaan harian kamu akan muncul di sini.</p>
            </div>
        </div>
    </div>
</div>
