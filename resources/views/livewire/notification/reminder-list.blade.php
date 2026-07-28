<div>
    @if ($reminders->isEmpty())
        <div class="py-6 text-center">
            <p class="text-sm text-gray-400">Tidak ada reminder aktif saat ini.</p>
        </div>
    @else
        <ul class="divide-y divide-gray-100" role="list">
            @foreach ($reminders as $reminder)
                @php
                    $isToday    = $reminder->scheduled_at->isToday() || $reminder->scheduled_at->isPast();
                    $isTomorrow = ! $isToday && $reminder->scheduled_at->isTomorrow();
                    $label      = $reminder->remindable?->title ?? 'Entitas dihapus';
                @endphp
                <li class="flex items-center justify-between gap-3 py-3" wire:key="reminder-{{ $reminder->id }}">
                    <div class="min-w-0 flex-1">
                        {{-- Entity title --}}
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $label }}</p>

                        {{-- Scheduled time --}}
                        <p class="mt-0.5 text-xs text-gray-400">
                            {{ $reminder->scheduled_at->translatedFormat('d M Y, H:i') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{-- Reminder type badge (H-1 / H) --}}
                        <span @class([
                            'inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full',
                            'bg-red-100 text-red-700'    => $isToday,
                            'bg-amber-100 text-amber-700' => $isTomorrow,
                            'bg-gray-100 text-gray-600'  => ! $isToday && ! $isTomorrow,
                        ])
                            aria-label="{{ $isToday ? 'Hari H' : ($isTomorrow ? 'H minus 1' : 'Mendatang') }}"
                        >
                            {{ $isToday ? 'H' : ($isTomorrow ? 'H-1' : 'Mendatang') }}
                        </span>

                        {{-- Urgency label (text alongside badge for accessibility) --}}
                        <span @class([
                            'text-xs font-medium',
                            'text-red-600'   => $isToday,
                            'text-amber-600' => $isTomorrow,
                            'text-gray-500'  => ! $isToday && ! $isTomorrow,
                        ])>
                            {{ $isToday ? 'Hari ini' : ($isTomorrow ? 'Besok' : '') }}
                        </span>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
