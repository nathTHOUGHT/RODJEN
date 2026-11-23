<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('preview session can visit the text-only admin shell', function () {
    config()->set('preview.enabled', true);

    $response = $this
        ->withSession([
            'preview.user' => [
                'username' => 'admin',
                'name' => 'Admin',
            ],
        ])
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('Administration Panel')
        ->assertSee('Dashboard')
        ->assertSee('data-test="logout-button"', false)
        ->assertDontSee('<img', false)
        ->assertDontSee('<svg', false);
});
