<?php

use App\Models\User;

function panelNavigationXPath(string $html): DOMXPath
{
    $document = new DOMDocument;
    $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET);

    return new DOMXPath($document);
}

function panelNavigationLinks(DOMXPath $document, string $query): array
{
    $links = [];

    foreach ($document->query($query) as $anchor) {
        $links[] = [$anchor->getAttribute('href'), trim($anchor->textContent)];
    }

    return $links;
}

test('the sidebar lists the requested destinations and settings opens from the profile menu', function () {
    $this->actingAs(User::factory()->create());

    $page = $this->get(route('dashboard'))->assertOk();
    $document = panelNavigationXPath($page->getContent());

    expect(panelNavigationLinks($document, '//*[@id="panel-navigation"]//a[@href]'))->toBe([
        [route('dashboard'), 'Dashboard'],
        [route('reports.index'), 'Reports'],
        [route('subscribers.index'), 'Records'],
        [route('subscribers.create'), '+'],
        [route('integrations.messaging'), 'Chats'],
        [route('job-orders.index'), 'Job Order'],
        [route('inventory.index'), 'Inventory'],
        [route('logs.index'), 'Logs'],
    ]);

    expect(panelNavigationLinks($document, '//*[@id="home-profile-menu"]//a[@href]'))
        ->toContain([route('panel.settings'), 'Settings']);

    $this->get(route('panel.settings'))->assertRedirect(route('products.index'));
});

test('the settings tabs link to each existing page and highlight the current section', function (string $activeRoute) {
    $this->actingAs(User::factory()->create());

    $page = $this->get(route($activeRoute))->assertOk();
    $document = panelNavigationXPath($page->getContent());
    $navigation = '//nav[@aria-label="Settings sections"]';

    expect(panelNavigationLinks($document, $navigation.'//a[@href]'))->toBe([
        [route('products.index'), 'Plans'],
        [route('policies.index'), 'Policy'],
        [route('integrations.scheduler'), 'Scheduler / Messaging'],
        [route('integrations.settings'), 'Automations'],
        [route('router.index'), 'MikroTik'],
        [route('staff.index'), 'User Profiles'],
    ]);

    $selected = $document->query($navigation.'//a[@aria-current="page"]');

    expect($selected->length)->toBe(1);
    expect($selected->item(0)->getAttribute('href'))
        ->toBe(route($activeRoute === 'router.backups' ? 'router.index' : $activeRoute));
})->with([
    'products.index',
    'policies.index',
    'integrations.scheduler',
    'integrations.settings',
    'router.index',
    'router.backups',
    'staff.index',
]);

test('the logs entry opens system logs and exposes both log pages', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('logs.index'))->assertRedirect(route('logs.system'));

    $page = $this->get(route('logs.activity'))->assertOk();
    $document = panelNavigationXPath($page->getContent());
    $navigation = '//nav[@aria-label="Logs sections"]';

    expect(panelNavigationLinks($document, $navigation.'//a[@href]'))->toBe([
        [route('logs.system'), 'System logs'],
        [route('logs.activity'), 'Activity logs'],
    ]);
    expect(panelNavigationLinks($document, $navigation.'//a[@aria-current="page"]'))->toBe([
        [route('logs.activity'), 'Activity logs'],
    ]);
});
