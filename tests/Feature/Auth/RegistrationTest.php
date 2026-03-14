<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});


test('registration uses the custom username rule', function () {
    $response = $this->post('/register', [
        'name' => 'bad name',
        'email' => 'newuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('name');
    
    $this->assertDatabaseMissing('users', [
        'email' => 'newuser@example.com'
    ]);
});


test('registration requires name of at least 3 characters', function () {
    $response = $this->post('/register', [
        'name' => 'ab',
        'email' => 'newuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('name');
});

test('registration rejects name longer than 25 characters', function () {
    $response = $this->post('/register', [
        'name' => str_repeat('a', 26),
        'email' => 'newuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('name');
});


test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test_User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
