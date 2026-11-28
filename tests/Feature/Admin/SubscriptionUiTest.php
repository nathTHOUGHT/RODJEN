<?php

use App\Models\Plan;
use App\Models\Subscriber;
use Database\Seeders\PlanSeeder;

beforeEach(function () {
    config()->set('preview.enabled', true);

    $this->withSession([
        'preview.user' => [
            'username' => 'admin',
            'name' => 'Admin',
        ],
    ]);
});

test('dashboard renders a neutral month overview', function () {
    $response = $this->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('Month overview')
        ->assertSeeInOrder(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'])
        ->assertSee('No activity scheduled');
});

test('add subscriber renders a database-backed form with monthly plans', function () {
    $this->seed(PlanSeeder::class);

    $response = $this->get(route('subscribers.create'));

    $response
        ->assertOk()
        ->assertSeeInOrder(['Personal Details', 'Subscription Configuration', 'MikroTik Configuration'])
        ->assertDontSee('Additional details')
        ->assertSee('Account number')
        ->assertSee('First name')
        ->assertSee('Last name')
        ->assertSee('Landmark')
        ->assertSee('Billing address')
        ->assertSee('Contact number')
        ->assertSee('Email address')
        ->assertSee('Coordinates')
        ->assertSee('name="external_url"', false)
        ->assertSee('aria-label="External URL"', false)
        ->assertSee('autocomplete="off"', false)
        ->assertSee('Installation date')
        ->assertSee('Plan')
        ->assertSee('aria-label="Bill Generation"', false)
        ->assertSee('Standard (7 days)')
        ->assertSee('Early (14 days)')
        ->assertSee('Remarks')
        ->assertSee('External URL')
        ->assertSee('PPPoE name')
        ->assertSee('PPPoE password')
        ->assertSee('PPPoE profile')
        ->assertSee('PPPoE comment')
        ->assertDontSee('name="area"', false)
        ->assertDontSee('name="subscription_date"', false)
        ->assertDontSee('name="credit_limit"', false)
        ->assertDontSee('name="olt_device"', false)
        ->assertDontSee('name="pon_port"', false)
        ->assertDontSee('name="nap_device"', false)
        ->assertDontSee('name="nap_port"', false)
        ->assertDontSee('name="onu_name"', false)
        ->assertDontSee('name="caller_id"', false)
        ->assertDontSee('name="mac_address"', false)
        ->assertSee('Cancel')
        ->assertSee('Submit')
        ->assertSee('action="'.route('subscribers.store').'"', false)
        ->assertSee('method="POST"', false)
        ->assertSee('name="account_number"', false)
        ->assertSee('name="plan_id"', false)
        ->assertSee('name="pppoe_username"', false)
        ->assertSee('name="pppoe_password"', false)
        ->assertSee('ADVOCACY PLAN 649/45MBPS')
        ->assertSee('type="submit"', false)
        ->assertDontSee('data-ui-only-form', false)
        ->assertDontSee('data-test="subscriber-create-disabled"', false);

    expect($response->getContent())->toMatch('/<option value="7" selected(?:="selected")?>Standard \(7 days\)<\/option>/');
});

test('add subscriber exposes an external URL field in personal details', function () {
    config()->set('preview.enabled', true);
    $this->withSession([
        'preview.user' => [
            'username' => 'admin',
            'name' => 'Admin',
        ],
    ]);

    $this->get(route('subscribers.create'))
        ->assertOk()
        ->assertSee('Personal Details')
        ->assertSee('name="external_url"', false)
        ->assertSee('aria-label="External URL"', false);
});

test('add subscriber password input is visible', function () {
    config()->set('preview.enabled', true);
    $this->withSession([
        'preview.user' => [
            'username' => 'admin',
            'name' => 'Admin',
        ],
    ]);

    $response = $this->get(route('subscribers.create'));

    $response->assertOk();
    expect($response->getContent())->toMatch('/<input(?=[^>]*name="pppoe_password")(?=[^>]*type="text")/i');
});

test('account number form input is limited to eight digits and has a generator', function () {
    $this->get(route('subscribers.create'))
        ->assertOk()
        ->assertSee('name="account_number"', false)
        ->assertSee('maxlength="8"', false)
        ->assertSee('pattern="[0-9]{8}"', false)
        ->assertSee('data-account-number-generate', false)
        ->assertSee('aria-label="Generate account number"', false)
        ->assertSee('aria-label="Account number"', false)
        ->assertSee('aria-label="First name"', false)
        ->assertSee('aria-label="Plan"', false)
        ->assertSee('data-open-date-picker', false)
        ->assertSee('<path d="M12 5v14M5 12h14" />', false)
        ->assertSee('placeholder="Account No."', false)
        ->assertSee('placeholder="Mobile No."', false)
        ->assertSee('placeholder="Enter first name"', false)
        ->assertSee('placeholder="Enter email address"', false)
        ->assertSeeInOrder(['name="installation_date"', 'name="bill_generation_days"', 'name="plan_id"'], false)
        ->assertDontSee('<label for="bill-generation"', false)
        ->assertDontSee('<label for="account-number"', false)
        ->assertDontSee('<label for="plan"', false);
});

test('add subscriber displays validation errors and never flashes the pppoe password', function () {
    $response = $this
        ->followingRedirects()
        ->from(route('subscribers.create'))
        ->post(route('subscribers.store'), [
            'account_number' => '00009999',
            'first_name' => '',
            'last_name' => '',
            'billing_address' => '',
            'pppoe_password' => 'never-render-this-secret',
        ]);

    $response
        ->assertOk()
        ->assertSee('The first name field is required.')
        ->assertSee('value="00009999"', false)
        ->assertSee('id="pppoe-password"', false)
        ->assertDontSee('never-render-this-secret');

    expect(session()->getOldInput('pppoe_password'))->toBeNull();
});

test('subscriber records link a saved account to its populated details', function () {
    $this->seed(PlanSeeder::class);
    $plan = Plan::query()->where('transaction_type', 'mrc')->firstOrFail();

    $this->post(route('subscribers.store'), [
        'account_number' => '00001234',
        'first_name' => 'Alex',
        'last_name' => 'Santos',
        'billing_address' => '123 Sample Lane',
        'plan_id' => $plan->id,
        'billing_day' => 15,
        'pppoe_username' => 'alex@rodjen',
    ])->assertSessionHasNoErrors();

    $subscriber = Subscriber::query()->firstOrFail();
    $response = $this->get(route('subscribers.index'));

    $response
        ->assertOk()
        ->assertSee('Area')
        ->assertSee('Status')
        ->assertSee('Search')
        ->assertSeeInOrder([
            'Account',
            'Recurring dates',
            'Details',
            'Subscription',
            'Service',
            'Status',
            'Balance',
            'Remarks',
        ])
        ->assertSee('Alex Santos')
        ->assertSee('00001234')
        ->assertSee('href="'.route('subscribers.details', $subscriber).'"', false);

    $this->get(route('subscribers.details', $subscriber))
        ->assertOk()
        ->assertSee('00001234')
        ->assertSee('Alex Santos')
        ->assertSee($plan->name)
        ->assertSee('alex@rodjen');
});

test('products and services renders plan cards and an accessible add dialog', function () {
    $this->seed(PlanSeeder::class);

    $response = $this->get(route('products.index'));

    $response
        ->assertOk()
        ->assertSee('Search')
        ->assertSee('ADVOCACY PLAN 649/45MBPS')
        ->assertSee('pppoe-45mbit/649')
        ->assertSee('Monthly Recurring Charge (MRC)')
        ->assertSee('45 Mbps')
        ->assertSee('₱649.00')
        ->assertSee('106 users')
        ->assertSee('data-test="plan-card"', false)
        ->assertSee('id="create-plan-dialog"', false)
        ->assertSee('aria-labelledby="create-plan-title"', false)
        ->assertSee('aria-haspopup="dialog"', false)
        ->assertSee('id="create-plan-speed"', false)
        ->assertSeeInOrder(['Speed (Mbps)', 'Price'])
        ->assertSeeInOrder(['45 Mbps', '649.00'])
        ->assertSee('href="'.route('products.index', ['edit' => 1]).'"', false)
        ->assertSee('action="'.route('products.destroy', 1).'"', false)
        ->assertSeeInOrder([
            'Monthly Recurring Charge (MRC)',
            'One Time Charge (OTC)',
            'Policy & Condition Fee (PCF)',
            'Monthly Recurring Discount (MRD)',
            'Monthly Due Surcharge (MDS)',
        ])
        ->assertDontSee('<table', false)
        ->assertDontSee('No products or services yet');
});

test('policies and conditions renders an empty table shell', function () {
    $response = $this->get(route('policies.index'));

    $response
        ->assertOk()
        ->assertSee('Search')
        ->assertSeeInOrder([
            'Policy',
            'PPPoE action',
            'PPPoE conditions',
            'Hotspot action',
            'Hotspot conditions',
            'IPoE action',
            'IPoE conditions',
        ])
        ->assertSee('No policies or conditions yet');
});
