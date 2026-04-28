<?php

test('anyone can view the rules page', function () {
    $this->get('/rules')
        ->assertOk()
        ->assertSee('House Rules')
        ->assertSee('The Ink Dries'); // Checking for our 24-hour rule section
});