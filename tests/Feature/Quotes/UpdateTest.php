<?php

use App\Models\User;
use App\Models\Quote;


beforeEach(function() {
    $this->user = User::factory()->create();
    $this->quote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'is_private' => false,
    ]);
});


test('author can make ungrabbed public quote private', function() {
    $this->actingAs($this->user)
        ->patch(
            route('quotes.update', $this->quote->id),
            ['is_private' => true]);

    $this->assertDatabaseHas('quotes', [
        'id' => $this->quote->id,
        'is_private' => true,
    ]);
});


test('author can not make grabbed quote private', function() {
    $stranger = User::factory()->create();
    $stranger->grabs()->attach($this->quote->id);

    $reponse = $this->actingAs($this->user)
        ->patch(
            route('quotes.update', $this->quote->id),
            ['is_private' => true]);

    $reponse->assertStatus(403);

    $this->assertDatabaseHas('quotes', [
        'id' => $this->quote->id,
        'is_private' => false,
    ]);
});