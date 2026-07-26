<?php

use App\Domain\Shared\Actions\CreateTag;
use App\Domain\Shared\Models\Tag;
use App\Domain\Shared\Models\User;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new CreateTag;
});

test('creates a new tag with lowercase name', function () {
    $tag = $this->action->execute($this->user->id, 'WorkTag');

    expect($tag)->toBeInstanceOf(Tag::class)
        ->and($tag->name)->toBe('worktag')
        ->and($tag->user_id)->toBe($this->user->id);

    assertDatabaseHas('tags', [
        'user_id' => $this->user->id,
        'name' => 'worktag',
    ]);
});

test('returns existing tag if name already exists (case-insensitive)', function () {
    // Create first tag
    $firstTag = $this->action->execute($this->user->id, 'urgent');

    // Try to create with different case
    $secondTag = $this->action->execute($this->user->id, 'URGENT');

    expect($secondTag->id)->toBe($firstTag->id)
        ->and($secondTag->name)->toBe('urgent');

    // Should only have 1 tag in database
    assertDatabaseCount('tags', 1);
});

test('normalizes tag name to lowercase', function () {
    $mixedCaseTag = $this->action->execute($this->user->id, 'MiXeD-CaSe');

    expect($mixedCaseTag->name)->toBe('mixed-case');

    assertDatabaseHas('tags', [
        'name' => 'mixed-case',
    ]);
});

test('trims whitespace from tag name', function () {
    $tag = $this->action->execute($this->user->id, '  spaced  ');

    expect($tag->name)->toBe('spaced');
});

test('different users can have tags with same name', function () {
    $user2 = User::factory()->create();

    $tag1 = $this->action->execute($this->user->id, 'shared');
    $tag2 = $this->action->execute($user2->id, 'shared');

    expect($tag1->id)->not->toBe($tag2->id)
        ->and($tag1->name)->toBe('shared')
        ->and($tag2->name)->toBe('shared');

    assertDatabaseCount('tags', 2);
});
