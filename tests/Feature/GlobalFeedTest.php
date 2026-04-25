<?php

use App\Models\Quote;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

test('guests can view the global discover feed on the home page', function () {
    // Arrange: Create 25 public thoughts
    Quote::factory(25)->create(['is_private' => false]);

    // Act
    $response = $this->get('/');

    // Assert: It should load exactly 20 random quotes
    $response->assertStatus(200);
    $response->assertViewHas('quotes', fn ($quotes) => $quotes->count() === 20);
});


test('global discover feed only shows public quotes', function () {
    Quote::factory()->create([
        'content' => 'This is a public thought meant for the world.',
        'is_private' => false,
    ]);

    Quote::factory()->create([
        'content' => 'This is a secret thought meant for no one.',
        'is_private' => true,
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('This is a public thought meant for the world.');
    $response->assertDontSee('This is a secret thought meant for no one.');
});


test('global discover feed does not display action buttons to guests', function () {
    Quote::factory()->create(['is_private' => false]);

    $response = $this->get('/');

    $response->assertDontSee('Edit');
    $response->assertDontSee('Delete');
    $response->assertDontSee('Grab');
    $response->assertDontSee('Ungrab');
});


test('global discover feed limits database queries', function () {
    Quote::factory(25)->create(['is_private' => false]);

    DB::enableQueryLog();
    DB::flushQueryLog();

    $this->get('/');

    $queryCount = count(DB::getQueryLog());

    // We should aim for <= 5 queries (session check, daily quote ID, daily quote fetch, fetch 20 random IDs, eager load quotes + users)
    expect($queryCount)->toBeLessThanOrEqual(5);
});


test('global discover feed links to user feed when user exists', function () {
    $user = User::factory()->create(['display_name' => 'Active Author']);
    Quote::factory()->create([
        'user_id' => $user->id,
        'is_private' => false,
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee(route('users.show', $user));
    $response->assertSee('Active Author');
});


test('global discover feed does not link to user feed when user is deleted', function () {
    $user = User::factory()->create();
    Quote::factory()->create([
        'user_id' => $user->id,
        'is_private' => false,
    ]);

    $user->delete(); // simulates user lost in time

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('(user lost in time)');
    $response->assertDontSee(route('users.show', $user)); // Ensure no user links are generated for this orphaned post
});


test('guests see permalink on thoughts', function () {
    $quote = Quote::factory()->create([
        'is_private' => false,
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee(route('quotes.show', $quote));
    $response->assertSee('Permalink');
});


test('global feed displays a daily choice thought', function () {
    $quote = Quote::factory()->create([
        'is_private' => false,
        'content' => 'Special daily thought',
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Thought of the Day');
    $response->assertSee('Special daily thought');
});


test('daily choice thought changes each day', function () {
    $quote1 = Quote::factory()->create(['is_private' => false, 'content' => 'Day 1 thought']);

    $this->get('/')->assertSee('Day 1 thought');

    // Time travel to tomorrow
    $this->travel(1)->day();

    $quote2 = Quote::factory()->create(['is_private' => false, 'content' => 'Day 2 thought']);
    $quote1->delete(); // Ensure it picks the new one

    $this->get('/')->assertSee('Day 2 thought');
});


test('authenticated user does not see their own thoughts on global discover', function () {
    $user = User::factory()->create();

    Quote::factory()->create([
        'user_id' => $user->id,
        'content' => 'My own public thought',
        'is_private' => false
    ]);

    $otherUser = User::factory()->create();
    $otherQuote = Quote::factory()->create([
        'user_id' => $otherUser->id,
        'content' => 'Someone elses public thought',
        'is_private' => false
    ]);

    // Ensure the daily quote is NOT the user's quote so we can properly test the standard feed logic
    Cache::shouldReceive('remember')
        ->with('daily_quote_id', Mockery::any(), Mockery::any())
        ->andReturn($otherQuote->id);

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
    $response->assertSee('Someone elses public thought');
    $response->assertDontSee('My own public thought');
});

test('authenticated user sees grab button for others thoughts on global discover', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Quote::factory()->create([
        'user_id' => $otherUser->id,
        'content' => 'A beautifully grabable thought',
        'is_private' => false
    ]);

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
    // We check for the form action or the button text
    $response->assertSee('Grab', false); 
});

test('authenticated user sees ungrab button for already grabbed thoughts on global discover', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $quote = Quote::factory()->create([
        'user_id' => $otherUser->id,
        'content' => 'A beautifully grabbed thought',
        'is_private' => false
    ]);

    $user->grabs()->attach($quote);

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
    $response->assertSee('Ungrab', false);
});

test('authenticated user sees their own thought if it is the daily quote', function () {
    $user = User::factory()->create();
    $myDailyQuote = Quote::factory()->create([
        'user_id' => $user->id,
        'content' => 'My very special daily thought',
        'is_private' => false,
    ]);

    // Force this quote to be the daily quote
    Cache::shouldReceive('remember')
        ->with('daily_quote_id', Mockery::any(), Mockery::any())
        ->andReturn($myDailyQuote->id);

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
    $response->assertSee('Thought of the Day');
    $response->assertSee('My very special daily thought');
});
