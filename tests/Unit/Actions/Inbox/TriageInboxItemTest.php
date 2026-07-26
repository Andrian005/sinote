<?php

use App\Domain\Inbox\Actions\TriageInboxItem;
use App\Domain\Inbox\Contracts\CreatesNoteFromInbox;
use App\Domain\Inbox\Contracts\CreatesTaskFromInbox;
use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Inbox\Exceptions\InboxItemAlreadyProcessedException;
use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Eloquent\Model;

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Return a Mockery partial mock of Model with a fixed getKey().
 * Avoids anonymous class constructor conflicts with Laravel's HasEvents boot.
 */
function fakeModel(string $id = 'fake-ulid-0001'): Model
{
    $mock = Mockery::mock(Model::class);
    $mock->shouldReceive('getKey')->andReturn($id);

    return $mock;
}

/**
 * Build a TriageInboxItem action with mock contract implementations.
 */
function buildAction(
    ?CreatesTaskFromInbox $createTask = null,
    ?CreatesNoteFromInbox $createNote = null,
): TriageInboxItem {
    return new TriageInboxItem(
        createTask: $createTask ?? Mockery::mock(CreatesTaskFromInbox::class),
        createNote: $createNote ?? Mockery::mock(CreatesNoteFromInbox::class),
    );
}

// ---------------------------------------------------------------------------
// Triage to Task
// ---------------------------------------------------------------------------

test('can triage an unprocessed InboxItem to a Task', function () {
    $user = User::factory()->create();
    $item = InboxItem::factory()->forUser($user)->unprocessed()->create();
    $fakeTask = fakeModel('task-ulid-001');

    $mockCreateTask = Mockery::mock(CreatesTaskFromInbox::class);
    $mockCreateTask->shouldReceive('execute')->once()->with($user, $item)->andReturn($fakeTask);

    $action = buildAction(createTask: $mockCreateTask);
    $result = $action->execute($user, $item, 'task');

    expect($result)->toBe($fakeTask);
});

test('InboxItem status becomes processed after triage to task', function () {
    $user = User::factory()->create();
    $item = InboxItem::factory()->forUser($user)->unprocessed()->create();

    $mockCreateTask = Mockery::mock(CreatesTaskFromInbox::class);
    $mockCreateTask->shouldReceive('execute')->once()->andReturn(fakeModel('task-001'));

    buildAction(createTask: $mockCreateTask)->execute($user, $item, 'task');

    expect($item->fresh()->status)->toBe(InboxItemStatus::Processed);
});

test('converted_to_type is set to task after triage', function () {
    $user = User::factory()->create();
    $item = InboxItem::factory()->forUser($user)->unprocessed()->create();

    $mockCreateTask = Mockery::mock(CreatesTaskFromInbox::class);
    $mockCreateTask->shouldReceive('execute')->once()->andReturn(fakeModel('task-001'));

    buildAction(createTask: $mockCreateTask)->execute($user, $item, 'task');

    expect($item->fresh()->converted_to_type)->toBe('task');
});

test('converted_to_id is set to the created task id after triage', function () {
    $user = User::factory()->create();
    $item = InboxItem::factory()->forUser($user)->unprocessed()->create();

    $mockCreateTask = Mockery::mock(CreatesTaskFromInbox::class);
    $mockCreateTask->shouldReceive('execute')->once()->andReturn(fakeModel('task-ulid-xyz'));

    buildAction(createTask: $mockCreateTask)->execute($user, $item, 'task');

    expect($item->fresh()->converted_to_id)->toBe('task-ulid-xyz');
});

// ---------------------------------------------------------------------------
// Triage to Note
// ---------------------------------------------------------------------------

test('can triage an unprocessed InboxItem to a Note', function () {
    $user = User::factory()->create();
    $item = InboxItem::factory()->forUser($user)->unprocessed()->create();
    $fakeNote = fakeModel('note-ulid-001');

    $mockCreateNote = Mockery::mock(CreatesNoteFromInbox::class);
    $mockCreateNote->shouldReceive('execute')->once()->with($user, $item)->andReturn($fakeNote);

    $result = buildAction(createNote: $mockCreateNote)->execute($user, $item, 'note');

    expect($result)->toBe($fakeNote);
});

test('InboxItem status becomes processed after triage to note', function () {
    $user = User::factory()->create();
    $item = InboxItem::factory()->forUser($user)->unprocessed()->create();

    $mockCreateNote = Mockery::mock(CreatesNoteFromInbox::class);
    $mockCreateNote->shouldReceive('execute')->once()->andReturn(fakeModel('note-001'));

    buildAction(createNote: $mockCreateNote)->execute($user, $item, 'note');

    expect($item->fresh()->status)->toBe(InboxItemStatus::Processed);
});

test('converted_to_type is set to note after triage', function () {
    $user = User::factory()->create();
    $item = InboxItem::factory()->forUser($user)->unprocessed()->create();

    $mockCreateNote = Mockery::mock(CreatesNoteFromInbox::class);
    $mockCreateNote->shouldReceive('execute')->once()->andReturn(fakeModel('note-001'));

    buildAction(createNote: $mockCreateNote)->execute($user, $item, 'note');

    expect($item->fresh()->converted_to_type)->toBe('note');
});

// ---------------------------------------------------------------------------
// processed_at
// ---------------------------------------------------------------------------

test('processed_at is set after successful triage', function () {
    $user = User::factory()->create();
    $item = InboxItem::factory()->forUser($user)->unprocessed()->create();

    $mockCreateTask = Mockery::mock(CreatesTaskFromInbox::class);
    $mockCreateTask->shouldReceive('execute')->once()->andReturn(fakeModel());

    buildAction(createTask: $mockCreateTask)->execute($user, $item, 'task');

    expect($item->fresh()->processed_at)->not->toBeNull();
});

// ---------------------------------------------------------------------------
// Guard: already processed / discarded
// ---------------------------------------------------------------------------

test('throws exception when triaging an already processed item', function () {
    $user = User::factory()->create();
    $item = InboxItem::factory()->forUser($user)->processed()->create();

    expect(fn () => buildAction()->execute($user, $item, 'task'))
        ->toThrow(InboxItemAlreadyProcessedException::class);
});

test('throws exception when triaging an already discarded item', function () {
    $user = User::factory()->create();
    $item = InboxItem::factory()->forUser($user)->discarded()->create();

    expect(fn () => buildAction()->execute($user, $item, 'note'))
        ->toThrow(InboxItemAlreadyProcessedException::class);
});

test('exception message contains the current status on guard failure', function () {
    $user = User::factory()->create();
    $item = InboxItem::factory()->forUser($user)->discarded()->create();

    expect(fn () => buildAction()->execute($user, $item, 'task'))
        ->toThrow(InboxItemAlreadyProcessedException::class, 'discarded');
});

// ---------------------------------------------------------------------------
// Guard: invalid target type
// ---------------------------------------------------------------------------

test('throws InvalidArgumentException for unsupported target type', function () {
    $user = User::factory()->create();
    $item = InboxItem::factory()->forUser($user)->unprocessed()->create();

    expect(fn () => buildAction()->execute($user, $item, 'project'))
        ->toThrow(InvalidArgumentException::class);
});

test('exception message names the unsupported target type', function () {
    $user = User::factory()->create();
    $item = InboxItem::factory()->forUser($user)->unprocessed()->create();

    expect(fn () => buildAction()->execute($user, $item, 'project'))
        ->toThrow(InvalidArgumentException::class, 'project');
});
