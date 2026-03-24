<?php

use App\Models\Quote;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('guest can view a specific user public feed', function () {
    // Arrange
    $user = User::factory()->create(['display_name' => 'Public Author']);
    Quote::factory(5)->create(['user_id' => $user->id, 'is_private' => false]);

    // Act
    $response = $this->get("/users/{$user->id}");

    // Assert
    $response->assertStatus(200);
    $response->assertSee('Public Author');
    $response->assertViewHas('quotes', fn ($quotes) => $quotes->count() === 5);
});

test('user feed only shows public quotes', function () {
    $user = User::factory()->create();

    Quote::factory()->create([
        'user_id' => $user->id,
        'content' => 'This is a public thought meant for the world.',
        'is_private' => false,
    ]);

    Quote::factory()->create([
        'user_id' => $user->id,
        'content' => 'This is a secret thought meant for no one.',
        'is_private' => true,
    ]);

    $response = $this->get("/users/{$user->id}");

    $response->assertStatus(200);
    $response->assertSee('This is a public thought meant for the world.');
    $response->assertDontSee('This is a secret thought meant for no one.');
});

test('user feed limits database queries', function () {
    $user = User::factory()->create();
    Quote::factory(25)->create(['user_id' => $user->id, 'is_private' => false]);

    DB::enableQueryLog();
    DB::flushQueryLog();

    $this->get("/users/{$user->id}");

    $queryCount = count(DB::getQueryLog());

    // Aim for <= 3 queries (session, fetch user, fetch paginated quotes + users)
    expect($queryCount)->toBeLessThanOrEqual(3);
});