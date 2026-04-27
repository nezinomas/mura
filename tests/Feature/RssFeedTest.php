<?php

use App\Models\Quote;
use App\Models\User;

test('user profile has an accessible rss feed returning xml', function () {
    // Arrange: Create an author
    $author = User::factory()->create();

    // Act: Request the feed endpoint
    $response = $this->get('/' . $author->name . '/feed');

    // Assert: It loads and returns the correct content type
    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/xml');
});

test('rss feed contains public thoughts and entirely excludes private ones', function () {
    // Arrange: Create an author with specific names
    $author = User::factory()->create([
        'name' => 'seneca_dev',
        'display_name' => 'Seneca the Younger'
    ]);

    // Create a public thought
    Quote::factory()->create([
        'user_id' => $author->id,
        'content' => 'This is a public thought meant for the RSS reader.',
        'is_private' => false,
    ]);

    // Create a private thought
    Quote::factory()->create([
        'user_id' => $author->id,
        'content' => 'This is a hidden thought that should never leak.',
        'is_private' => true,
    ]);

    // Act: Request the feed
    $response = $this->get('/' . $author->name . '/feed');

    // Assert: The XML contains the right data and hides the private data
    $response->assertSee('Seneca the Younger');
    $response->assertSee('This is a public thought meant for the RSS reader.');
    $response->assertDontSee('This is a hidden thought that should never leak.');
});


test('user profile page contains a link to their rss feed', function () {
    // Arrange
    $author = User::factory()->create();
    $visitor = User::factory()->create();

    // Act: Visit the author's profile
    $response = $this->actingAs($visitor)->get('/' . $author->name);

    // Assert: The page loads and contains the generated feed URL
    $response->assertOk();
    $response->assertSee(route('users.feed', $author));
});