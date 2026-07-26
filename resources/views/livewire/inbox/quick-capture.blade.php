<div
    x-data="{ saved: @entangle('saved') }"
    x-init="$watch('saved', value => { if (value) setTimeout(() => $wire.resetSaved(), 3000) })"
>
    {{-- Success flash --}}
    <div
        x-show="saved"
        x-transition:leave="transition ease-in duration-500"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="mb-3 px-4 py-2 text-sm text-green-800 bg-green-100 border border-green-200 rounded-md"
        role="status"
        aria-live="polite"
    >
        Item berhasil disimpan ke Inbox.
    </div>

    {{-- Capture form --}}
    <form wire:submit="save" novalidate>
        <div class="relative">
            <textarea
                wire:model="content"
                id="quick-capture-content"
                rows="4"
                maxlength="5000"
                placeholder="Tangkap ide cepat..."
                class="w-full px-4 py-3 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('content') border-red-400 focus:ring-red-400 @enderror"
                aria-label="Quick capture content"
                aria-describedby="quick-capture-error quick-capture-counter"
            ></textarea>

            {{-- Character counter --}}
            <span
                id="quick-capture-counter"
                class="absolute bottom-2 right-3 text-xs text-gray-400 select-none"
                aria-live="polite"
            >
                {{ strlen($content) }}/5000
            </span>
        </div>

        @error('content')
            <p id="quick-capture-error" class="mt-1 text-xs text-red-600" role="alert">
                {{ $message }}
            </p>
        @enderror

        <div class="mt-3 flex justify-end">
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span wire:loading.remove>Simpan</span>
                <span wire:loading class="inline-flex items-center gap-1">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Menyimpan...
                </span>
            </button>
        </div>
    </form>
</div>
