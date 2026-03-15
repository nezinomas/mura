<?php

use App\Models\User;


dataset('invalid_quotes', [
    'empty' => [''],
    'too short' => ['ab'],
    'too long' => [str_repeat('a', 1001)],
]);


beforeEach(function () {
    $this->user = User::factory()->create();
});


test('guest cannot see quotes create', function() {
    $reponse = $this->get(route('quotes.create'));

    $reponse->assertRedirect('/login');
});


test('authenticated user can see quotes create', function() {
    $response = $this->actingAs($this->user)->get(route('quotes.create'));

    $response->assertStatus(200);

    $response->assertViewIs('quotes.create');
});


test('quote create contains required html form elements', function () {
    $createPageUrl = route('quotes.create');
    $storeActionUrl = route('quotes.store');

    $response = $this->actingAs($this->user)->get($createPageUrl);

    $response->assertSee("<form method=\"POST\" action=\"{$storeActionUrl}\"", false);
    $response->assertSee('<textarea', false);
    $response->assertSee('name="content"', false);
    $response->assertSee('<button type="submit"', false);

    $response->assertSee('x-data="{ isPrivate: false }"', false);
    $response->assertSee('name="is_private"', false);
});


test('user can save valid public quote', function() {
    $response = $this->actingAs($this->user)->post(route('quotes.store'), [
        'content' => 'This is a brand new quote for the mura feed.'
    ]);

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('quotes', [
        'user_id' => $this->user->id,
        'content' => 'This is a brand new quote for the mura feed.',
        'is_private' => false,
    ]);
});


test('user can save valid private quote', function () {
    $response = $this->actingAs($this->user)->post(route('quotes.store'), [
        'content' => 'This is a secret quote.',
        'is_private' => true,
    ]);

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('quotes', [
        'user_id' => $this->user->id,
        'content' => 'This is a secret quote.',
        'is_private' => true,
    ]);
});


test('reject invalid quotes', function ($invalidContent) {
    $response = $this->actingAs($this->user)->post(route('quotes.store'), [
        'content' => $invalidContent,
    ]);

    $response->assertSessionHasErrors('content');
})->with('invalid_quotes');


test('reject an invalid privacy flag', function () {
    $response = $this->actingAs($this->user)->post(route('quotes.store'), [
        'content' => 'This is a perfectly valid quote.',
        'is_private' => 'this-is-not-a-boolean',
    ]);
    $response->assertSessionHasErrors('is_private');
});


test('rate limits a user to 5 quotes per minute', function () {
    // A bot successfully posts 5 times in a single second
    for ($i = 0; $i < 5; $i++) {
        $this->actingAs($this->user)->post(route('quotes.store'), [
            'content' => "This is valid quote number {$i}.",
        ])->assertRedirect('/dashboard');
    }

    // The bot aggressively tries to post a 6th time...
    $response = $this->actingAs($this->user)->post(route('quotes.store'), [
        'content' => 'This is the spam quote.',
    ]);

    // Reject request with a "429 Too Many Requests" status code
    $response->assertStatus(429);
});