<div class="tag-input-container">
    {{-- Attached Tags Display --}}
    @if($this->attachedTags->isNotEmpty())
        <div class="flex flex-wrap gap-2 mb-3">
            @foreach($this->attachedTags as $tag)
                <span class="inline-flex items-center gap-1 px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full">
                    <span>{{ $tag->name }}</span>
                    <button 
                        type="button"
                        wire:click="detachTag('{{ $tag->id }}')"
                        class="text-blue-600 hover:text-blue-800 focus:outline-none"
                        aria-label="Remove tag {{ $tag->name }}"
                    >
                        ×
                    </button>
                </span>
            @endforeach
        </div>
    @endif

    {{-- Tag Input with Autocomplete --}}
    <div class="relative">
        <input 
            type="text"
            wire:model.live="searchQuery"
            wire:keydown.enter="createAndAttachTag"
            placeholder="Add tags..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >

        {{-- Autocomplete Dropdown --}}
        @if(strlen($searchQuery) >= 1 && $this->availableTags->isNotEmpty())
            <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                @foreach($this->availableTags as $tag)
                    <button
                        type="button"
                        wire:click="attachTag('{{ $tag->id }}')"
                        class="w-full px-3 py-2 text-left hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
                    >
                        <span class="text-sm">{{ $tag->name }}</span>
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Create New Tag Hint --}}
        @if(strlen($searchQuery) >= 1 && $this->availableTags->isEmpty())
            <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg">
                <div class="px-3 py-2 text-sm text-gray-600">
                    Press <kbd class="px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded">Enter</kbd> to create "<strong>{{ strtolower($searchQuery) }}</strong>"
                </div>
            </div>
        @endif
    </div>
</div>
