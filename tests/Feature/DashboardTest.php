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

test('Home and profile actions are in the toolbar and Dashboard stays in the sidebar', function () {
    $this->actingAs(User::factory()->create());

    $response = $this->get(route('subscribers.index'))->assertOk();
    $homeUrl = preg_quote(url('/home'), '/');
    $dashboardUrl = preg_quote(route('dashboard'), '/');

    $html = $response->getContent();

    expect($html)
        ->toMatch('/<header\b[^>]*aria-label="Administration toolbar"[^>]*>.*?<a\b[^>]*href="'.$homeUrl.'"[^>]*aria-label="RODJEN Technet Home"/s')
        ->toMatch('/<a\b[^>]*href="'.$dashboardUrl.'"[^>]*>\s*Dashboard\s*<\/a>/')
        ->not->toMatch('/<a\b[^>]*href="'.$dashboardUrl.'"[^>]*>\s*Overview\s*<\/a>/');
    expect(substr_count($html, 'href="'.route('panel.home').'"'))->toBe(1);
    expect(substr_count($html, 'data-test="logout-button"'))->toBe(1);
    expect($html)->toMatch('/id="home-profile-menu".*?data-test="logout-button"/s');
});

test('panel pages display the shared toolbar without replacing their content', function (string $route, string $title) {
    $this->actingAs(User::factory()->create());

    $this->get(route($route))
        ->assertOk()
        ->assertSee('aria-label="Administration toolbar"', false)
        ->assertSee('id="home-page-search"', false)
        ->assertSee('aria-label="Profile menu"', false)
        ->assertSee('>'.$title.'</h1>', false)
        ->assertDontSee(' data-flux-main', false)
        ->assertDontSee('id="calendar-pane"', false);
})->with([
    'dashboard' => ['dashboard', 'Dashboard'],
    'subscriber records' => ['subscribers.index', 'Subscriber records'],
]);

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
