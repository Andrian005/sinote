<?php

namespace App\Livewire\Shared;

use App\Domain\Shared\Actions\AttachTag;
use App\Domain\Shared\Actions\CreateTag;
use App\Domain\Shared\Actions\DetachTag;
use App\Domain\Shared\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TagInput extends Component
{
    public string $taggableType;

    public string $taggableId;

    public string $searchQuery = '';

    public function mount(string $taggableType, string $taggableId): void
    {
        $this->taggableType = $taggableType;
        $this->taggableId = $taggableId;
    }

    #[Computed]
    public function taggable(): ?Model
    {
        $class = $this->taggableType;

        if (! class_exists($class)) {
            return null;
        }

        return $class::find($this->taggableId);
    }

    #[Computed]
    public function attachedTags(): Collection
    {
        $taggable = $this->taggable();

        if (! $taggable) {
            return collect();
        }

        return $taggable->tags()
            ->where('user_id', auth()->id())
            ->get();
    }

    #[Computed]
    public function availableTags(): Collection
    {
        if (strlen($this->searchQuery) < 1) {
            return collect();
        }

        $attachedIds = $this->attachedTags()->pluck('id')->toArray();

        return Tag::where('user_id', auth()->id())
            ->where('name', 'like', '%'.strtolower($this->searchQuery).'%')
            ->whereNotIn('id', $attachedIds)
            ->orderByRaw('(SELECT COUNT(*) FROM taggables WHERE tag_id = tags.id) DESC')
            ->limit(10)
            ->get();
    }

    public function attachTag(string $tagId): void
    {
        $tag = Tag::find($tagId);

        if (! $tag || Gate::denies('view', $tag)) {
            abort(403, 'You cannot attach tags that do not belong to you.');
        }

        $taggable = $this->taggable();

        if (! $taggable) {
            return;
        }

        (new AttachTag)->execute($tag, $taggable);

        $this->searchQuery = '';
        unset($this->attachedTags, $this->availableTags);
    }

    public function createAndAttachTag(): void
    {
        if (empty(trim($this->searchQuery))) {
            return;
        }

        $taggable = $this->taggable();

        if (! $taggable) {
            return;
        }

        $tag = (new CreateTag)->execute(auth()->id(), $this->searchQuery);
        (new AttachTag)->execute($tag, $taggable);

        $this->searchQuery = '';
        unset($this->attachedTags, $this->availableTags);
    }

    public function detachTag(string $tagId): void
    {
        $tag = Tag::find($tagId);

        if (! $tag || Gate::denies('view', $tag)) {
            abort(403, 'You cannot detach tags that do not belong to you.');
        }

        $taggable = $this->taggable();

        if (! $taggable) {
            return;
        }

        (new DetachTag)->execute($tag, $taggable);

        unset($this->attachedTags);
    }

    public function render()
    {
        return view('livewire.shared.tag-input');
    }
}
