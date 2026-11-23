<?php

test('routes visitors to the login screen', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect(route('login'));
});
