<?php

use App\Models\User;

dataset('admin page routes', [
    'add subscriber' => ['subscribers.create', 'Add subscriber'],
    'subscriber records' => ['subscribers.index', 'Subscriber records'],
    'subscriber details' => ['subscribers.details', 'Subscriber details'],
    'products and services' => ['products.index', 'Products and services'],
    'policies and conditions' => ['policies.index', 'Policies and conditions'],
    'reports overview' => ['reports.index', 'Reports'],
    'daily accounting report' => ['reports.daily', 'Daily accounting report'],
    'payment and transaction history' => ['reports.transactions', 'Payment and transaction history'],
    'expenses' => ['reports.expenses', 'Expenses'],
    'system logs' => ['logs.system', 'System logs'],
    'activity logs' => ['logs.activity', 'Activity logs'],
    'router settings' => ['router.index', 'Router settings'],
    'router backups' => ['router.backups', 'Router backups'],
    'staff users' => ['staff.index', 'Staff users'],
    'messaging' => ['integrations.messaging', 'Messaging'],
    'scheduler' => ['integrations.scheduler', 'Scheduler'],
    'integration settings' => ['integrations.settings', 'Integration settings'],
]);

test('guests cannot access admin pages', function (string $route) {
    $this->get(route($route))->assertRedirect(route('login'));
})->with('admin page routes');

test('local preview sessions can access admin pages', function (string $route, string $heading) {
    config()->set('preview.enabled', true);

    $this
        ->followingRedirects()
        ->withSession([
            'preview.user' => [
                'username' => 'admin',
                'name' => 'Admin',
            ],
        ])
        ->get(route($route))
        ->assertOk()
        ->assertSee($heading);
})->with('admin page routes');

test('unverified authenticated users cannot access admin pages', function (string $route) {
    $user = User::factory()->unverified()->create();

    $this
        ->actingAs($user)
        ->get(route($route))
        ->assertRedirect(route('verification.notice'));
})->with('admin page routes');

test('verified authenticated users can access admin pages', function (string $route, string $heading) {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->followingRedirects()
        ->get(route($route))
        ->assertOk()
        ->assertSee($heading);
})->with('admin page routes');
