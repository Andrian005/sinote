<?php

use App\Domain\Shared\Actions\AttachTag;
use App\Domain\Shared\Actions\DetachTag;
use App\Domain\Shared\Models\Tag;
use App\Domain\Shared\Models\User;
use Tests\Stubs\TaggableModelStub;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->tag = Tag::factory()->forUser($this->user)->create();
    $this->taggable = TaggableModelStub::create([
        'user_id' => $this->user->id,
        'name' => 'Test Item',
    ]);

    // Attach tag first
    $attachAction = new AttachTag;
    $attachAction->execute($this->tag, $this->taggable);

    $this->action = new DetachTag;
});

test('detaches tag from taggable model', function () {
    // Verify tag is attached
    expect($this->taggable->tags)->toHaveCount(1);

    $this->action->execute($this->tag, $this->taggable);

    assertDatabaseMissing('taggables', [
        'tag_id' => $this->tag->id,
        'taggable_id' => $this->taggable->id,
    ]);

    expect($this->taggable->fresh()->tags)->toHaveCount(0);
});

test('does not delete the tag itself', function () {
    $tagId = $this->tag->id;

    $this->action->execute($this->tag, $this->taggable);

    expect(Tag::find($tagId))->not->toBeNull()
        ->and(Tag::find($tagId)->id)->toBe($tagId);
});

test('does not delete the taggable model', function () {
    $taggableId = $this->taggable->id;

    $this->action->execute($this->tag, $this->taggable);

    expect(TaggableModelStub::find($taggableId))->not->toBeNull()
        ->and(TaggableModelStub::find($taggableId)->id)->toBe($taggableId);
});

test('detaching only removes specific tag relationship', function () {
    // Attach a second tag
    $tag2 = Tag::factory()->forUser($this->user)->create();
    $attachAction = new AttachTag;
    $attachAction->execute($tag2, $this->taggable);

    // Verify both tags attached
    expect($this->taggable->fresh()->tags)->toHaveCount(2);

    // Detach only the first tag
    $this->action->execute($this->tag, $this->taggable);

    // Second tag should still be attached
    expect($this->taggable->fresh()->tags)->toHaveCount(1)
        ->and($this->taggable->fresh()->tags->first()->id)->toBe($tag2->id);
});

test('is safe to call on unattached tag', function () {
    // Detach first
    $this->action->execute($this->tag, $this->taggable);

    // Try to detach again - should not throw error
    expect(fn () => $this->action->execute($this->tag, $this->taggable))
        ->not->toThrow(Exception::class);

    assertDatabaseCount('taggables', 0);
});
