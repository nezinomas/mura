<?php

use App\Models\Quote;
use App\Models\User;

beforeEach(function() {
    $this->user = User::factory()->create();
    $this->quote = Quote::factory()->create(['user_id' => $this->user->id]);
});


test('user can grab public quote from a stranger', function() {
    $grabber = User::factory()->create();

    $response = $this->actingAs($grabber)->post(route('quotes.grab', $this->quote->id));

    $response->assertRedirect();
    $this->assertDatabaseHas('quote_user', [
        'user_id' => $grabber->id,
        'quote_id' => $this->quote->id,
    ]);
});


test('user cannot grab a private quote', function() {
    $this->quote->update(['is_private' => true]);

    $sneakyGrabber = User::factory()->create();

    $this->quote->is_private = true;
    $this->quote->refresh();

    $response = $this->actingAs($sneakyGrabber)->post(route('quotes.grab', $this->quote->id));

    $response->assertStatus(403);
});


test('user cannot grab their own quote', function() {
    $response = $this->actingAs($this->user)->post(route('quotes.grab', $this->quote->id));

    $response->assertStatus(403);

    $this->assertDatabaseMissing('quote_user', [
        'user_id' => $this->user->id,
        'quote_id' => $this->quote->id,
    ]);
});

