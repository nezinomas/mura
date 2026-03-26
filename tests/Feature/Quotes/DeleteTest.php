<?php

use App\Models\Quote;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->quote = Quote::factory()->create(['user_id' => $this->user->id]);
});


test('quest cannot delete quote', function() {
    $this->delete(route('quotes.destroy', $this->quote->id))->assertRedirect(route('login'));
});


test('author can delete this own private thought', function() {
    $this->quote->update(['is_private' => true]);

    $response = $this->actingAs($this->user)->delete(route('quotes.destroy', $this->quote->id));
    $response->assertRedirect('/dashboard');

    $this->assertDatabaseMissing('quotes', [
        'id' => $this->quote->id,
    ]);
});


test('author can delete this own public thought if it is not grabbed', function() {
    $response = $this->actingAs($this->user)->delete(route('quotes.destroy', $this->quote->id));
    $response->assertRedirect('/dashboard');

    $this->assertDatabaseMissing('quotes', [
            'id' => $this->quote->id,
        ]);
});


test('author can not delete this own public thought if it is grabbed', function() {
    $grabber = User::factory()->create();
    $grabber->grabs()->attach($this->quote->id);

    $response = $this->actingAs($this->user)->delete(route('quotes.destroy', $this->quote->id));
    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('quotes', [
            'id' => $this->quote->id,
            'user_id' => null,
        ]);
});


test('user cannot delete someone else thought', function() {
    $stranger = User::factory()->create();
    $strangersQuote = Quote::factory()->create(['user_id' => $stranger->id]);

    $response = $this->actingAs($this->user)->delete(route('quotes.destroy', $strangersQuote->id));

    $response->assertStatus(403);

    $this->assertDatabaseHas('quotes', [
            'id' => $strangersQuote->id,
            'user_id' => $stranger->id,
        ]);
});