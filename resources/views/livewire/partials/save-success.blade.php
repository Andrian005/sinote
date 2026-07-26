{{--
    Save-success banner for form components.
    Variables expected from parent Livewire scope:
      $message — string  (the confirmation text to display)
    Alpine state expected in parent: `saved` (bool)
--}}
<div
    x-show="saved"
    x-transition:leave="transition ease-in duration-500"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="mb-3 px-4 py-2 text-sm text-green-800 bg-green-100 border border-green-200 rounded-md"
    role="status"
    aria-live="polite"
>
    {{ $message }}
</div>
