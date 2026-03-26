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
    
    // Check that the specific edit link for this quote is missing
    $response->assertDontSee(route('quotes.edit', $grabbedQuote));
    
    // Check that the specific delete dispatch for this quote is missing
    $response->assertDontSee("\$dispatch('confirmDelete', { quoteId: {$grabbedQuote->id} })", false);
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


test('user can filter dashboard by public thoughts', function () {
    Quote::factory()->create(['user_id' => $this->user->id, 'content' => 'My public thought', 'is_private' => false]);
    Quote::factory()->create(['user_id' => $this->user->id, 'content' => 'My private thought', 'is_private' => true]);

    $response = $this->actingAs($this->user)->get('/dashboard?filter=public');

    $response->assertStatus(200);
    $response->assertSee('My public thought');
    $response->assertDontSee('My private thought');
});


test('user can filter dashboard by private thoughts', function () {
    Quote::factory()->create(['user_id' => $this->user->id, 'content' => 'My public thought', 'is_private' => false]);
    Quote::factory()->create(['user_id' => $this->user->id, 'content' => 'My private thought', 'is_private' => true]);

    $response = $this->actingAs($this->user)->get('/dashboard?filter=private');

    $response->assertStatus(200);
    $response->assertSee('My private thought');
    $response->assertDontSee('My public thought');
});


test('user can filter dashboard by grabbed thoughts', function () {
    Quote::factory()->create(['user_id' => $this->user->id, 'content' => 'My own thought']);

    $otherUser = User::factory()->create();
    $grabbedQuote = Quote::factory()->create(['user_id' => $otherUser->id, 'content' => 'A grabbed thought']);
    $this->user->grabs()->attach($grabbedQuote);

    $response = $this->actingAs($this->user)->get('/dashboard?filter=grabbed');

    $response->assertStatus(200);
    $response->assertSee('A grabbed thought');
    $response->assertDontSee('My own thought');
});


test('dashboard header contains filter links', function () {
    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('filter=public', false);
    $response->assertSee('filter=private', false);
    $response->assertSee('filter=grabbed', false);

    $response->assertSeeText('Public');
    $response->assertSeeText('Private');
    $response->assertSeeText('Grabbed');
});


test('grabbed quotes do not display the public visibility text on the dashboard', function () {
    // Arrange: Create another user and a public quote
    $otherUser = User::factory()->create();
    $grabbedQuote = Quote::factory()->create([
        'user_id' => $otherUser->id,
        'is_private' => false,
    ]);

    // Act: The logged-in user grabs it
    $this->user->grabs()->attach($grabbedQuote);

    // Assert: The dashboard does not render the "- public" string for this quote
    $response = $this->actingAs($this->user)->get('/dashboard');

    // Check that the specific quote content exists, but the " - public" badge does not
    $response->assertSee($grabbedQuote->content_html, false);
    $response->assertDontSee('— Public');
});