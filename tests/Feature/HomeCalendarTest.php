<?php

use App\Models\User;
use Carbon\CarbonImmutable;

test('Home displays the calendar workspace and scheduled bills preview', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('panel.home'))
        ->assertOk()
        ->assertSeeInOrder(['Calendar', 'Add event', 'Reminders', 'Scheduled bills', 'No scheduled bills to display'])
        ->assertSee('id="calendar-pane"', false)
        ->assertSee('id="scheduled-bills"', false)
        ->assertSee('id="scheduled-bill-dialog"', false)
        ->assertSee('id="reminders"', false);
});

test('staff can leave a note that another staff member can see', function () {
    $author = User::factory()->create(['name' => 'Alex Rivera']);
    $colleague = User::factory()->create();
    $date = '2026-11-26';
    $title = '<b>Fibre maintenance</b>';

    $this->actingAs($author)
        ->post(route('panel.home.reminders.store'), [
            'date' => $date,
            'title' => $title,
            'body' => 'Check the affected connections after the maintenance window.',
            'repeat' => 'none',
            'view' => 'week',
            'author_name' => 'Someone else',
        ])
        ->assertRedirect(route('panel.home', ['view' => 'week', 'date' => $date]))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('staff_reminders', ['title' => $title, 'author_name' => $author->name]);

    $this->actingAs($colleague)
        ->get(route('panel.home', ['view' => 'day', 'date' => $date]))
        ->assertOk()
        ->assertSee($title)
        ->assertDontSee($title, false)
        ->assertSee('Check the affected connections')
        ->assertSee($author->name);

    $this->get(route('panel.home', ['view' => 'day', 'date' => '2026-11-27']))
        ->assertOk()
        ->assertDontSee('Fibre maintenance');
});

test('the calendar displays daily weekly and monthly repeats on their scheduled dates', function () {
    $this->actingAs(User::factory()->create());

    foreach (['daily' => 'Daily checks', 'weekly' => 'Weekly checks', 'monthly' => 'Monthly checks'] as $repeat => $title) {
        $this->post(route('panel.home.reminders.store'), [
            'title' => $title,
            'date' => '2026-05-31',
            'repeat' => $repeat,
            'view' => 'month',
        ])->assertSessionHasNoErrors();
    }

    foreach ([
        '2026-05-30' => [],
        '2026-05-31' => ['Daily checks', 'Weekly checks', 'Monthly checks'],
        '2026-06-01' => ['Daily checks'],
        '2026-06-07' => ['Daily checks', 'Weekly checks'],
        '2026-06-30' => ['Daily checks', 'Monthly checks'],
        '2026-07-31' => ['Daily checks', 'Monthly checks'],
        '2027-02-28' => ['Daily checks', 'Weekly checks', 'Monthly checks'],
        '2028-02-29' => ['Daily checks', 'Monthly checks'],
    ] as $date => $expectedTitles) {
        $response = $this->get(route('panel.home', ['view' => 'day', 'date' => $date]))->assertOk();

        expect(collect($response->viewData('days'))->sole()['notes']->pluck('title')->all())
            ->toEqualCanonicalizing($expectedTitles);
    }

    $this->assertDatabaseCount('staff_reminders', 3);
});

test('the calendar displays a Monday first week and a month including adjacent days', function () {
    $this->actingAs(User::factory()->create());

    $week = $this->get(route('panel.home', ['view' => 'week', 'date' => '2026-11-26']))
        ->assertOk()
        ->assertViewHas('mode', 'week');
    $weekDates = collect($week->viewData('days'))->pluck('date');

    expect($weekDates)->toHaveCount(7);
    expect($weekDates->first()->toDateString())->toBe('2026-11-23');
    expect($weekDates->last()->toDateString())->toBe('2026-11-29');

    $month = $this->get(route('panel.home', ['view' => 'month', 'date' => '2026-11-26']))
        ->assertOk()
        ->assertViewHas('mode', 'month');
    $monthDays = collect($month->viewData('days'));

    expect($monthDays->first()['date']->toDateString())->toBe('2026-10-26');
    expect($monthDays->last()['date']->toDateString())->toBe('2026-12-06');
    expect($monthDays->where('inPeriod', true))->toHaveCount(30);
});

test('invalid calendar query parameters fall back to the current month', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('panel.home', ['view' => ['week'], 'date' => '2026-02-31']))
        ->assertOk()
        ->assertViewHas('mode', 'month')
        ->assertViewHas('selectedDate', fn (CarbonImmutable $date) => $date->toDateString() === CarbonImmutable::today('Asia/Singapore')->toDateString());
});

test('the calendar follows the Singapore date around midnight', function () {
    $this->actingAs(User::factory()->create());
    $this->post(route('panel.home.reminders.store'), [
        'date' => '2026-11-27',
        'title' => 'Morning maintenance',
        'repeat' => 'none',
    ])->assertSessionHasNoErrors();

    CarbonImmutable::withTestNow(CarbonImmutable::parse('2026-11-26 17:00:00', 'UTC'), function () {
        $this->get(route('panel.home', ['view' => 'day']))
            ->assertOk()
            ->assertViewHas('today', fn (CarbonImmutable $date) => $date->toDateString() === '2026-11-27')
            ->assertViewHas('selectedDate', fn (CarbonImmutable $date) => $date->toDateString() === '2026-11-27')
            ->assertSee('Morning maintenance');
    });
});

test('staff reminder submissions reject invalid values', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('panel.home.reminders.store'), [
            'title' => str_repeat('a', 81),
            'body' => str_repeat('a', 1001),
            'date' => '2026-02-31',
            'repeat' => 'yearly',
            'view' => 'agenda',
        ])
        ->assertSessionHasErrors(['title', 'body', 'date', 'repeat', 'view']);

    $this->assertDatabaseEmpty('staff_reminders');
});

test('guests cannot read or create staff reminders', function () {
    $this->get(route('panel.home'))->assertRedirect(route('login'));
    $this->post(route('panel.home.reminders.store'))->assertRedirect(route('login'));
});

test('the note form stays available after malformed fields are rejected', function () {
    $calendar = route('panel.home', ['view' => 'day', 'date' => '2026-11-26']);

    $this->actingAs(User::factory()->create())
        ->from($calendar)
        ->followingRedirects()
        ->post(route('panel.home.reminders.store'), [
            'title' => ['Invalid title'],
            'body' => ['Invalid note'],
            'date' => ['Invalid date'],
            'repeat' => 'none',
        ])
        ->assertOk()
        ->assertSee('aria-invalid="true"', false)
        ->assertDontSee('Invalid title');
});

test('unverified users must verify their email before accessing reminders', function () {
    $this->actingAs(User::factory()->unverified()->create())
        ->get(route('panel.home'))
        ->assertRedirect(route('verification.notice'));

    $this->post(route('panel.home.reminders.store'))->assertRedirect(route('verification.notice'));
});

test('the local preview can post under its session name when enabled', function () {
    config()->set('preview.enabled', true);

    $this->withSession(['preview.user' => ['username' => 'admin', 'name' => 'Preview Admin']])
        ->post(route('panel.home.reminders.store'), [
            'title' => 'Preview note',
            'date' => '2026-11-26',
            'repeat' => 'none',
        ])
        ->assertRedirect(route('panel.home', ['view' => 'month', 'date' => '2026-11-26']));

    $this->assertDatabaseHas('staff_reminders', ['title' => 'Preview note', 'author_name' => 'Preview Admin']);
});
