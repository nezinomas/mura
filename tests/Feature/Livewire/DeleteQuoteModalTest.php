<?php

use App\Models\Quote;
use App\Models\User;
use Livewire\Livewire;


beforeEach(function() {
    $this->user = User::factory()->create();
    $this->quote = Quote::factory()->create(['user_id' => $this->user->id]);
});


test('delete modal shows standard message when quote is not grabbed', function () {
    Livewire::actingAs($this->user)
        ->test('App\Livewire\DeleteQuoteModal')
        ->dispatch('confirmDelete', quoteId: $this->quote->id)
        ->assertSee('Are you sure you want to delete this thought?');
});

test('delete modal shows warning message when quote is grabbed', function () {
    $otherUser = User::factory()->create();
    $otherUser->grabs()->attach($this->quote);

    Livewire::actingAs($this->user)
        ->test('App\Livewire\DeleteQuoteModal')
        ->dispatch('confirmDelete', quoteId: $this->quote->id)
        ->assertSee('This thought will remain visible on the global feed forever. Are you sure to disown it?');
});

test('delete modal successfully destroys the quote', function () {
    Livewire::actingAs($this->user)
        ->test('App\Livewire\DeleteQuoteModal')
        ->dispatch('confirmDelete', quoteId: $this->quote->id)
        ->call('destroy')
        ->assertRedirect('/dashboard');

    $this->assertDatabaseMissing('quotes', ['id' => $this->quote->id]);
});

test('guest cannot delete a quote', function () {
    Livewire::test('App\Livewire\DeleteQuoteModal')
        ->dispatch('confirmDelete', quoteId: $this->quote->id)
        ->call('destroy')
        ->assertForbidden();
});

test('user cannot delete another users quote', function () {
    $otherUser = User::factory()->create();

    Livewire::actingAs($otherUser)
        ->test('App\Livewire\DeleteQuoteModal')
        ->dispatch('confirmDelete', quoteId: $this->quote->id)
        ->call('destroy')
        ->assertForbidden();
});

test('delete modal disowns the quote if it is grabbed instead of deleting', function () {
    // 1. Arrange: Another user grabs the quote
    $otherUser = User::factory()->create();
    $otherUser->grabs()->attach($this->quote);

    // 2. Act: The original author clicks confirm on the modal
    Livewire::actingAs($this->user)
        ->test('App\Livewire\DeleteQuoteModal')
        ->dispatch('confirmDelete', quoteId: $this->quote->id)
        ->call('destroy')
        ->assertRedirect('/dashboard');

    // 3. Assert: The quote still exists, but the user_id is now null (disowned)
    $this->assertDatabaseHas('quotes', [
        'id' => $this->quote->id,
        'user_id' => null,
    ]);
});