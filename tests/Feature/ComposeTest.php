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
});


test('user can save valid thought', function() {
    $response = $this->actingAs($this->user)->post('/compose', [
        'content' => 'This is a brand new thought for the mura feed.'
    ]);

    $response->assertRedirect('/home');

    $this->assertDatabaseHas('quotes', [
        'user_id' => $this->user->id,
        'content' => 'This is a brand new thought for the mura feed.',
    ]);
});


test('reject invalid thoughts', function ($invalidContent) {
    $response = $this->actingAs($this->user)->post('/compose', [
        'content' => $invalidContent,
    ]);

    $response->assertSessionHasErrors('content');
})->with('invalid_thoughts');


test('rate limits a user to 5 thoughts per minute', function () {
    // A bot successfully posts 5 times in a single second
    for ($i = 0; $i < 5; $i++) {
        $this->actingAs($this->user)->post('/compose', [
            'content' => "This is valid thought number {$i}.",
        ])->assertRedirect('/home'); 
    }

    // The bot aggressively tries to post a 6th time...
    $response = $this->actingAs($this->user)->post('/compose', [
        'content' => 'This is the spam thought.',
    ]);

    // The bouncer violently rejects the request with a "429 Too Many Requests" status code
    $response->assertStatus(429);
});