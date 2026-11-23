<?php

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

test('add subscriber renders the complete frontend-only field foundation', function () {
    $response = $this->get(route('subscribers.create'));

    $response
        ->assertOk()
        ->assertSee('General profile')
        ->assertSee('Network profile')
        ->assertSee('Account number')
        ->assertSee('First name')
        ->assertSee('Last name')
        ->assertSee('Billing address')
        ->assertSee('Area')
        ->assertSee('Contact number')
        ->assertSee('Email address')
        ->assertSee('Coordinates')
        ->assertSee('Subscription date')
        ->assertSee('Credit limit')
        ->assertSee('Remarks')
        ->assertSee('External URL')
        ->assertSee('Image URL')
        ->assertSee('OLT device')
        ->assertSee('PON port')
        ->assertSee('NAP device')
        ->assertSee('NAP port')
        ->assertSee('ONU name')
        ->assertSee('Caller ID or PPPoE identifier')
        ->assertSee('MAC address')
        ->assertSee('data-ui-only-form', false)
        ->assertSee('data-test="subscriber-create-disabled"', false)
        ->assertSee('type="button"', false)
        ->assertSee('disabled', false);
});

test('subscriber records renders filters and an empty table shell', function () {
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
        ->assertSee('No subscriber records yet');
});

test('products and services renders an empty table shell', function () {
    $response = $this->get(route('products.index'));

    $response
        ->assertOk()
        ->assertSee('Search')
        ->assertSeeInOrder([
            'Product',
            'Profile group',
            'Transaction type',
            'Price',
            'Usage or account count',
        ])
        ->assertSee('No products or services yet');
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
