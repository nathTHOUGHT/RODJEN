@php
    $authenticatedUser = auth()->user();
    $displayName = $authenticatedUser?->name ?? session('preview.user.name') ?? 'Admin';
    $logoutRoute = $authenticatedUser ? route('logout') : route('preview.logout');
    $currentRoute = request()->route()?->getName();
    $navigationGroups = [
        'Overview' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
            ['label' => 'Reports', 'route' => 'reports.index', 'icon' => 'chart-no-axes-combined'],
        ],
        'Subscriptions' => [
            ['label' => 'Subscriber records', 'caption' => 'Records', 'route' => 'subscribers.index', 'icon' => 'contact-round'],
            ['label' => 'Add subscriber', 'caption' => '+', 'route' => 'subscribers.create'],
        ],
        'Communication' => [
            ['label' => 'Chats', 'route' => 'integrations.messaging', 'icon' => 'messages-square'],
        ],
        'Operations' => [
            ['label' => 'Job Order', 'route' => 'job-orders.index', 'icon' => 'receipt'],
            ['label' => 'Inventory', 'route' => 'inventory.index', 'icon' => 'package'],
        ],
        'Logs' => [
            ['label' => 'Logs', 'route' => 'logs.index', 'icon' => 'scroll-text'],
        ],
    ];

    $homeUrl = route('panel.home');
    $navigationUrls = [];
    $settingsUrl = route('panel.settings');
    $searchPages = [
        ['label' => 'Home', 'group' => '', 'url' => $homeUrl],
        ['label' => 'Settings', 'group' => '', 'url' => $settingsUrl],
    ];

    foreach ($navigationGroups as $group => $items) {
        foreach ($items as $item) {
            $url = route($item['route']);
            $navigationUrls[$item['route']] = $url;
            $searchPages[] = ['label' => $item['label'], 'group' => $group, 'url' => $url];
        }
    }

    $settingsTabs = require resource_path('data/panel-settings-tabs.php');
    foreach ($settingsTabs + ['router.backups' => 'Router backups'] as $routeName => $label) {
        $searchPages[] = ['label' => $label, 'group' => 'Settings', 'url' => route($routeName)];
    }

    $searchPages[] = ['label' => 'System logs', 'group' => 'Logs', 'url' => route('logs.system')];
    $searchPages[] = ['label' => 'Activity logs', 'group' => 'Logs', 'url' => route('logs.activity')];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scrollbar-hidden">
    <head>
        @include('partials.head')
    </head>
    <body class="flow-root min-h-screen bg-slate-50 text-slate-700 antialiased">
        <div x-data="{ navigationOpen: false }" class="m-2 grid gap-2.5 p-2 sm:m-3 lg:min-h-[calc(100dvh-1.5rem)] lg:grid-cols-[14rem_minmax(0,1fr)] lg:grid-rows-[3.5rem_1fr]">
            @include('layouts.app.panel-header')
            <aside
                id="panel-navigation"
                class="fixed inset-y-0 left-0 z-40 flex w-65 -translate-x-full flex-col bg-slate-100 transition-transform data-[open=true]:translate-x-0 lg:sticky lg:top-[4.625rem] lg:col-start-1 lg:row-start-2 lg:h-[calc(100dvh-6.75rem)] lg:w-full lg:translate-x-0 lg:rounded-lg lg:border lg:border-slate-200"
                x-bind:data-open="navigationOpen"
                aria-label="Primary navigation"
            >
                <nav class="scrollbar-hidden min-h-0 flex-1 divide-y divide-slate-200 overflow-y-auto px-2.5 py-1">
                    @foreach ($navigationGroups as $group => $items)
                        <div
                            role="group"
                            aria-label="{{ $group }}"
                            @class([
                                'py-2',
                                'grid grid-cols-[minmax(0,1fr)_auto] gap-1' => $group === 'Subscriptions',
                                'space-y-1' => $group !== 'Subscriptions',
                            ])
                        >
                            @foreach ($items as $item)
                                @php
                                    $routeName = $item['route'];
                                    $current = $routeName === $currentRoute;
                                    $active = $current
                                        || ($routeName === 'subscribers.index' && $currentRoute === 'subscribers.details')
                                        || ($routeName === 'logs.index' && request()->routeIs('logs.*'));
                                @endphp
                                <a
                                    href="{{ $navigationUrls[$routeName] }}"
                                    @isset($item['icon'])
                                        style="--sidebar-icon: url('{{ asset('icons/sidebar/'.$item['icon'].'.svg') }}')"
                                    @endisset
                                    @isset($item['caption'])
                                        aria-label="{{ $item['label'] }}"
                                    @endisset
                                    @if ($routeName === 'subscribers.create') title="{{ $item['label'] }}" @endif
                                    @if ($current) aria-current="page" @endif
                                    @class([
                                        'flex items-center rounded-lg px-3 py-2 text-sm font-medium focus-visible:outline-2 focus-visible:outline-cyan-600 focus-visible:outline-offset-[-2px]',
                                        'sidebar-icon gap-2.5' => isset($item['icon']),
                                        'min-w-10 justify-center' => $routeName === 'subscribers.create',
                                        'bg-cyan-100 font-semibold text-cyan-900 inset-ring inset-ring-cyan-200' => $active,
                                        'text-slate-700 hover:bg-white hover:text-cyan-800' => ! $active,
                                    ])
                                >
                                    {{ $item['caption'] ?? $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </nav>
            </aside>

            <main class="h-full min-w-0 lg:col-start-2 lg:row-start-2">
                {{ $slot }}
            </main>

            <button
                type="button"
                x-cloak
                x-show="navigationOpen"
                x-on:click="navigationOpen = false"
                class="fixed inset-0 z-30 bg-slate-700/30 lg:hidden"
                aria-label="Close navigation"
            ></button>
        </div>

        @fluxScripts
    </body>
</html>
