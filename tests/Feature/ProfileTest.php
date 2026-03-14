<?php

use Illuminate\Support\Facades\Hash;
use App\Models\User;

beforeEach(function() {
    $this->user = User::factory()->create();
});


test('profile page is displayed', function () {
    $response = $this
        ->actingAs($this->user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $response = $this
        ->actingAs($this->user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->user->refresh();

    $this->assertSame('test user', $this->user->name);
    $this->assertSame('test@example.com', $this->user->email);
    $this->assertNull($this->user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $response = $this
        ->actingAs($this->user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $this->user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($this->user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $response = $this
        ->actingAs($this->user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($this->user->fresh());
});

test('correct password must be provided to delete account', function () {
    $response = $this
        ->actingAs($this->user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($this->user->fresh());
});


test('change password page is displayed', function() {
    $response = $this
        ->actingAs($this->user)
        ->get('change-password');

    $response->assertOk();
});


test('change password page contains the correct form inputs', function () {
    $response = $this
        ->actingAs($this->user)
        ->get('/change-password');

    $response->assertOk();

    $response->assertSee('name="current_password"', false);
    $response->assertSee('name="password"', false);
    $response->assertSee('name="password_confirmation"', false);

    $response->assertSee('name="_method" value="put"', false);
});


test('change password view displays validation errors to the user', function () {
    $response = $this
        ->actingAs($this->user)
        ->from('/change-password') 
        ->put('/password', [
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

    $response->assertRedirect('/change-password');

    $this->followRedirects($response)
        ->assertSeeText('The current password field is required')
        ->assertSeeText('The password field is required');
});


test('password was succesfully changed', function() {
    $response = $this
        ->actingAs($this->user)
        ->from('/change-password')
        ->put('/password', [
            'current_password' => 'password',
            'password' => '123-password',
            'password_confirmation' => '123-password'
        ]);

    $this->user->refresh();

    expect(Hash::check('123-password', $this->user->password))->toBeTrue();

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/change-password');
});