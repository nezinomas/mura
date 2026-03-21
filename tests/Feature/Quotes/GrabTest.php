<?php

use App\Models\Quote;
use App\Models\User;

beforeEach(function() {
    $this->user = User::factory()->create();
    $this->quote = Quote::factory()->create(['user_id' => $this->user->id]);
});


test('guest cannot grab quote', function() {
    $this->post(route('quotes.grab', $this->quote->id))->assertRedirect(route('login'));
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

    $this->quote->update(['is_private' => true]);

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


test('user can click grab multiple times without cashing databse', function() {
    $grabber = User::factory()->create();

    // Click 1: initial grab
    $this->actingAs($grabber)
        ->post(route('quotes.grab', $this->quote->id))
        ->assertRedirect();

    // Click 2: laggy double-click
    $this->actingAs($grabber)
        ->post(route('quotes.grab', $this->quote->id))
        ->assertRedirect();

    expect($grabber->grabs()->where('quote_id', $this->quote->id)->count())->toBe(1);
});