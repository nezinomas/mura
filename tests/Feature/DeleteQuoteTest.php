<?php

use App\Models\Quote;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});


test('author can delete this own private thought', function() {
    $quote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'is_private' => true,
    ]);

    $response = $this->actingAs($this->user)->delete("/quotes/{$quote->id}");
    $response->assertRedirect('/dashboard');

    $this->assertDatabaseMissing('quotes', [
        'id' => $quote->id,
    ]);
});


test('author can not delete this own public thought', function() {
    $quote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'is_private' => false,
    ]);

    $response = $this->actingAs($this->user)->delete("/quotes/{$quote->id}");
    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('quotes', [
            'id' => $quote->id,
            'user_id' => null,
        ]);
});


test('user cannot delete someone else thought', function() {
    $stranger = User::factory()->create();
    $strangersQuote = Quote::factory()->create(['user_id' => $stranger->id]);

    $response = $this->actingAs($this->user)->delete("/quotes/{$strangersQuote->id}");

    $response->assertStatus(403);

    $this->assertDatabaseHas('quotes', [
            'id' => $strangersQuote->id,
            'user_id' => $stranger->id,
        ]);
});