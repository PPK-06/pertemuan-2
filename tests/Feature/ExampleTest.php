<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    // '/' redirect ke dashboard untuk guest maupun user login.
    $response->assertRedirect(route('dashboard'));
});
