<?php

namespace Tests\Feature\Search;

use App\Models\Quote;
use App\Models\User;

test('the search page is accessible', function () {
    $this->get('/search')
        ->assertOk()
        ->assertSee('Search'); // We will ensure the view has a "Search" title or placeholder
});

test('it can search for public quotes by text', function () {
    $user = User::factory()->create();

    // The quote we want to find
    Quote::factory()->create([
        'user_id' => $user->id,
        'content' => 'This is a unique analog typewriter thought.',
        'is_private' => false,
    ]);

    // The quote we want to ignore
    Quote::factory()->create([
        'user_id' => $user->id,
        'content' => 'Another random thought about the weather.',
        'is_private' => false,
    ]);

    $this->get('/search?q=analog')
        ->assertOk()
        ->assertSee('This is a unique analog typewriter thought.')
        ->assertDontSee('Another random thought about the weather.');
});

test('it does not show private quotes from other users in search results', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    // A private quote containing our search keyword
    Quote::factory()->create([
        'user_id' => $otherUser->id,
        'content' => 'This is a secret analog thought.',
        'is_private' => true,
    ]);

    $this->actingAs($user)
        ->get('/search?q=analog')
        ->assertOk()
        ->assertDontSee('This is a secret analog thought.');
});

test('search results are paginated correctly', function () {
    $user = User::factory()->create();

    // 1. Create 25 unique quotes, using %02d to generate 01, 02... 25
    for ($i = 1; $i <= 25; $i++) {
        $num = sprintf('%02d', $i);
        
        Quote::factory()->create([
            'user_id' => $user->id,
            'content' => "This is typewriter thought number {$num}",
            'is_private' => false,
            'created_at' => now()->subMinutes(25 - $i), 
        ]);
    }

    // 2. Act: Search without a page parameter (defaults to Page 1)
    $responsePage1 = $this->get('/search?q=typewriter');

    // 3. Assert Page 1: 
    $responsePage1->assertOk()
        ->assertSee('typewriter thought number 25') // Newest
        ->assertSee('typewriter thought number 06')  // 20th newest
        ->assertDontSee('typewriter thought number 05') // 21st newest
        ->assertDontSee('typewriter thought number 01'); // Oldest

    // 4. Act: Request Page 2 explicitly
    $responsePage2 = $this->get('/search?q=typewriter&page=2');

    // 5. Assert Page 2:
    $responsePage2->assertOk()
        ->assertSee('typewriter thought number 05')
        ->assertSee('typewriter thought number 01')
        ->assertDontSee('typewriter thought number 25'); // Shouldn't see page 1 stuff
});