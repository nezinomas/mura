<?php

use App\Models\Quote;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('guest can view a specific user public feed', function () {
    // Arrange
    $user = User::factory()->create(['display_name' => 'Public Author']);
    Quote::factory(5)->create(['user_id' => $user->id, 'is_private' => false]);

    // Act
    $response = $this->get("/{$user->name}");

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

    $response = $this->get("/{$user->name}");

    $response->assertStatus(200);
    $response->assertSee('This is a public thought meant for the world.');
    $response->assertDontSee('This is a secret thought meant for no one.');
});

test('user feed limits database queries', function () {
    $user = User::factory()->create();
    Quote::factory(25)->create(['user_id' => $user->id, 'is_private' => false]);

    DB::enableQueryLog();
    DB::flushQueryLog();

    $this->get("/{$user->name}");

    $queryCount = count(DB::getQueryLog());

    // Aim for <= 3 queries (session, fetch user, fetch paginated quotes + users)
    expect($queryCount)->toBeLessThanOrEqual(3);
});

test('authenticated user sees grab button on another users profile', function () {
    // Arrange: Create a visitor and an author with a public thought
    $visitor = User::factory()->create();
    $author = User::factory()->create(['name' => 'author_name']);

    $quote = Quote::factory()->create([
        'user_id' => $author->id,
        'is_private' => false,
    ]);

    // Act: Visitor looks at the author's feed
    $response = $this->actingAs($visitor)->get('/' . $author->name);

    // Assert: The page loads and contains our Livewire component
    $response->assertOk();
    $response->assertSeeLivewire('grab-button');
});

test('user does not see grab button on their own profile', function () {
    // Arrange: Create an author with a public thought
    $author = User::factory()->create(['name' => 'author_name']);

    $quote = Quote::factory()->create([
        'user_id' => $author->id,
        'is_private' => false,
    ]);

    // Act: Author looks at their own feed
    $response = $this->actingAs($author)->get('/' . $author->name);

    // Assert: The page loads, but the Livewire component is NOT rendered
    $response->assertOk();
    $response->assertDontSeeLivewire('grab-button');
});

test('user profile feed is paginated at 20 thoughts', function () {
    // 1. Arrange: Create an author and a visitor
    $author = User::factory()->create();
    $visitor = User::factory()->create();

    // The elegant way to batch-create with unique timestamps
    Quote::factory()
        ->count(25)
        ->sequence(fn ($sequence) => ['created_at' => now()->subMinutes($sequence->index)])
        ->create([
            'user_id' => $author->id,
            'is_private' => false,
        ]);

    // 2. Act: Load Page 1
    $response = $this->actingAs($visitor)->get('/' . $author->name);
    
    $response->assertOk();
    
    // 3. Assert Backend: The controller paginated the data
    $response->assertViewHas('quotes', function ($quotes) {
        return $quotes->count() === 20;
    });

    // 4. Assert Frontend: The Blade file actually rendered the pagination HTML
    $response->assertSee('?page=2', false);
});