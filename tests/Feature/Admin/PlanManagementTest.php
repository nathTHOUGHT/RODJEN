<?php

use App\Models\Plan;
use Database\Seeders\PlanSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    config()->set('preview.enabled', true);
});

function planPreviewSession(): array
{
    return [
        'preview.user' => [
            'username' => 'admin',
            'name' => 'Admin',
        ],
    ];
}

function planAttributes(array $overrides = []): array
{
    return array_merge([
        'name' => 'Fiber Plan 100',
        'profile_group' => 'default-encryption',
        'transaction_type' => 'mrc',
        'speed_mbps' => 100,
        'price' => '1499.50',
    ], $overrides);
}

dataset('invalid plan speeds', [
    'missing' => [null, true],
    'zero' => [0, false],
    'negative' => [-1, false],
    'fractional' => ['0.5', false],
    'array' => [['100'], false],
    'above maximum' => [100001, false],
]);

test('plan seed creates the supplied sample exactly once without resetting recorded usage', function () {
    $this->seed(PlanSeeder::class);

    $plan = Plan::query()->sole();

    expect($plan->usage_count)->toBe(106);

    $plan->increment('usage_count');
    $this->seed(PlanSeeder::class);
    $plan->refresh();

    expect($plan)
        ->name->toBe('ADVOCACY PLAN 649/45MBPS')
        ->profile_group->toBe('pppoe-45mbit/649')
        ->transaction_type->toBe('mrc')
        ->speed_mbps->toBe(45)
        ->price->toBe('649.00')
        ->usage_count->toBe(107)
        ->and($plan->transactionTypeLabel())->toBe('Monthly Recurring Charge (MRC)')
        ->and(Plan::query()->count())->toBe(1);
});

test('plan catalogue can be searched by persisted plan details', function () {
    Plan::query()->create(planAttributes(['name' => 'Fiber Plan 100']));
    Plan::query()->create(planAttributes([
        'name' => 'Office Setup Fee',
        'profile_group' => 'pppoe-45mbit/649',
        'transaction_type' => 'otc',
        'price' => '500.00',
    ]));

    $response = $this
        ->withSession(planPreviewSession())
        ->get(route('products.index', ['q' => 'office']));

    $response
        ->assertOk()
        ->assertViewHas('plans', fn ($plans) => $plans->pluck('name')->all() === ['Office Setup Fee'])
        ->assertViewHas('editingPlan', null)
        ->assertViewHas('profileGroups', Plan::PROFILE_GROUPS)
        ->assertViewHas('transactionTypes', Plan::TRANSACTION_TYPES);
});

test('preview administrator can create a plan', function () {
    $response = $this
        ->withSession(planPreviewSession())
        ->post(route('products.store'), planAttributes());

    $response
        ->assertRedirect(route('products.index'))
        ->assertSessionHas('status', 'Plan added.');

    $this->assertDatabaseHas('plans', [
        'name' => 'Fiber Plan 100',
        'profile_group' => 'default-encryption',
        'transaction_type' => 'mrc',
        'price' => 1499.50,
        'usage_count' => 0,
    ]);

    expect(Plan::query()->sole()->speed_mbps)->toBe(100);
});

test('plan input rejects unsupported and malformed values', function () {
    $response = $this
        ->withSession(planPreviewSession())
        ->from(route('products.index'))
        ->post(route('products.store'), [
            'name' => '',
            'profile_group' => 'unknown-profile',
            'transaction_type' => 'unknown-type',
            'speed_mbps' => '0.5',
            'price' => '-1.999',
        ]);

    $response
        ->assertRedirect(route('products.index'))
        ->assertSessionHasErrors(['name', 'profile_group', 'transaction_type', 'speed_mbps', 'price']);

    $this->assertDatabaseCount('plans', 0);
});

test('plan creation rejects invalid speed values', function (mixed $speed, bool $omitSpeed) {
    $attributes = planAttributes(['name' => 'Invalid Speed Plan']);

    if ($omitSpeed) {
        unset($attributes['speed_mbps']);
    } else {
        $attributes['speed_mbps'] = $speed;
    }

    $this
        ->withSession(planPreviewSession())
        ->from(route('products.index'))
        ->post(route('products.store'), $attributes)
        ->assertRedirect(route('products.index'))
        ->assertSessionHasErrors(['speed_mbps']);

    $this->assertDatabaseCount('plans', 0);
})->with('invalid plan speeds');

test('plan update rejects invalid speed values', function (mixed $speed, bool $omitSpeed) {
    $plan = Plan::query()->create(planAttributes(['name' => 'Existing Plan']));
    $attributes = planAttributes(['name' => 'Existing Plan']);

    if ($omitSpeed) {
        unset($attributes['speed_mbps']);
    } else {
        $attributes['speed_mbps'] = $speed;
    }

    $editUrl = route('products.index', ['edit' => $plan->id]);

    $this
        ->withSession(planPreviewSession())
        ->from($editUrl)
        ->patch(route('products.update', $plan), $attributes)
        ->assertRedirect($editUrl)
        ->assertSessionHasErrors(['speed_mbps']);

    expect($plan->refresh()->speed_mbps)->toBe(100);
})->with('invalid plan speeds');

test('plan creation accepts the minimum speed', function () {
    $this
        ->withSession(planPreviewSession())
        ->post(route('products.store'), planAttributes(['speed_mbps' => 1]))
        ->assertRedirect(route('products.index'))
        ->assertSessionHasNoErrors();

    expect(Plan::query()->sole()->speed_mbps)->toBe(1);
});

test('plan update accepts the maximum speed', function () {
    $plan = Plan::query()->create(planAttributes());

    $this
        ->withSession(planPreviewSession())
        ->patch(route('products.update', $plan), planAttributes(['speed_mbps' => 100000]))
        ->assertRedirect(route('products.index'))
        ->assertSessionHasNoErrors();

    expect($plan->refresh()->speed_mbps)->toBe(100000);
});

test('plan names must be unique', function () {
    Plan::query()->create(planAttributes());

    $this
        ->withSession(planPreviewSession())
        ->from(route('products.index'))
        ->post(route('products.store'), planAttributes(['price' => '999.00']))
        ->assertRedirect(route('products.index'))
        ->assertSessionHasErrors(['name']);

    $this->assertDatabaseCount('plans', 1);
});

test('preview administrator can update a plan without changing its unique name', function () {
    $plan = Plan::query()->create(planAttributes());

    $response = $this
        ->withSession(planPreviewSession())
        ->patch(route('products.update', $plan), planAttributes([
            'profile_group' => 'pppoe-45mbit/649',
            'transaction_type' => 'mrd',
            'speed_mbps' => 200,
            'price' => '1299.00',
        ]));

    $response
        ->assertRedirect(route('products.index'))
        ->assertSessionHas('status', 'Plan updated.');

    $this->assertDatabaseHas('plans', [
        'id' => $plan->id,
        'name' => 'Fiber Plan 100',
        'profile_group' => 'pppoe-45mbit/649',
        'transaction_type' => 'mrd',
        'price' => 1299.00,
    ]);

    expect($plan->refresh()->speed_mbps)->toBe(200);
});

test('an assigned monthly plan cannot be changed to a non-monthly transaction type', function () {
    $plan = Plan::query()->create(planAttributes());

    $this
        ->withSession(planPreviewSession())
        ->post(route('subscribers.store'), [
            'account_number' => '00005555',
            'first_name' => 'Assigned',
            'last_name' => 'Subscriber',
            'billing_address' => '123 Sample Lane',
            'plan_id' => $plan->id,
        ])
        ->assertSessionHasNoErrors();

    $editUrl = route('products.index', ['edit' => $plan->id]);

    $this
        ->withSession(planPreviewSession())
        ->from($editUrl)
        ->patch(route('products.update', $plan), planAttributes([
            'transaction_type' => 'otc',
        ]))
        ->assertRedirect($editUrl)
        ->assertSessionHasErrors(['transaction_type']);

    expect($plan->refresh()->transaction_type)->toBe('mrc');
});

test('preview administrator can delete a plan', function () {
    $plan = Plan::query()->create(planAttributes());

    $this
        ->withSession(planPreviewSession())
        ->delete(route('products.destroy', $plan))
        ->assertRedirect(route('products.index'))
        ->assertSessionHas('status', 'Plan deleted.');

    $this->assertDatabaseMissing('plans', ['id' => $plan->id]);
});

test('a plan assigned to a subscriber cannot be deleted', function () {
    $plan = Plan::query()->create(planAttributes());

    $this->withSession(planPreviewSession())
        ->post(route('subscribers.store'), [
            'account_number' => '00005556',
            'first_name' => 'Assigned',
            'last_name' => 'Subscriber',
            'billing_address' => '123 Sample Lane',
            'plan_id' => $plan->id,
        ])
        ->assertSessionHasNoErrors();

    $this->withSession(planPreviewSession())
        ->from(route('products.index'))
        ->delete(route('products.destroy', $plan))
        ->assertRedirect(route('products.index'))
        ->assertSessionHas('error', 'This plan is assigned to subscribers and cannot be deleted.');

    $this->assertDatabaseHas('plans', ['id' => $plan->id]);
    $this->assertDatabaseHas('subscriptions', ['plan_id' => $plan->id]);
});

test('missing plans return not found for mutations', function () {
    $this
        ->withSession(planPreviewSession())
        ->patch(route('products.update', 999999), planAttributes())
        ->assertNotFound();
});

test('guests cannot mutate plans', function () {
    config()->set('preview.enabled', false);

    $this
        ->post(route('products.store'), planAttributes())
        ->assertRedirect(route('login'));

    $this->assertDatabaseCount('plans', 0);
});

test('edit selection renders the plan dialog with its persisted values', function () {
    $plan = Plan::query()->create(planAttributes());

    $this
        ->withSession(planPreviewSession())
        ->get(route('products.index', ['edit' => $plan->id]))
        ->assertOk()
        ->assertSee('id="edit-plan-dialog"', false)
        ->assertSee('value="Fiber Plan 100"', false)
        ->assertSee('id="edit-plan-speed"', false)
        ->assertSee('value="100"', false)
        ->assertSee('action="'.route('products.update', $plan).'"', false);
});

test('legacy plan without a speed remains usable and shows a blank edit field', function () {
    $plan = Plan::query()->create(planAttributes([
        'name' => 'Legacy Plan',
        'speed_mbps' => null,
    ]));

    $response = $this
        ->withSession(planPreviewSession())
        ->get(route('products.index', ['edit' => $plan->id]));

    $response
        ->assertOk()
        ->assertSee('Speed not set');

    expect($response->getContent())
        ->toMatch('/<input(?=[^>]*id="edit-plan-speed")(?=[^>]*value="")[^>]*>/');
});

test('catalogue displays an explicit empty search result', function () {
    Plan::query()->create(planAttributes());

    $this
        ->withSession(planPreviewSession())
        ->get(route('products.index', ['q' => 'no-such-plan']))
        ->assertOk()
        ->assertSee('No plans match your search.');
});

test('failed edit restores values and errors only in the selected edit dialog', function () {
    $plan = Plan::query()->create(planAttributes(['name' => 'Plan to edit']));
    Plan::query()->create(planAttributes(['name' => 'Name already used']));
    $editUrl = route('products.index', ['edit' => $plan->id]);

    $page = $this
        ->withSession(planPreviewSession())
        ->followingRedirects()
        ->from($editUrl)
        ->patch(route('products.update', $plan), planAttributes([
            '_plan_form' => "edit:{$plan->id}",
            'name' => 'Name already used',
        ]));

    $html = $page->getContent();

    expect($html)
        ->toMatch('/<input(?=[^>]*id="create-plan-name")(?=[^>]*value="")[^>]*>/')
        ->toMatch('/<input(?=[^>]*id="edit-plan-name")(?=[^>]*value="Name already used")[^>]*>/');

    $page
        ->assertOk()
        ->assertSee('The name has already been taken.')
        ->assertSee('x-init="$nextTick(() => $refs.editPlanDialog.showModal())"', false);
});

test('malformed array values return to the create dialog without a rendering error', function () {
    $this
        ->withSession(planPreviewSession())
        ->followingRedirects()
        ->from(route('products.index'))
        ->post(route('products.store'), [
            '_plan_form' => 'create',
            'name' => ['not-a-scalar'],
            'profile_group' => 'default-encryption',
            'transaction_type' => 'mrc',
            'price' => ['not-a-scalar'],
        ])
        ->assertOk()
        ->assertSee('The name field must be a string.')
        ->assertSee('The price field must be a number.');
});

test('concurrent duplicate plan creation returns a name validation error', function () {
    $inserted = false;

    Plan::creating(function (Plan $plan) use (&$inserted): void {
        if ($inserted || $plan->name !== 'Concurrent Plan') {
            return;
        }

        $inserted = true;
        DB::table('plans')->insert([
            ...planAttributes(['name' => 'Concurrent Plan']),
            'usage_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    });

    $this
        ->withSession(planPreviewSession())
        ->from(route('products.index'))
        ->post(route('products.store'), planAttributes([
            '_plan_form' => 'create',
            'name' => 'Concurrent Plan',
        ]))
        ->assertRedirect(route('products.index'))
        ->assertSessionHasErrors(['name']);

    $this->assertDatabaseCount('plans', 1);
});

test('concurrent duplicate plan rename returns a name validation error', function () {
    $plan = Plan::query()->create(planAttributes(['name' => 'Original Plan']));
    $inserted = false;

    Plan::updating(function (Plan $updatingPlan) use (&$inserted): void {
        if ($inserted || $updatingPlan->name !== 'Concurrent Rename') {
            return;
        }

        $inserted = true;
        DB::table('plans')->insert([
            ...planAttributes(['name' => 'Concurrent Rename']),
            'usage_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    });

    $editUrl = route('products.index', ['edit' => $plan->id]);

    $this
        ->withSession(planPreviewSession())
        ->from($editUrl)
        ->patch(route('products.update', $plan), planAttributes([
            '_plan_form' => "edit:{$plan->id}",
            'name' => 'Concurrent Rename',
        ]))
        ->assertRedirect($editUrl)
        ->assertSessionHasErrors(['name']);

    expect($plan->refresh()->name)->toBe('Original Plan');
    $this->assertDatabaseCount('plans', 2);
});
