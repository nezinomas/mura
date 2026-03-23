<?php

use App\Models\Quote;
use App\Models\User;

beforeEach(function() {
    $this->quoteOwner = User::factory()->create();
    $this->quote = Quote::factory()->create(['user_id' => $this->quoteOwner->id]);
    $this->grabber = User::factory()->create();
});

test('user can ungrab a quote', function() {
    // Grab the quote first
    $this->grabber->grabs()->attach($this->quote);
    $this->assertDatabaseHas('quote_user', [
        'user_id' => $this->grabber->id,
        'quote_id' => $this->quote->id,
    ]);

    // Act
    $response = $this->actingAs($this->grabber)->delete(route('quotes.ungrab', $this->quote));

    // Assert
    $response->assertRedirect();
    $this->assertDatabaseMissing('quote_user', [
        'user_id' => $this->grabber->id,
        'quote_id' => $this->quote->id,
    ]);
});

test('guest cannot ungrab quote', function() {
    $response = $this->delete(route('quotes.ungrab', $this->quote));
    $response->assertRedirect(route('login'));
});

test('user cannot ungrab a quote they have not grabbed', function() {
    $response = $this->actingAs($this->grabber)->delete(route('quotes.ungrab', $this->quote));
    $response->assertStatus(403);
});
