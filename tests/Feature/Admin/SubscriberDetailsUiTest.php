<?php

use Illuminate\Testing\TestResponse;

function subscriberDetailsResponse(object $testCase): TestResponse
{
    config()->set('preview.enabled', true);

    return $testCase
        ->withSession([
            'preview.user' => [
                'username' => 'admin',
                'name' => 'Admin',
            ],
        ])
        ->get(route('subscribers.details'));
}

function expectDisabledSubscriberAction(string $html, string $testMarker): void
{
    $pattern = '/<button(?=[^>]*data-test="'.preg_quote($testMarker, '/').'"\)'
        .'(?=[^>]*type="button")(?=[^>]*disabled)[^>]*>/i';

    expect($html)->toMatch($pattern);
}

test('subscriber details exposes the complete text-only information foundation', function () {
    $response = subscriberDetailsResponse($this);

    $response
        ->assertOk()
        ->assertSeeInOrder([
            'Account details',
            'Service details',
            'Coverage map',
            'Customer location',
            'Network path',
            'PON details',
            'MikroTik parameters',
            'Transaction summary',
            'Transactions',
            'System logs',
            'Activity logs',
        ])
        ->assertSee('Archive')
        ->assertSee('Message')
        ->assertSee('Support ticket')
        ->assertSee('Job order')
        ->assertSee('Transaction')
        ->assertSee('Billing status')
        ->assertSee('Service state')
        ->assertSee('Override date')
        ->assertDontSee('<img', false)
        ->assertDontSee('<svg', false)
        ->assertDontSee('<iframe', false)
        ->assertDontSee('<canvas', false);
});

test('subscriber detail modals expose blank future fields and disabled mutations', function () {
    $response = subscriberDetailsResponse($this);
    $html = $response->getContent();

    $response
        ->assertOk()
        ->assertSee('Remove account')
        ->assertSee('Account settings')
        ->assertSee('Deactivate billing')
        ->assertSee('Override date')
        ->assertSee('Transaction details')
        ->assertSee('Service type')
        ->assertSee('Username')
        ->assertSee('Password')
        ->assertSee('Subscription')
        ->assertSee('Local address')
        ->assertSee('Remote address')
        ->assertSee('Caller ID')
        ->assertSee('Credit limit')
        ->assertSee('Transaction date')
        ->assertSee('Current balance')
        ->assertSee('Transaction type')
        ->assertSee('Credit date')
        ->assertSee('Description')
        ->assertSee('Amount')
        ->assertSee('Payment mode')
        ->assertSee('Optional reference');

    expectDisabledSubscriberAction($html, 'subscriber-remove-disabled');
    expectDisabledSubscriberAction($html, 'subscriber-settings-disabled');
    expectDisabledSubscriberAction($html, 'subscriber-billing-disabled');
    expectDisabledSubscriberAction($html, 'subscriber-override-disabled');
    expectDisabledSubscriberAction($html, 'subscriber-transaction-disabled');

    expect($html)
        ->toMatch('/<input(?=[^>]*name="service_username")(?=[^>]*value="")[^>]*>/i')
        ->toMatch('/<input(?=[^>]*name="service_password")(?=[^>]*type="password")(?=[^>]*value="")[^>]*>/i');
});

test('subscriber detail data regions remain explicitly empty', function () {
    $response = subscriberDetailsResponse($this);

    $response
        ->assertOk()
        ->assertSee('Not available')
        ->assertSee('No transactions yet')
        ->assertSee('No system logs yet')
        ->assertSee('No activity logs yet');

    $html = $response->getContent();

    expect($html)
        ->not->toMatch('/<button(?![^>]*disabled)[^>]*>\s*(Confirm|Update|Submit)\s*<\/button>/i');
});
