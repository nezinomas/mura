<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guest cannot see quotes create', function() {
    $this->get(route('quotes.create'))
        ->assertRedirect('/login');
});

test('authenticated user can see quotes create', function() {
    $this->actingAs($this->user)
        ->get(route('quotes.create'))
        ->assertStatus(200)
        ->assertViewIs('quotes.create');
});

test('quote create contains required html form elements', function () {
    $storeActionUrl = route('quotes.store');

    $this->actingAs($this->user)
        ->get(route('quotes.create'))
        ->assertSee("<form method=\"POST\" action=\"{$storeActionUrl}\"", false)
        ->assertSee('<textarea', false)
        ->assertSee('name="content"', false)
        ->assertSee('<button type="submit"', false)
        ->assertSee('x-data="{ isPrivate: false }"', false)
        ->assertSee('name="is_private"', false);
});