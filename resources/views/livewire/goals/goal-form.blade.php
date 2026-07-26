<div
    x-data="{ saved: @entangle('saved') }"
    x-init="$watch('saved', value => { if (value) setTimeout(() => $wire.resetSaved(), 3000) })"
>
    @include('livewire.partials.save-success', ['message' => 'Goal berhasil disimpan.'])

    <form wire:submit="save" novalidate>
        {{-- Title --}}
        <div class="mb-4">
            <label for="goal-title" class="block text-sm font-medium text-gray-700 mb-1">
                Judul <span class="text-red-500" aria-hidden="true">*</span>
            </label>
            <input
                wire:model="title"
                id="goal-title"
                type="text"
                maxlength="255"
                placeholder="Apa yang ingin kamu capai?"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror"
                aria-required="true"
            />
            @error('title')
                <p class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        {{-- Goal type --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Goal</label>
            @if ($isEditMode)
                <p class="px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md text-gray-500">
                    {{ $goalType === 'time_bound' ? 'Berujung (ada tenggat)' : 'Berkelanjutan' }}
                    <span class="text-xs ml-1">(tidak bisa diubah)</span>
                </p>
                <input type="hidden" wire:model="goalType" />
            @else
                <select wire:model.live="goalType" id="goal-type"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="ongoing">Berkelanjutan</option>
                    <option value="time_bound">Berujung (ada tenggat)</option>
                </select>
            @endif
        </div>

        {{-- Target date (only for time_bound goals) --}}
        @if ($goalType === 'time_bound')
            <div class="mb-4">
                <label for="goal-target-date" class="block text-sm font-medium text-gray-700 mb-1">
                    Tenggat Target <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input
                    wire:model="targetDate"
                    id="goal-target-date"
                    type="date"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('targetDate') border-red-400 @enderror"
                />
                @error('targetDate')
                    <p class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>
                @enderror
            </div>
        @endif

        {{-- Description --}}
        <div class="mb-4">
            <label for="goal-desc" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea
                wire:model="description"
                id="goal-desc"
                rows="3"
                placeholder="Detail tambahan (opsional)"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
            ></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" wire:loading.attr="disabled"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">
                <span wire:loading.remove>{{ $goalId ? 'Perbarui' : 'Simpan' }}</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
