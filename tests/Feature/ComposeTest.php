<?php

use App\Models\User;


dataset('invalid_thoughts', [
    'empty' => [''],
    'too short' => ['ab'],
    'too long' => [str_repeat('a', 1001)],
]);


beforeEach(function () {
    $this->user = User::factory()->create();
});


test('guest cannot see compose page', function() {
    $reponse = $this->get('/compose');

    $reponse->assertRedirect('/login');
});


test('authenticated user can see compose page', function() {
    $response = $this->actingAs($this->user)->get('/compose');

    $response->assertStatus(200);

    $response->assertViewIs('compose');
});


test('compose page contains required html form elements', function () {
    $response = $this->actingAs($this->user)->get('/compose');

    // We strictly demand the presence of these physical HTML tags.
    // The 'false' parameter tells Pest not to escape the < > brackets.
    $response->assertSee('<form method="POST" action="/compose"', false);
    $response->assertSee('<textarea', false);
    $response->assertSee('name="content"', false);
    $response->assertSee('<button type="submit"', false);

    $response->assertSee('x-data="{ isPrivate: false }"', false); // The Alpine state engine
    $response->assertSee('name="is_private"', false);             // The hidden checkbox for the server
});


test('user can save valid public thought', function() {
    $response = $this->actingAs($this->user)->post('/compose', [
        'content' => 'This is a brand new thought for the mura feed.'
    ]);

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('quotes', [
        'user_id' => $this->user->id,
        'content' => 'This is a brand new thought for the mura feed.',
        'is_private' => false,
    ]);
});


test('user can save valid private thought', function () {
    $response = $this->actingAs($this->user)->post('/compose', [
        'content' => 'This is a secret thought.',
        'is_private' => true,
    ]);

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('quotes', [
        'user_id' => $this->user->id,
        'content' => 'This is a secret thought.',
        'is_private' => true,
    ]);
});


test('reject invalid thoughts', function ($invalidContent) {
    $response = $this->actingAs($this->user)->post('/compose', [
        'content' => $invalidContent,
    ]);

    $response->assertSessionHasErrors('content');
})->with('invalid_thoughts');


test('reject an invalid privacy flag', function () {
    $response = $this->actingAs($this->user)->post('/compose', [
        'content' => 'This is a perfectly valid thought.',
        'is_private' => 'this-is-not-a-boolean',
    ]);
    $response->assertSessionHasErrors('is_private');
});


test('rate limits a user to 5 thoughts per minute', function () {
    // A bot successfully posts 5 times in a single second
    for ($i = 0; $i < 5; $i++) {
        $this->actingAs($this->user)->post('/compose', [
            'content' => "This is valid thought number {$i}.",
        ])->assertRedirect('/dashboard');
    }

    // The bot aggressively tries to post a 6th time...
    $response = $this->actingAs($this->user)->post('/compose', [
        'content' => 'This is the spam thought.',
    ]);

    // The bouncer violently rejects the request with a "429 Too Many Requests" status code
    $response->assertStatus(429);
});