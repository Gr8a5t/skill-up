<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Home'));
});

test('the about page returns a successful inertia response', function () {
    $response = $this->get('/about');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('About'));
});

