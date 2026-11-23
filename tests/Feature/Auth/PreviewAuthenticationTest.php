<?php

beforeEach(function () {
    config()->set([
        'preview.enabled' => true,
        'preview.username' => 'admin',
        'preview.password' => 'admin',
    ]);
});

it('routes the application entry point to the login screen', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect(route('login'));
});

it('renders the database-free preview login form when preview access is enabled', function () {
    $response = $this->get(route('login'));

    $response
        ->assertOk()
        ->assertSee('Username')
        ->assertSee('Password')
        ->assertSee('data-test="login-button"', false);
    $this->assertDatabaseCount('users', 0);
});

it('starts a preview session with the configured credentials', function () {
    $this->withSession(['session_marker' => 'before-login']);
    $previousSessionId = session()->getId();

    $response = $this->post(route('preview.login'), [
        'username' => 'admin',
        'password' => 'admin',
    ]);

    $response
        ->assertRedirect(route('dashboard', absolute: false))
        ->assertSessionHas('preview.user', [
            'username' => 'admin',
            'name' => 'Admin',
        ]);
    expect(session()->getId())->not->toBe($previousSessionId);
    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
});

it('rejects invalid preview credentials without starting a session', function () {
    $response = $this->from(route('login'))->post(route('preview.login'), [
        'username' => 'admin',
        'password' => 'incorrect',
    ]);

    $response
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('username')
        ->assertSessionMissing('preview.user');
    $this->assertGuest();
});

it('redirects guests to login before rendering the dashboard', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

it('ends a preview session without using the authentication guard', function () {
    $response = $this
        ->withSession([
            'preview.user' => [
                'username' => 'admin',
                'name' => 'Admin',
            ],
        ])
        ->post(route('preview.logout'));

    $response
        ->assertRedirect(route('login'))
        ->assertSessionMissing('preview.user');
    $this->assertGuest();
});

it('does not expose the preview login endpoint when preview access is disabled', function () {
    config()->set('preview.enabled', false);

    $response = $this->post(route('preview.login'), [
        'username' => 'admin',
        'password' => 'admin',
    ]);

    $response->assertNotFound();
});
