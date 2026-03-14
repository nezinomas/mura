<?php

use App\Rules\ValidUsername;
use Illuminate\Support\Facades\Validator;


dataset('valid_usernames', [
    'only letters' => ['lukas'],
    'with undescore' => ['lukas_dev'],
    'with numbers' => ['lukas123'],
]);


dataset('invalid_usernames', [
    'contains a space' => ['lukas dev'],
    'contains a dash' => ['lukas-dev'],
    'contains a symbol' => ['lukas@dev'],
    'contains a emoji' => ['lukas😊'],
]);


test('user name invalid formats', function(string $name) {
    $validator = Validator::make(
        ['name' => $name],
        ['name' => new ValidUsername()]
    );

    expect($validator->fails())->toBeTrue();
})->with('invalid_usernames');


test('a user name valid formats', function(string $name) {
    $validator = Validator::make(
        ['name' => $name],
        ['name' => new ValidUsername()]
    );
    
    expect($validator->fails())->toBeFalse();
})->with('valid_usernames');