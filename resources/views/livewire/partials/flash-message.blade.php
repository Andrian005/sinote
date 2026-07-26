{{--
    Flash message partial for list components.
    Variables expected from parent Livewire scope:
      $flash        — string|null
      $flashIsError — bool
--}}
@if ($flash)
    <div
        x-show="flash"
        x-transition:leave="transition ease-in duration-500"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @class([
            'mb-4 px-4 py-2 text-sm rounded-md border',
            'text-green-800 bg-green-100 border-green-200' => ! $flashIsError,
            'text-red-800 bg-red-100 border-red-200'       => $flashIsError,
        ])
        role="status"
        aria-live="polite"
    >
        {{ $flash }}
    </div>
@endif
