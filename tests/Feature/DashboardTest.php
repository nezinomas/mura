<?php

use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


beforeEach(function () {
    $this->user = User::factory()->create();
});


test('the dashboard displays a complete mix of user content', function () {
    // 1. Create a Public Thought
    Quote::factory()->create([
        'user_id' => $this->user->id,
        'content' => 'This is public',
        'is_private' => false
    ]);

    // 2. Create a Private Thought
    Quote::factory()->create([
        'user_id' => $this->user->id,
        'content' => 'This is a secret',
        'is_private' => true
    ]);

    // 3. Create a "Grab" (Pivot table logic)
    $otherUser = User::factory()->create();
    $grabbedQuote = Quote::factory()->create([
        'user_id' => $otherUser->id,
        'content' => 'A brilliant thought I grabbed'
    ]);
    $this->user->grabs()->attach($grabbedQuote);

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertStatus(200);

    // Assertions for Content
    $response->assertSee('This is public');
    $response->assertSee('This is a secret');
    $response->assertSee('A brilliant thought I grabbed');


    $response->assertSee('Public');
    $response->assertSee('Private');
    $response->assertSee('mura-grab-card');
});


test('author sees edit and delete buttons on their recent thoughts', function () {
    $quote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'created_at' => now() // Freshly created
    ]);

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertSee('Edit');
    $response->assertSee('Delete');
});


test('author does not see edit button after 24 hours', function () {
    $quote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'created_at' => now()->subHours(25) // Older than the 24h window
    ]);

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertSee('Delete'); // They can still delete!
    $response->assertDontSee('Edit'); // But it's locked in stone.
});


test('user does not see edit or delete buttons on grabbed thoughts', function () {
    $otherUser = User::factory()->create();
    $grabbedQuote = Quote::factory()->create(['user_id' => $otherUser->id]);

    $this->user->grabs()->attach($grabbedQuote);

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertSee('Ungrab');
    $response->assertDontSee('Edit');
    $response->assertDontSee('Delete');
});


test('user should see change password link', function() {
    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertSee(route('password.change'));
    $response->assertSeeText('Change Password');
});


test('dashboard feed does not trigger N+1 lazy loading', function () {
    // 1. Turn on the strict alarm
    Model::preventLazyLoading(true);

    // 2. Arrange: Create multiple quotes and grabs to force a collection loop
    Quote::factory(3)->create(['user_id' => $this->user->id]);

    $otherUser = User::factory()->create();
    $grabbedQuotes = Quote::factory(2)->create(['user_id' => $otherUser->id]);
    $this->user->grabs()->attach($grabbedQuotes->pluck('id'));

    // 3. Act: Load the dashboard
    $response = $this->actingAs($this->user)->get('/dashboard');

    // 4. Assert: If the controller or Blade view lazy loads the authors, 
    // this will crash with a 500 error before it ever reaches this line
    $response->assertStatus(200);

    // Turn it off so it doesn't leak into other tests
    Model::preventLazyLoading(false);
});

test('author sees standard delete warning for private thought', function () {
    $quote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'is_private' => true
    ]);

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertSee('Are you sure you want to delete this thought?');
});

test('author sees standard delete warning for ungrabbed public thought', function () {
    $quote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'is_private' => false
    ]);

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertSee('Are you sure you want to delete this thought?');
});

test('author sees global feed warning when deleting grabbed public thought', function () {
    $quote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'is_private' => false
    ]);

    $otherUser = User::factory()->create();
    $otherUser->grabs()->attach($quote);

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertSee('This thought will remain visible on the global feed forever.');
});