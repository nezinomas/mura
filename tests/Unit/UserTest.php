<?php

use App\Models\User;
use Illuminate\Database\QueryException;


test('user has a unique name', function () {
    $create_user = fn() => User::factory()->create(['name' => 'nezinomas']);
    $create_user();

    expect($create_user)->toThrow(QueryException::class);
});


test('user is stored as lower case', function() {
    $user = User::factory()->create(['name' => 'Nezinomas']);

    expect($user->name)->toBe('nezinomas');
});


dataset('invalid_usernames', [
    'contains a space' => ['lukas dev'],
    'contains a dash' => ['lukas-dev'],
    'contains a symbol' => ['lukas@dev'],
    'contains a emoji' => ['lukas😊'],
]);


test('user name invalid formats', function(string $name) {
    $user = User::factory()->make(['name' => $name]);

    expect((bool) preg_match('/^\w+$/', $user->name))->toBeFalse();
})->with('invalid_usernames');


dataset('valid_usernames', [
    'only letters' => ['lukas'],
    'with undescore' => ['lukas_dev'],
    'with numbers' => ['lukas123'],
]);


test('a user name valid formats', function(string $name) {
    $user = User::factory()->make(['name' => $name]);

    expect((bool) preg_match('/^\w+$/', $user->name))->toBeTrue();
})->with('valid_usernames');


test('user name automatically stripped whitespace', function() {
    $user = User::factory()->make(['name' => '  spaces  ']);
    expect($user->name)->toBe('spaces');
});


test('dispay_name default to name on creation', function() {
    $user = User::create([
        'name' => 'ghost_writer',
        'email' => 'ghost@mura.test',
        'password' => bcrypt('password'),
    ]);

    expect($user->display_name)->toBe('ghost_writer');
});


test('display_name can be manually set to differ from name', function() {
    $user = User::factory()->create([
        'name' => 'lukas_dev',
        'display_name' => 'Nobody'
    ]);

    expect($user->name)->toBe('lukas_dev');
    expect($user->display_name)->toBe('Nobody');
});


test('email must be unique', function() {
    $user = fn() => User::factory()->create(['email' => 'hello@mura.com']);

    $user();

    expect($user)->toThrow(QueryException::class);
});


test('email address is automatically stripped of whitespace', function () {
    $user = User::factory()->create(['email' => '  padded@mura.test  ']);

    expect($user->email)->toBe('padded@mura.test');
});