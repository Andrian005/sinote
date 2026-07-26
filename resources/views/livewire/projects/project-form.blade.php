<div
    x-data="{ saved: @entangle('saved') }"
    x-init="$watch('saved', value => { if (value) setTimeout(() => $wire.resetSaved(), 3000) })"
>
    @include('livewire.partials.save-success', ['message' => 'Project berhasil disimpan.'])

    <form wire:submit="save" novalidate>
        {{-- Title --}}
        <div class="mb-4">
            <label for="project-title" class="block text-sm font-medium text-gray-700 mb-1">
                Judul <span class="text-red-500" aria-hidden="true">*</span>
            </label>
            <input
                wire:model="title"
                id="project-title"
                type="text"
                maxlength="255"
                placeholder="Nama project"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror"
            />
            @error('title')
                <p class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        {{-- Goal + Due date --}}
        <div class="mb-4 grid grid-cols-2 gap-4">
            <div>
                <label for="project-goal" class="block text-sm font-medium text-gray-700 mb-1">Goal (opsional)</label>
                <select wire:model="goalId" id="project-goal"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Tanpa Goal —</option>
                    @foreach ($userGoals as $goal)
                        <option value="{{ $goal->id }}">{{ $goal->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="project-due-date" class="block text-sm font-medium text-gray-700 mb-1">Tenggat</label>
                <input
                    wire:model="dueDate"
                    id="project-due-date"
                    type="date"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
        </div>

        {{-- Description --}}
        <div class="mb-4">
            <label for="project-desc" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea
                wire:model="description"
                id="project-desc"
                rows="3"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
            ></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" wire:loading.attr="disabled"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">
                <span wire:loading.remove>{{ $projectId ? 'Perbarui' : 'Simpan' }}</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
