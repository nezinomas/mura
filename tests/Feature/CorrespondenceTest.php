<?php

use Illuminate\Support\Facades\Mail;

test('anyone can view the correspondence page', function () {
    $this->get('/correspondence')
        ->assertOk()
        ->assertSee('Correspondence');
});

test('a user can send a letter', function () {
    Mail::fake();

    $response = $this->post('/correspondence', [
        'email' => 'reader@example.com',
        'message' => 'I love the quiet aesthetic of this space.',
    ]);

    $response->assertRedirect()
             ->assertSessionHas('success', 'Your letter has been sent.');

    // Assert that our specific class was sent!
    Mail::assertSent(\App\Mail\CorrespondenceLetter::class); 
});

test('a letter requires a message', function () {
    $response = $this->post('/correspondence', [
        'email' => 'reader@example.com',
        'message' => '', // Empty message
    ]);

    $response->assertSessionHasErrors('message');
});