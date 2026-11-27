<?php

use App\Models\NetworkAccount;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('preview.enabled', true);

    $this->withSession([
        'preview.user' => [
            'username' => 'admin',
            'name' => 'Admin',
        ],
    ]);
});

function monthlyPlan(array $overrides = []): Plan
{
    return Plan::query()->create(array_merge([
        'name' => 'Fiber 100 Mbps',
        'profile_group' => 'default-encryption',
        'transaction_type' => 'mrc',
        'speed_mbps' => 100,
        'price' => 1299,
        'usage_count' => 0,
    ], $overrides));
}

function subscriberPayload(array $overrides = []): array
{
    return array_merge([
        'account_number' => '00001234',
        'first_name' => 'Alex',
        'last_name' => 'Santos',
        'landmark' => 'Near the community hall',
        'billing_address' => '123 Sample Lane, Riverside',
        'contact_number' => '09171234567',
        'email' => 'alex@example.com',
        'coordinates' => '14.5995, 120.9842',
        'remarks' => 'Priority installation',
        'area' => 'Riverside',
        'installation_date' => '2026-11-20',
        'billing_day' => 15,
        'subscription_date' => '2026-11-15',
        'credit_limit' => '1500.00',
        'external_url' => 'https://example.com/subscribers/00001234',
        'image_url' => 'https://example.com/subscribers/00001234.jpg',
        'pppoe_username' => '00001234@rodjen',
        'pppoe_password' => 'secret-passphrase',
        'pppoe_profile' => 'fiber-100m',
        'pppoe_comment' => 'Primary PPPoE account',
        'olt_device' => 'OLT-01',
        'pon_port' => 'PON-1/1',
        'nap_device' => 'NAP-08',
        'nap_port' => '8',
        'onu_name' => 'ONU-00001234',
        'caller_id' => 'CALLER-00001234',
        'mac_address' => 'AA:BB:CC:DD:EE:FF',
        'network_remarks' => 'Drop cable inspected',
    ], $overrides);
}

test('a preview administrator can store a subscriber and related service data', function () {
    $plan = monthlyPlan();

    $response = $this->post('/subscriptions/records', subscriberPayload([
        'plan_id' => $plan->id,
    ]));

    $response
        ->assertRedirect('/subscriptions/records/1')
        ->assertSessionHas('status', 'Subscriber added.');

    $this->assertDatabaseHas('subscribers', [
        'account_number' => '00001234',
        'first_name' => 'Alex',
        'last_name' => 'Santos',
    ]);
    $this->assertDatabaseHas('subscriptions', [
        'subscriber_id' => 1,
        'plan_id' => $plan->id,
        'billing_day' => 15,
        'status' => 'active',
    ]);
    $this->assertDatabaseHas('network_accounts', [
        'subscription_id' => 1,
        'username' => '00001234@rodjen',
        'profile' => 'fiber-100m',
    ]);

    $storedPassword = DB::table('network_accounts')->value('password');

    expect($storedPassword)
        ->not->toBe('secret-passphrase')
        ->and(Crypt::decryptString($storedPassword))->toBe('secret-passphrase')
        ->and($plan->fresh()->usage_count)->toBe(1);
});

test('subscriber creation accepts the required fields without optional service data', function () {
    $response = $this->post('/subscriptions/records', [
        'account_number' => '00000001',
        'first_name' => 'Maria',
        'last_name' => 'Reyes',
        'billing_address' => 'Purok 1',
    ]);

    $response->assertRedirect('/subscriptions/records/1');

    $this->assertDatabaseHas('subscribers', ['account_number' => '00000001']);
    $this->assertDatabaseHas('subscriptions', ['subscriber_id' => 1, 'plan_id' => null]);
    $this->assertDatabaseHas('network_accounts', ['subscription_id' => 1, 'username' => null]);
});

test('subscriber account numbers require exactly eight digits', function () {
    foreach (['1234567', '123456789', '1234ABCD', '１２３４５６７８'] as $accountNumber) {
        $this->from('/subscriptions/add')
            ->post('/subscriptions/records', subscriberPayload([
                'account_number' => $accountNumber,
            ]))
            ->assertRedirect('/subscriptions/add')
            ->assertSessionHasErrors(['account_number']);
    }

    $this->assertDatabaseCount('subscribers', 0);
});

test('subscriber account number generator returns an unused eight digit number', function () {
    $this->post('/subscriptions/records', subscriberPayload([
        'account_number' => '17465997',
        'pppoe_username' => 'existing@rodjen',
    ]))->assertSessionHasNoErrors();

    $response = $this->getJson(route('subscribers.account-number'))
        ->assertOk()
        ->assertJsonStructure(['account_number']);

    $accountNumber = $response->json('account_number');

    expect($accountNumber)->toMatch('/\A[0-9]{8}\z/')
        ->not->toBe('17465997');

    $this->assertDatabaseMissing('subscribers', ['account_number' => $accountNumber]);
});

test('subscriber identifiers must be unique without creating partial records', function () {
    $plan = monthlyPlan();

    $this->post('/subscriptions/records', subscriberPayload(['plan_id' => $plan->id]))
        ->assertSessionHasNoErrors();

    $this->post('/subscriptions/records', subscriberPayload([
        'first_name' => 'Different',
        'last_name' => 'Subscriber',
    ]))->assertSessionHasErrors(['account_number', 'pppoe_username']);

    $this->assertDatabaseCount('subscribers', 1);
    $this->assertDatabaseCount('subscriptions', 1);
    $this->assertDatabaseCount('network_accounts', 1);
});

test('subscriber creation rejects invalid service and contact values', function () {
    $nonMonthlyPlan = monthlyPlan([
        'name' => 'Installation Fee',
        'transaction_type' => 'otc',
    ]);

    $response = $this->from('/subscriptions/add')->post('/subscriptions/records', subscriberPayload([
        'plan_id' => $nonMonthlyPlan->id,
        'email' => 'invalid-email',
        'installation_date' => 'not-a-date',
        'billing_day' => 32,
        'credit_limit' => -1,
        'external_url' => 'ftp://example.com/file',
        'image_url' => 'javascript:alert(1)',
        'mac_address' => 'not-a-mac-address',
    ]));

    $response
        ->assertRedirect('/subscriptions/add')
        ->assertSessionHasErrors([
            'plan_id',
            'email',
            'installation_date',
            'billing_day',
            'credit_limit',
            'external_url',
            'image_url',
            'mac_address',
        ]);

    expect(session()->getOldInput('pppoe_password'))->toBeNull();
    $this->assertDatabaseCount('subscribers', 0);
});

test('subscriber creation rejects credit limits with more than two decimal places', function () {
    $this->from('/subscriptions/add')
        ->post('/subscriptions/records', subscriberPayload([
            'credit_limit' => '100.999',
        ]))
        ->assertRedirect('/subscriptions/add')
        ->assertSessionHasErrors(['credit_limit']);

    $this->assertDatabaseCount('subscribers', 0);
});

test('malformed array values return to the subscriber form without a rendering error', function () {
    $plan = monthlyPlan();

    $response = $this
        ->followingRedirects()
        ->from(route('subscribers.create'))
        ->post(route('subscribers.store'), [
            'account_number' => '00009999',
            'first_name' => ['Alex'],
            'last_name' => 'Santos',
            'billing_address' => '123 Sample Lane',
            'billing_day' => [15],
            'plan_id' => [$plan->id],
        ]);

    $response
        ->assertOk()
        ->assertSee('The first name field must be a string.')
        ->assertSee('The billing day field must be an integer.')
        ->assertSee('The plan field must be an integer.')
        ->assertSee('value="00009999"', false);

    $this->assertDatabaseCount('subscribers', 0);
    $this->assertDatabaseCount('subscriptions', 0);
    $this->assertDatabaseCount('network_accounts', 0);
});

test('subscriber creation rolls back every row when a related insert fails', function () {
    $plan = monthlyPlan();

    NetworkAccount::creating(function (): never {
        throw new RuntimeException('Forced network account failure.');
    });

    try {
        $this->post('/subscriptions/records', subscriberPayload(['plan_id' => $plan->id]));
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toBe('Forced network account failure.');
    } finally {
        NetworkAccount::flushEventListeners();
    }

    $this->assertDatabaseCount('subscribers', 0);
    $this->assertDatabaseCount('subscriptions', 0);
    $this->assertDatabaseCount('network_accounts', 0);
    expect($plan->fresh()->usage_count)->toBe(0);
});

test('unexpected subscriber errors do not expose the pppoe password in debug responses', function () {
    config()->set('app.debug', true);
    $secret = implode('', ['debug-only-', 'pppoe-secret']);

    NetworkAccount::creating(function (): never {
        throw new RuntimeException('Forced network account failure.');
    });

    try {
        $response = $this->post('/subscriptions/records', subscriberPayload([
            'pppoe_password' => $secret,
        ]));
    } finally {
        NetworkAccount::flushEventListeners();
    }

    $response
        ->assertInternalServerError()
        ->assertDontSee($secret);
});

test('guests and unverified users cannot store subscribers', function () {
    $this->flushSession();

    $this->post('/subscriptions/records', subscriberPayload())
        ->assertRedirect(route('login'));

    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->post('/subscriptions/records', subscriberPayload())
        ->assertRedirect(route('verification.notice'));

    $this->assertDatabaseCount('subscribers', 0);
});

test('subscriber records list persisted accounts and apply area status and search filters', function () {
    $plan = monthlyPlan();

    $this->post('/subscriptions/records', subscriberPayload([
        'plan_id' => $plan->id,
        'account_number' => '00000011',
        'first_name' => 'North',
        'last_name' => 'Subscriber',
        'area' => 'North Zone',
        'pppoe_username' => 'north@rodjen',
    ]))->assertSessionHasNoErrors();

    $this->post('/subscriptions/records', subscriberPayload([
        'plan_id' => $plan->id,
        'account_number' => '00000022',
        'first_name' => 'South',
        'last_name' => 'Subscriber',
        'area' => 'South Zone',
        'pppoe_username' => 'south@rodjen',
    ]))->assertSessionHasNoErrors();

    $this->assertDatabaseHas('subscribers', ['area' => '0', 'remarks' => '0']);
    $this->assertDatabaseHas('subscriptions', ['plan_id' => $plan->id]);

    $this->get('/subscriptions/records')
        ->assertOk()
        ->assertSee('00000011')
        ->assertSee('North Subscriber')
        ->assertSee('00000022')
        ->assertSee('South Subscriber')
        ->assertSee('href="'.route('subscribers.details', 1).'"', false)
        ->assertSee('href="'.route('subscribers.details', 2).'"', false);

    $this->get('/subscriptions/records?area=North%20Zone&status=active&q=North')
        ->assertOk()
        ->assertSee('00000011')
        ->assertDontSee('00000022');
});

test('subscriber records show an honest empty state', function () {
    $this->get('/subscriptions/records')
        ->assertOk()
        ->assertSee('No subscriber records found.')
        ->assertDontSee('DEMO-0001');
});

test('subscriber records ignore malformed array filters without a server error', function () {
    $this->post('/subscriptions/records', subscriberPayload([
        'account_number' => '00000033',
        'pppoe_username' => 'arraysafe@rodjen',
    ]))->assertSessionHasNoErrors();

    $this->get(route('subscribers.index', [
        'q' => ['not-a-string'],
        'area' => ['not-a-string'],
        'status' => ['not-a-string'],
    ]))
        ->assertOk()
        ->assertSee('00000033');
});

test('subscriber records preserve valid zero-like text values', function () {
    $plan = monthlyPlan(['name' => '0']);

    $this->post('/subscriptions/records', subscriberPayload([
        'plan_id' => $plan->id,
        'account_number' => '00000055',
        'area' => '0',
        'remarks' => '0',
        'pppoe_username' => 'zero-values@rodjen',
    ]))->assertSessionHasNoErrors();

    $this->get('/subscriptions/records')
        ->assertOk()
        ->assertDontSee('Area not specified')
        ->assertDontSee('Not assigned')
        ->assertSee('>0</span>', false)
        ->assertSee('>0</td>', false);
});

test('subscriber details show only the selected persisted account without exposing its password', function () {
    $plan = monthlyPlan();

    $this->post('/subscriptions/records', subscriberPayload([
        'plan_id' => $plan->id,
        'account_number' => '00000011',
        'first_name' => 'Selected',
        'last_name' => 'Subscriber',
        'pppoe_username' => 'selected@rodjen',
        'pppoe_password' => 'selected-secret',
    ]))->assertSessionHasNoErrors();

    $this->post('/subscriptions/records', subscriberPayload([
        'plan_id' => $plan->id,
        'account_number' => '00000022',
        'first_name' => 'Other',
        'last_name' => 'Subscriber',
        'pppoe_username' => 'other@rodjen',
        'pppoe_password' => 'other-secret',
    ]))->assertSessionHasNoErrors();

    $this->get('/subscriptions/records/1')
        ->assertOk()
        ->assertSee('00000011')
        ->assertSee('Selected Subscriber')
        ->assertSee('selected@rodjen')
        ->assertSee('Fiber 100 Mbps')
        ->assertSeeInOrder(['Bill generation day', 'Day 15 of each month', 'Due date'])
        ->assertSee('Subscription date')
        ->assertSee('Nov 15, 2026')
        ->assertSee('Credit limit')
        ->assertSee('PHP 1,500.00')
        ->assertSee('External URL')
        ->assertSee('https://example.com/subscribers/00001234')
        ->assertSee('Image URL')
        ->assertSee('https://example.com/subscribers/00001234.jpg')
        ->assertSee('AA:BB:CC:DD:EE:FF')
        ->assertSee('Drop cable inspected')
        ->assertSee('Configured')
        ->assertDontSee('00000022')
        ->assertDontSee('selected-secret')
        ->assertDontSee('other-secret');
});

test('a pppoe password containing zero is still reported as configured', function () {
    $this->post('/subscriptions/records', subscriberPayload([
        'account_number' => '00000044',
        'pppoe_username' => 'zero-password@rodjen',
        'pppoe_password' => '0',
    ]))->assertSessionHasNoErrors();

    $this->get('/subscriptions/records/1')
        ->assertOk()
        ->assertSee('Configured')
        ->assertDontSee('>0</dd>', false);
});

test('subscriber details check panel access before returning a missing record', function () {
    $this->flushSession();

    $this->get('/subscriptions/records/999999')
        ->assertRedirect(route('login'));

    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/subscriptions/records/999999')
        ->assertRedirect(route('verification.notice'));

    auth()->logout();
    $this->withSession([
        'preview.user' => ['username' => 'admin', 'name' => 'Admin'],
    ])->get('/subscriptions/records/999999')->assertNotFound();

    $this->get('/subscriptions/records/'.str_repeat('9', 100))->assertNotFound();
});
