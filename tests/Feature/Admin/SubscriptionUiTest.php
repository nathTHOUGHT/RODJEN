<?php

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

test('add subscriber groups its fields in the frontend-only layout', function () {
    $response = $this->get(route('subscribers.create'));

    $response
        ->assertOk()
        ->assertSeeInOrder(['Personal Details', 'Subscription Details', 'MikroTik Details'])
        ->assertSee('Additional details')
        ->assertSee('Account number')
        ->assertSee('First name')
        ->assertSee('Last name')
        ->assertSee('Landmark')
        ->assertSee('Billing address')
        ->assertSee('Area')
        ->assertSee('Contact number')
        ->assertSee('Email address')
        ->assertSee('Coordinates')
        ->assertSee('Installation date')
        ->assertSee('Plan')
        ->assertSee('Bill generation')
        ->assertSee('Subscription date')
        ->assertSee('Credit limit')
        ->assertSee('Remarks')
        ->assertSee('External URL')
        ->assertSee('Image URL')
        ->assertSee('PPPoE name')
        ->assertSee('PPPoE password')
        ->assertSee('PPPoE profile')
        ->assertSee('PPPoE comment')
        ->assertSee('OLT device')
        ->assertSee('PON port')
        ->assertSee('NAP device')
        ->assertSee('NAP port')
        ->assertSee('ONU name')
        ->assertSee('Caller ID or PPPoE identifier')
        ->assertSee('MAC address')
        ->assertSee('Cancel')
        ->assertSee('Submit')
        ->assertSee('data-ui-only-form', false)
        ->assertSee('data-test="subscriber-create-disabled"', false)
        ->assertSee('type="button"', false)
        ->assertSee('disabled', false);
});

test('subscriber records links a demo account to its populated details', function () {
    $response = $this->get(route('subscribers.index'));

    $response
        ->assertOk()
        ->assertSee('Area')
        ->assertSee('Status')
        ->assertSee('Due from')
        ->assertSee('Due to')
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
        ->assertSee('Sample subscriber')
        ->assertSee('DEMO-0001')
        ->assertSee('href="'.route('subscribers.details').'"', false);

    $this->get(route('subscribers.details'))
        ->assertOk()
        ->assertSee('Sample subscriber')
        ->assertSee('DEMO-0001')
        ->assertSee('Alex Sample')
        ->assertSee('Fiber 100 Mbps')
        ->assertSee('demo-alex');
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
