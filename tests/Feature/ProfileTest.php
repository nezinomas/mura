<?php

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Quote;


beforeEach(function() {
    $this->user = User::factory()->create();
});


test('profile page is displayed', function () {
    $response = $this
        ->actingAs($this->user)
        ->get('/profile');

    $response->assertOk();
});


test('profile has form fields email user_name display_name', function() {
    $response = $this
    ->actingAs($this->user)
    ->get('/profile');

    $response->assertOk();

    $response->assertSee('name="name"', false);
    $response->assertSee('name="email', false);
    $response->assertSee('name="display_name', false);
});


test('profile update uses the custom username rule', function () {
    $response = $this
        ->actingAs($this->user)
        ->patch('/profile', [
            'name' => 'Bad name',
            'email' => 'test@test.com',
        ]);

    $response->assertSessionHasErrors('name');

    $this->assertDatabaseMissing('users', [
        'name' => 'Bad name'
    ]);
});


test('profile update requires a username of at least 3 characters', function () {
    $response = $this
        ->actingAs($this->user)
        ->patch('/profile', [
            'name' => 'ab', 
            'email' => $this->user->email,
        ]);

    $response->assertSessionHasErrors('name');
});

test('profile update rejects a username longer than 25 characters', function () {
    $longName = str_repeat('a', 26);
    
    $response = $this
        ->actingAs($this->user)
        ->patch('/profile', [
            'name' => $longName, 
            'email' => $this->user->email,
        ]);

    $response->assertSessionHasErrors('name');
});


test('profile information can be updated', function () {
    $response = $this
        ->actingAs($this->user)
        ->patch('/profile', [
            'name' => 'TestUser',
            'email' => 'test@example.com',
            'display_name' => "Test User",
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->user->refresh();

    $this->assertSame('testuser', $this->user->name);
    $this->assertSame('test@example.com', $this->user->email);
    $this->assertNull($this->user->email_verified_at);
});


dataset('invalid_display_names', [
    'more than 51' => [str_repeat('a', 51)],
    'less than 3' => ['xz'],
    'empty' => [''],
]);

test('profile fails update invalid display_name', function(string $name) {
    $response = $this
    ->actingAs($this->user)
    ->patch('/profile', [
        'name' => $this->user->name,
        'email' => $this->user->email,
        'display_name' => $name,
    ]);

    $response->assertSessionHasErrors('display_name');
})->with('invalid_display_names');


test('email verification status is unchanged when the email address is unchanged', function () {
    $response = $this
        ->actingAs($this->user)
        ->patch('/profile', [
            'name' => 'Test_User',
            'email' => $this->user->email,
            'display_name' => $this->user->display_name,
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


test('user deletion handles thoughts correctly based on grab status', function() {
    $otherUser = User::factory()->create();

    // 1. Private quote
    $privateQuote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'is_private' => true
    ]);

    // 2. Public quote (Nobody grabbed it)
    $publicUngrabbedQuote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'is_private' => false
    ]);

    // 3. Public quote (Grabbed by someone else)
    $publicGrabbedQuote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'is_private' => false
    ]);

    // Simulate someone grabbing the third quote
    $otherUser->grabs()->attach($publicGrabbedQuote);

    // Act: User deletes their own account
    $response = $this
        ->actingAs($this->user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/');

    // Assert: The user is gone
    $this->assertDatabaseMissing('users', ['id' => $this->user->id]);

    // Assert: The private quote is permanently deleted
    $this->assertDatabaseMissing('quotes', ['id' => $privateQuote->id]);

    // Assert: The ungrabbed public quote is permanently deleted
    $this->assertDatabaseMissing('quotes', ['id' => $publicUngrabbedQuote->id]);

    // Assert: The grabbed public quote survives, but belongs to no one
    $this->assertDatabaseHas('quotes', [
        'id' => $publicGrabbedQuote->id,
        'user_id' => null
    ]);
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