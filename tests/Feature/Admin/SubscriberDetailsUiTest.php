<?php

use App\Models\Plan;
use App\Models\Subscriber;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Illuminate\Testing\TestResponse;

function subscriberDetailsResponse(object $testCase, array $attributes = []): TestResponse
{
    config()->set('preview.enabled', true);

    $testCase
        ->withSession([
            'preview.user' => [
                'username' => 'admin',
                'name' => 'Admin',
            ],
        ])
        ->post(route('subscribers.store'), array_merge([
            'account_number' => '00001234',
            'first_name' => 'Alex',
            'last_name' => 'Santos',
            'billing_address' => '123 Sample Lane',
            'pppoe_username' => 'alex@rodjen',
            'pppoe_password' => 'never-render-this-secret',
            'pppoe_comment' => 'Initial MikroTik comment',
            'caller_id' => 'CALLER-00001234',
        ], $attributes))
        ->assertSessionHasNoErrors();

    $subscriber = Subscriber::query()->firstOrFail();

    return $testCase->get(route('subscribers.details', $subscriber));
}

test('subscriber details renders the personal external URL as a safe external link', function () {
    $url = 'https://example.com/subscribers/00001234';
    $response = subscriberDetailsResponse($this, ['external_url' => $url]);

    $response
        ->assertOk()
        ->assertSee('External URL')
        ->assertSee('href="'.$url.'"', false)
        ->assertSee('target="_blank"', false)
        ->assertSee('rel="noopener noreferrer"', false);
});

function expectDisabledSubscriberAction(string $html, string $testMarker): void
{
    $pattern = '/<button(?=[^>]*data-test="'.preg_quote($testMarker, '/').'")'
        .'(?=[^>]*type="button")(?=[^>]*disabled)[^>]*>/i';

    expect($html)->toMatch($pattern);
}

test('subscriber details presents the toolbar and information panels in layout order', function () {
    $response = subscriberDetailsResponse($this);

    $response
        ->assertOk()
        ->assertSeeInOrder([
            'Personal Details',
            'Service Details',
            'MikroTik Details',
            'MikroTik parameters',
            'Transaction list',
        ])
        ->assertSee('Timestamp (Manila time)')
        ->assertSee('Commenter')
        ->assertDontSee('Comment list')
        ->assertSee('Additional details')
        ->assertSee('External URL')
        ->assertSee('Image URL')
        ->assertSee('OLT device')
        ->assertSee('PON port')
        ->assertSee('NAP device')
        ->assertSee('NAP port')
        ->assertSee('ONU name')
        ->assertSee('MAC address')
        ->assertSee('Network remarks')
        ->assertSee('Transaction summary')
        ->assertSee('System logs')
        ->assertSee('Activity logs')
        ->assertSee('Message')
        ->assertSee('Support ticket')
        ->assertSee('Job order')
        ->assertSee('Transaction')
        ->assertSee('Billing status')
        ->assertSee('Service state')
        ->assertSee('Override date')
        ->assertSee('aria-label="Archive"', false)
        ->assertSee('aria-label="Thermal"', false)
        ->assertSee('aria-label="A4 SOA"', false)
        ->assertSee('aria-label="Mark as pulled out"', false)
        ->assertSee('flex flex-nowrap gap-2 overflow-x-auto', false)
        ->assertSee('xl:flex-row xl:items-center', false)
        ->assertSee('xl:ml-auto xl:w-fit xl:flex-none xl:grid-cols-5', false)
        ->assertSee('xl:w-24', false)
        ->assertSee('Post-pullout')
        ->assertSee('title="Reactivate from pullout count"', false)
        ->assertDontSee('Return to records')
        ->assertDontSee('<img', false)
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

    foreach (['transaction', 'comment', 'promise'] as $dialog) {
        expect($html)->toMatch('/<dialog\b[^>]*\bid="subscriber-'.$dialog.'-dialog"[^>]*>/');
    }

    expectDisabledSubscriberAction($html, 'subscriber-remove-disabled');
    expectDisabledSubscriberAction($html, 'subscriber-settings-disabled');
    expectDisabledSubscriberAction($html, 'subscriber-billing-disabled');
    expectDisabledSubscriberAction($html, 'subscriber-override-disabled');
    expectDisabledSubscriberAction($html, 'subscriber-transaction-disabled');
    expect($html)->not->toMatch('/<button(?=[^>]*data-test="subscriber-comment-disabled")[^>]*>/');
    expect($html)->toMatch('/<form(?=[^>]*method="post")(?=[^>]*action="'.preg_quote(route('subscribers.comments.store', 1), '/').'")/');

    expect($html)
        ->toMatch('/<input(?=[^>]*name="service_username")(?=[^>]*value="")[^>]*>/i')
        ->toMatch('/<input(?=[^>]*name="service_password")(?=[^>]*type="password")(?=[^>]*value="")[^>]*>/i');
});

test('subscriber detail displays persisted identity and keeps future data regions explicit', function () {
    $response = subscriberDetailsResponse($this);

    $response
        ->assertOk()
        ->assertSee('00001234')
        ->assertSee('Alex Santos')
        ->assertSee('alex@rodjen')
        ->assertSee('never-render-this-secret')
        ->assertSee('CALLER-00001234')
        ->assertSee('Not available')
        ->assertSee('No comments yet')
        ->assertSee('No transactions yet')
        ->assertSee('No system logs yet')
        ->assertSee('No activity logs yet');

    $html = $response->getContent();

    expect($html)
        ->not->toMatch('/<button(?![^>]*disabled)[^>]*>\s*(Confirm|Update|Submit)\s*<\/button>/i');
});

test('admins cannot view or change MikroTik passwords', function () {
    subscriberDetailsResponse($this);
    $subscriber = Subscriber::query()->firstOrFail();
    $admin = User::factory()->create(['user_type' => 'admin']);

    $this->actingAs($admin)
        ->get(route('subscribers.details', $subscriber))
        ->assertOk()
        ->assertSee('Hidden for your role')
        ->assertDontSee('never-render-this-secret');

    $this->patch(route('subscribers.mikrotik.update', $subscriber), [
        'username' => 'alex@rodjen',
        'profile' => 'default',
        'password' => 'replacement-secret',
    ])->assertRedirect(route('subscribers.details', $subscriber));

    expect($subscriber->refresh()->subscription->networkAccount->password)
        ->toBe('never-render-this-secret');
});

test('subscriber personal service and MikroTik sections have separate working editors', function () {
    $this->seed(PlanSeeder::class);
    $response = subscriberDetailsResponse($this);
    $subscriber = Subscriber::query()->firstOrFail();
    $plan = Plan::query()->where('transaction_type', 'mrc')->firstOrFail();

    $response
        ->assertOk()
        ->assertSee('aria-label="Edit Personal Details"', false)
        ->assertSee('aria-label="Edit Service Details"', false)
        ->assertSee('aria-label="Edit MikroTik Details"', false)
        ->assertSee('id="subscriber-personal-edit-dialog"', false)
        ->assertSee('id="subscriber-service-edit-dialog"', false)
        ->assertSee('id="subscriber-mikrotik-edit-dialog"', false)
        ->assertSee('name="account_number"', false)
        ->assertSee('name="plan_id"', false)
        ->assertSee('name="username"', false)
        ->assertSee('value="never-render-this-secret"', false)
        ->assertSee('Initial MikroTik comment')
        ->assertDontSee('<textarea name="comment" rows="3" maxlength="2000"', false);

    $this->patch(route('subscribers.personal.update', $subscriber), [
        'account_number' => '00004321',
        'first_name' => 'Jamie',
        'last_name' => 'Reyes',
        'area' => 'North Zone',
        'landmark' => 'Near the park',
        'billing_address' => '456 New Street',
        'contact_number' => '09123456789',
        'email' => 'jamie@example.com',
        'coordinates' => '14.5, 121.0',
        'remarks' => 'Updated personal details',
        'external_url' => 'https://example.com/accounts/00004321',
    ])->assertRedirect(route('subscribers.details', $subscriber));

    $subscriber->refresh();
    expect($subscriber->account_number)->toBe('00004321')
        ->and($subscriber->first_name)->toBe('Jamie')
        ->and($subscriber->area)->toBe('North Zone')
        ->and($subscriber->external_url)->toBe('https://example.com/accounts/00004321');

    $this->patch(route('subscribers.service.update', $subscriber), [
        'plan_id' => $plan->id,
        'service_type' => 'fiber',
        'status' => 'active',
        'installation_date' => '2026-01-15',
        'subscription_date' => '2026-01-20',
        'billing_day' => 15,
        'credit_limit' => '250.00',
    ])->assertRedirect(route('subscribers.details', $subscriber));

    $subscriber->refresh()->load('subscription');
    expect($subscriber->subscription->plan_id)->toBe($plan->id)
        ->and($subscriber->subscription->billing_day)->toBe(15)
        ->and($subscriber->subscription->credit_limit)->toBe('250.00');

    $this->patch(route('subscribers.mikrotik.update', $subscriber), [
        'username' => 'jamie@rodjen',
        'password' => 'updated-secret',
        'profile' => 'standard',
        'comment' => 'This value must be ignored',
    ])->assertRedirect(route('subscribers.details', $subscriber));

    $networkAccount = $subscriber->subscription()->firstOrFail()->networkAccount()->firstOrFail();
    expect($networkAccount->username)->toBe('jamie@rodjen')
        ->and($networkAccount->password)->toBe('updated-secret')
        ->and($networkAccount->profile)->toBe('standard')
        ->and($networkAccount->comment)->toBe('Initial MikroTik comment');
});
