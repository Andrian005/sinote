<?php

use App\Domain\Shared\Actions\AttachTag;
use App\Domain\Shared\Models\Tag;
use App\Domain\Shared\Models\User;
use Tests\Stubs\TaggableModelStub;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->tag = Tag::factory()->forUser($this->user)->create();
    $this->taggable = TaggableModelStub::create([
        'user_id' => $this->user->id,
        'name' => 'Test Item',
    ]);
    $this->action = new AttachTag;
});

test('attaches tag to taggable model', function () {
    $this->action->execute($this->tag, $this->taggable);

    assertDatabaseHas('taggables', [
        'tag_id' => $this->tag->id,
        'taggable_id' => $this->taggable->id,
        'taggable_type' => TaggableModelStub::class,
    ]);

    expect($this->taggable->tags)->toHaveCount(1)
        ->and($this->taggable->tags->first()->id)->toBe($this->tag->id);
});

test('is idempotent - attaching twice does not create duplicates', function () {
    $this->action->execute($this->tag, $this->taggable);
    $this->action->execute($this->tag, $this->taggable);

    assertDatabaseCount('taggables', 1);

    expect($this->taggable->fresh()->tags)->toHaveCount(1);
});

test('can attach multiple tags to same taggable', function () {
    $tag2 = Tag::factory()->forUser($this->user)->create();
    $tag3 = Tag::factory()->forUser($this->user)->create();

    $this->action->execute($this->tag, $this->taggable);
    $this->action->execute($tag2, $this->taggable);
    $this->action->execute($tag3, $this->taggable);

    assertDatabaseCount('taggables', 3);

    expect($this->taggable->fresh()->tags)->toHaveCount(3);
});

test('can attach same tag to multiple taggables', function () {
    $taggable2 = TaggableModelStub::create([
        'user_id' => $this->user->id,
        'name' => 'Second Item',
    ]);

    $this->action->execute($this->tag, $this->taggable);
    $this->action->execute($this->tag, $taggable2);

    assertDatabaseCount('taggables', 2);

    // Verify both taggables have the tag
    expect($this->taggable->fresh()->tags)->toHaveCount(1)
        ->and($taggable2->fresh()->tags)->toHaveCount(1);
});
