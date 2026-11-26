@php
    $authenticatedUser = auth()->user();
    $displayName = $authenticatedUser?->name ?? session('preview.user.name') ?? 'Admin';
    $logoutRoute = $authenticatedUser ? route('logout') : route('preview.logout');
    $currentRoute = request()->route()?->getName();
    $navigationGroups = [
        [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
        ],
        'Subscriptions' => [
            ['label' => 'Add subscriber', 'route' => 'subscribers.create', 'icon' => 'user-round-plus'],
            ['label' => 'Subscriber records', 'route' => 'subscribers.index', 'icon' => 'contact-round'],
            ['label' => 'Products and services', 'route' => 'products.index', 'icon' => 'package'],
            ['label' => 'Policies and conditions', 'route' => 'policies.index', 'icon' => 'shield-check'],
        ],
        [
            ['label' => 'Reports', 'route' => 'reports.index', 'icon' => 'chart-no-axes-combined'],
        ],
        'API Integration' => [
            ['label' => 'Messaging', 'route' => 'integrations.messaging', 'icon' => 'messages-square'],
            ['label' => 'Scheduler', 'route' => 'integrations.scheduler', 'icon' => 'clock'],
            ['label' => 'Settings', 'route' => 'integrations.settings', 'icon' => 'plug'],
        ],
        'Settings' => [
            ['label' => 'Router', 'route' => 'router.index', 'icon' => 'router'],
            ['label' => 'Router backups', 'route' => 'router.backups', 'icon' => 'database-backup'],
            ['label' => 'Staff users', 'route' => 'staff.index', 'icon' => 'users-round'],
        ],
        'Logs' => [
            ['label' => 'System logs', 'route' => 'logs.system', 'icon' => 'scroll-text'],
            ['label' => 'Activity logs', 'route' => 'logs.activity', 'icon' => 'activity'],
        ],
    ];

    $homeUrl = route('panel.home');
    $navigationUrls = [];
    $searchPages = [['label' => 'Home', 'group' => '', 'url' => $homeUrl]];

    foreach ($navigationGroups as $group => $items) {
        $groupLabel = is_string($group) ? $group : '';

        foreach ($items as $item) {
            $url = route($item['route']);
            $navigationUrls[$item['route']] = $url;
            $searchPages[] = ['label' => $item['label'], 'group' => $groupLabel, 'url' => $url];
        }
    }
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
                <nav class="scrollbar-hidden min-h-0 flex-1 space-y-3 overflow-y-auto px-2.5 py-3">
                    @foreach ($navigationGroups as $group => $items)
                        <div>
                            @if (is_string($group))
                                    <h2 class="px-3 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-600">{{ $group }}</h2>
                            @endif
                            <div @class(['space-y-1', 'mt-2' => is_string($group)])>
                                @foreach ($items as $item)
                                    @php
                                        $routeName = $item['route'];
                                        $current = $routeName === $currentRoute;
                                        $active = $current || ($routeName === 'subscribers.index' && $currentRoute === 'subscribers.details');
                                    @endphp
                                    <a
                                        href="{{ $navigationUrls[$routeName] }}"
                                        style="--sidebar-icon: url('{{ asset('icons/sidebar/'.$item['icon'].'.svg') }}')"
                                        @if ($current) aria-current="page" @endif
                                        @class([
                                            'sidebar-icon flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium focus-visible:outline-2 focus-visible:outline-cyan-600 focus-visible:outline-offset-[-2px]',
                                            'bg-cyan-100 font-semibold text-cyan-900 inset-ring inset-ring-cyan-200' => $active,
                                            'text-slate-700 hover:bg-white hover:text-cyan-800' => ! $active,
                                        ])
                                    >
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
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
