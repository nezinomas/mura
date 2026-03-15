<?php

use App\Models\User;

// Dataset kept here as it is specific to storage validation
dataset('invalid_quotes', [
    'empty' => [''],
    'too short' => ['ab'],
    'too long' => [str_repeat('a', 1001)],
]);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('user can save valid public quote', function() {
    $data = ['content' => 'This is a brand new quote for the mura feed.'];

    $this->actingAs($this->user)
        ->post(route('quotes.store'), $data)
        ->assertRedirect('/dashboard');

    $this->assertDatabaseHas('quotes', [
        'user_id' => $this->user->id,
        'content' => $data['content'],
        'is_private' => false,
    ]);
});

test('user can save valid private quote', function () {
    $data = [
        'content' => 'This is a secret quote.',
        'is_private' => true,
    ];

    $this->actingAs($this->user)
        ->post(route('quotes.store'), $data)
        ->assertRedirect('/dashboard');

    $this->assertDatabaseHas('quotes', [
        'user_id' => $this->user->id,
        'is_private' => true,
    ]);
});

test('reject invalid quotes', function ($invalidContent) {
    $this->actingAs($this->user)
        ->post(route('quotes.store'), ['content' => $invalidContent])
        ->assertSessionHasErrors('content');
})->with('invalid_quotes');

test('reject an invalid privacy flag', function () {
    $this->actingAs($this->user)
        ->post(route('quotes.store'), [
            'content' => 'Valid quote.',
            'is_private' => 'not-a-boolean',
        ])
        ->assertSessionHasErrors('is_private');
});

test('rate limits a user to 5 quotes per minute', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->actingAs($this->user)
            ->post(route('quotes.store'), ['content' => "Quote {$i}"])
            ->assertRedirect('/dashboard');
    }

    $this->actingAs($this->user)
        ->post(route('quotes.store'), ['content' => 'Spam'])
        ->assertStatus(429);
});