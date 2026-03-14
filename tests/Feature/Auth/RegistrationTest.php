<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});


test('registration uses the custom username rule', function () {
    // Act: A guest tries to register with a name that has a space
    $response = $this->post('/register', [
        'name' => 'bad name',
        'email' => 'newuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Assert: The session should have an error specifically for 'name'
    $response->assertSessionHasErrors('name');
    
    // Assert: The user was not created
    $this->assertDatabaseMissing('users', [
        'email' => 'newuser@example.com'
    ]);
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
