<?php

use App\Models\User;
use App\Models\Quote;


beforeEach(function () {
    $this->user = User::factory()->create();
    // Create a quote belonging to our main test user
    $this->quote = Quote::factory()->create(['user_id' => $this->user->id]);
});


test('guest cannot see edit page', function () {
    $this->get(route('quotes.edit', $this->quote))
        ->assertRedirect('/login');
});


test('owner can see their own edit page', function () {
    $this->actingAs($this->user)
        ->get(route('quotes.edit', $this->quote))
        ->assertStatus(200)
        ->assertViewIs('quotes.create')
        ->assertSee($this->quote->content);
});


test('user cannot see edit page of another users quote', function () {
    $anotherUser = User::factory()->create();

    // Attempting to edit a quote that doesn't belong to them
    $this->actingAs($anotherUser)
        ->get(route('quotes.edit', $this->quote))
        ->assertStatus(403); 
});


test('edit page contains the current quote data', function () {
    $updateActionUrl = route('quotes.update', $this->quote);

    $this->actingAs($this->user)
        ->get(route('quotes.edit', $this->quote))
        ->assertSee("<form method=\"POST\" action=\"{$updateActionUrl}\"", false)
        ->assertSee('<input type="hidden" name="_method" value="PUT">', false)
        ->assertSee($this->quote->content);
});


test('author cannot load the edit form after 24 hours', function () {
    $this->travel(24)->hours();
    $this->travel(1)->minutes();

    $response = $this
        ->actingAs($this->user)
        ->get("/quotes/{$this->quote->id}/edit");

    $response->assertRedirect('/dashboard');
    $response->assertSessionHas('modal_error', 'This thought is locked and can no longer be modified.');
});


test('author cannot edit thought content after 24 hours', function () {
    $this->travel(24)->hours();
    $this->travel(1)->minutes();

    $response = $this
        ->actingAs($this->user)
        ->patch("/quotes/{$this->quote->id}", [
            'content' => 'Trying to rewrite history.',
        ]);

    $response->assertSessionHas('modal_error', 'This thought is locked and can no longer be modified.');

    $this->assertDatabaseHas('quotes', [
        'id' => $this->quote->id,
        'content' => $this->quote->content,
    ]);
});