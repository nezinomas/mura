<?php

use App\Models\Quote;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});


test('author can sof-delete their own thought', function() {
    $quote = Quote::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)->delete("/quotes/{$quote->id}");
    $response->assertRedirect('/dashboard');

    $this->assertSoftDeleted($quote);
});


test('user cannot delete someone else thought', function() {
    $stranger = User::factory()->create();
    $strangersQuote = Quote::factory()->create(['user_id' => $stranger->id]);

    $response = $this->actingAs($this->user)->delete("/quotes/{$strangersQuote->id}");

    $response->assertStatus(403);
    $this->assertNotSoftDeleted($strangersQuote);
});