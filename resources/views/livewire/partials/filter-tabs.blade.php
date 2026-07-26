{{--
    Reusable filter-tab bar for list components.
    Variables expected from parent Livewire scope:
      $filter — string  (active filter key)
      $tabs   — array   (associative: ['key' => 'Label', ...])
--}}
<div class="flex gap-1 mb-4 border-b border-gray-200" role="tablist">
    @foreach ($tabs as $key => $label)
        <button
            wire:click="$set('filter', '{{ $key }}')"
            type="button"
            role="tab"
            aria-selected="{{ $filter === $key ? 'true' : 'false' }}"
            @class([
                'px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors',
                'border-blue-600 text-blue-600'                   => $filter === $key,
                'border-transparent text-gray-500 hover:text-gray-700' => $filter !== $key,
            ])
        >
            {{ $label }}
        </button>
    @endforeach
</div>
