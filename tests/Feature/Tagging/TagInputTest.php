<?php

use App\Domain\Shared\Models\Tag;
use App\Domain\Shared\Models\User;
use App\Livewire\Shared\TagInput;
use Livewire\Livewire;
use Tests\Fixtures\FakeTaggable;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->taggable = FakeTaggable::create([
        'name' => 'Test Item',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
});

test('user can attach existing tag to taggable', function () {
    $tag = Tag::factory()->forUser($this->user)->withName('urgent')->create();

    actingAs($this->user);

    Livewire::test(TagInput::class, [
        'taggableType' => FakeTaggable::class,
        'taggableId' => $this->taggable->id,
    ])
        ->call('attachTag', $tag->id)
        ->assertSet('searchQuery', '');

    assertDatabaseHas('taggables', [
        'tag_id' => $tag->id,
        'taggable_id' => $this->taggable->id,
        'taggable_type' => FakeTaggable::class,
    ]);

    expect($this->taggable->fresh()->tags)->toHaveCount(1);
});

test('user can detach tag from taggable', function () {
    $tag = Tag::factory()->forUser($this->user)->withName('urgent')->create();

    // Attach tag first
    $this->taggable->tags()->attach($tag->id);

    actingAs($this->user);

    Livewire::test(TagInput::class, [
        'taggableType' => FakeTaggable::class,
        'taggableId' => $this->taggable->id,
    ])
        ->call('detachTag', $tag->id);

    assertDatabaseCount('taggables', 0);

    expect($this->taggable->fresh()->tags)->toHaveCount(0);
});

test('user can create new tag on type and attach it', function () {
    actingAs($this->user);

    Livewire::test(TagInput::class, [
        'taggableType' => FakeTaggable::class,
        'taggableId' => $this->taggable->id,
    ])
        ->set('searchQuery', 'NewTag')
        ->call('createAndAttachTag')
        ->assertSet('searchQuery', '');

    // Tag should be created with lowercase name
    assertDatabaseHas('tags', [
        'user_id' => $this->user->id,
        'name' => 'newtag',
    ]);

    // Tag should be attached
    $tag = Tag::where('name', 'newtag')->first();
    assertDatabaseHas('taggables', [
        'tag_id' => $tag->id,
        'taggable_id' => $this->taggable->id,
    ]);
});

test('create and attach is idempotent - does not create duplicate', function () {
    actingAs($this->user);

    $component = Livewire::test(TagInput::class, [
        'taggableType' => FakeTaggable::class,
        'taggableId' => $this->taggable->id,
    ]);

    // Create once
    $component->set('searchQuery', 'duplicate')->call('createAndAttachTag');

    // Try to create again with different case
    $component->set('searchQuery', 'DUPLICATE')->call('createAndAttachTag');

    // Should only have 1 tag
    assertDatabaseCount('tags', 1);
    assertDatabaseHas('tags', ['name' => 'duplicate']);

    // Should only have 1 taggable entry
    assertDatabaseCount('taggables', 1);
});

test('autocomplete only shows tags belonging to current user', function () {
    $otherUser = User::factory()->create();

    // Create tags for both users
    $myTag = Tag::factory()->forUser($this->user)->withName('mytag')->create();
    $otherTag = Tag::factory()->forUser($otherUser)->withName('othertag')->create();

    actingAs($this->user);

    $component = Livewire::test(TagInput::class, [
        'taggableType' => FakeTaggable::class,
        'taggableId' => $this->taggable->id,
    ])
        ->set('searchQuery', 'tag');

    // Check computed property
    $availableTags = $component->get('availableTags');

    expect($availableTags)->toHaveCount(1)
        ->and($availableTags->first()->id)->toBe($myTag->id);
});

test('autocomplete filters tags case-insensitive', function () {
    Tag::factory()->forUser($this->user)->withName('urgent')->create();
    Tag::factory()->forUser($this->user)->withName('learning')->create();
    Tag::factory()->forUser($this->user)->withName('design')->create();

    actingAs($this->user);

    $component = Livewire::test(TagInput::class, [
        'taggableType' => FakeTaggable::class,
        'taggableId' => $this->taggable->id,
    ])
        ->set('searchQuery', 'URG');

    $availableTags = $component->get('availableTags');

    expect($availableTags)->toHaveCount(1)
        ->and($availableTags->first()->name)->toBe('urgent');
});

test('autocomplete excludes already attached tags', function () {
    $tag1 = Tag::factory()->forUser($this->user)->withName('work')->create();
    $tag2 = Tag::factory()->forUser($this->user)->withName('workflow')->create();

    // Attach tag1
    $this->taggable->tags()->attach($tag1->id);

    actingAs($this->user);

    $component = Livewire::test(TagInput::class, [
        'taggableType' => FakeTaggable::class,
        'taggableId' => $this->taggable->id,
    ])
        ->set('searchQuery', 'work');

    $availableTags = $component->get('availableTags');

    // Should only show workflow, not work (already attached)
    expect($availableTags)->toHaveCount(1)
        ->and($availableTags->first()->name)->toBe('workflow');
});

test('attached tags are displayed', function () {
    $tag1 = Tag::factory()->forUser($this->user)->withName('urgent')->create();
    $tag2 = Tag::factory()->forUser($this->user)->withName('work')->create();

    $this->taggable->tags()->attach([$tag1->id, $tag2->id]);

    actingAs($this->user);

    $component = Livewire::test(TagInput::class, [
        'taggableType' => FakeTaggable::class,
        'taggableId' => $this->taggable->id,
    ]);

    $attachedTags = $component->get('attachedTags');

    expect($attachedTags)->toHaveCount(2);
});

test('user cannot attach tag belonging to another user', function () {
    $otherUser = User::factory()->create();
    $otherTag = Tag::factory()->forUser($otherUser)->withName('othertag')->create();

    actingAs($this->user);

    Livewire::test(TagInput::class, [
        'taggableType' => FakeTaggable::class,
        'taggableId' => $this->taggable->id,
    ])
        ->call('attachTag', $otherTag->id)
        ->assertForbidden();
});

test('user cannot detach tag belonging to another user', function () {
    $otherUser = User::factory()->create();
    $otherTag = Tag::factory()->forUser($otherUser)->withName('othertag')->create();

    // Somehow the tag got attached (shouldn't happen in real scenario)
    $this->taggable->tags()->attach($otherTag->id);

    actingAs($this->user);

    Livewire::test(TagInput::class, [
        'taggableType' => FakeTaggable::class,
        'taggableId' => $this->taggable->id,
    ])
        ->call('detachTag', $otherTag->id)
        ->assertForbidden();
});
