<?php

use App\Models\Quote;
use App\Models\User;
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