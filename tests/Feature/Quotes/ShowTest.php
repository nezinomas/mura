<?php

use App\Models\Quote;
use App\Models\User;

test('guest can see a public thought', function () {
    $quote = Quote::factory()->create([
        'is_private' => false,
        'content' => 'Public thought by someone',
    ]);

    $this->get(route('quotes.show', $quote))
        ->assertStatus(200)
        ->assertSee('Public thought by someone');
});

test('guest cannot see a private thought', function () {
    $quote = Quote::factory()->create([
        'is_private' => true,
    ]);

    $this->get(route('quotes.show', $quote))
        ->assertStatus(404);
});

test('logged user can see their own public thought', function () {
    $user = User::factory()->create();
    $quote = Quote::factory()->create([
        'user_id' => $user->id,
        'is_private' => false,
        'content' => 'My own public thought',
    ]);

    $this->actingAs($user)
        ->get(route('quotes.show', $quote))
        ->assertStatus(200)
        ->assertSee('My own public thought');
});

test('logged user can see their own private thought', function () {
    $user = User::factory()->create();
    $quote = Quote::factory()->create([
        'user_id' => $user->id,
        'is_private' => true,
        'content' => 'My own secret thought',
    ]);

    $this->actingAs($user)
        ->get(route('quotes.show', $quote))
        ->assertStatus(200)
        ->assertSee('My own secret thought');
});

test('logged user can see another users public thought', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $quote = Quote::factory()->create([
        'user_id' => $otherUser->id,
        'is_private' => false,
        'content' => 'Another users public thought',
    ]);

    $this->actingAs($user)
        ->get(route('quotes.show', $quote))
        ->assertStatus(200)
        ->assertSee('Another users public thought');
});

test('logged user cannot see another users private thought', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $quote = Quote::factory()->create([
        'user_id' => $otherUser->id,
        'is_private' => true,
    ]);

    $this->actingAs($user)
        ->get(route('quotes.show', $quote))
        ->assertStatus(404);
});