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
        Task berhasil disimpan.
    </div>

    <form wire:submit="save" novalidate>
        {{-- Title --}}
        <div class="mb-4">
            <label for="task-title" class="block text-sm font-medium text-gray-700 mb-1">
                Judul <span class="text-red-500" aria-hidden="true">*</span>
            </label>
            <input
                wire:model="title"
                id="task-title"
                type="text"
                maxlength="255"
                placeholder="Apa yang perlu dilakukan?"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror"
                aria-required="true"
                aria-describedby="task-title-error"
            />
            @error('title')
                <p id="task-title-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div class="mb-4">
            <label for="task-description" class="block text-sm font-medium text-gray-700 mb-1">
                Deskripsi
            </label>
            <textarea
                wire:model="description"
                id="task-description"
                rows="3"
                placeholder="Detail tambahan (opsional)"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
            ></textarea>
        </div>

        {{-- Priority & Due date (side by side) --}}
        <div class="mb-4 grid grid-cols-2 gap-4">
            <div>
                <label for="task-priority" class="block text-sm font-medium text-gray-700 mb-1">
                    Prioritas
                </label>
                <select
                    wire:model="priority"
                    id="task-priority"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="low">Rendah</option>
                    <option value="medium">Sedang</option>
                    <option value="high">Tinggi</option>
                </select>
            </div>

            <div>
                <label for="task-due-date" class="block text-sm font-medium text-gray-700 mb-1">
                    Tenggat Waktu
                </label>
                <input
                    wire:model="dueDate"
                    id="task-due-date"
                    type="date"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end">
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
            >
                <span wire:loading.remove>{{ $taskId ? 'Perbarui' : 'Simpan' }}</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
