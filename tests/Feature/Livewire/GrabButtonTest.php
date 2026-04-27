<?php

use App\Models\Quote;
use App\Models\User;
use Livewire\Livewire;

test('grab button toggles grab status and updates UI text', function () {
    // 1. Arrange: Create the author, the logged-in user, and a public quote
    $author = User::factory()->create();
    $user = User::factory()->create();

    $quote = Quote::factory()->create([
        'user_id' => $author->id,
        'is_private' => false,
    ]);

    // 2. Act & Assert (Initial State -> Grab)
    Livewire::actingAs($user)
        ->test('App\Livewire\GrabButton', ['quote' => $quote])
        ->assertSee('Grab')           // UI should initially say Grab
        ->assertDontSee('Ungrab')
        ->call('toggle')              // Click the button
        ->assertSee('Ungrab');        // UI should instantly update

    // 3. Verify Database: The user successfully grabbed it
    expect($user->grabs()->where('quote_id', $quote->id)->exists())->toBeTrue();

    // 4. Act & Assert (Second Click -> Ungrab)
    Livewire::actingAs($user)
        ->test('App\Livewire\GrabButton', ['quote' => $quote])
        ->call('toggle')              // Click it again
        ->assertSee('Grab');          // UI reverts

    // 5. Verify Database: The user successfully ungrabbed it
    expect($user->grabs()->where('quote_id', $quote->id)->exists())->toBeFalse();
});