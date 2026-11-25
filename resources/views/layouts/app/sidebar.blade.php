@php
    $authenticatedUser = auth()->user();
    $previewUser = session('preview.user');
    $displayName = $authenticatedUser?->name ?? data_get($previewUser, 'name', 'Admin');
    $logoutRoute = $authenticatedUser ? route('logout') : route('preview.logout');
    $isHome = request()->routeIs('panel.home');
    $sidebarLinkClasses = 'sidebar-icon flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium focus-visible:outline-2 focus-visible:outline-cyan-300 focus-visible:outline-offset-[-2px]';
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
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-700 antialiased dark:bg-slate-950 dark:text-slate-200">
        <div x-data="{ navigationOpen: false }" @class(['min-h-screen lg:grid lg:grid-cols-[260px_minmax(0,1fr)]', 'lg:grid-rows-[4rem_1fr]' => $isHome])>
            @if ($isHome)
                @include('layouts.app.home-header')
            @endif
            <aside
                @class([
                    'fixed inset-y-0 left-0 z-40 flex w-65 -translate-x-full flex-col bg-slate-900 transition-transform lg:sticky lg:translate-x-0',
                    'lg:top-16 lg:h-[calc(100dvh-4rem)]' => $isHome,
                    'lg:top-0 lg:h-screen' => ! $isHome,
                ])
                :class="navigationOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                aria-label="Primary navigation"
            >
                <div class="border-b border-slate-700 p-4">
                    <x-app-logo
                        href="{{ route('panel.home') }}"
                        :aria-current="$isHome ? 'page' : null"
                        aria-label="Home — ISP Operations Administration Panel"
                        class="sidebar-icon sidebar-logo hover:bg-slate-800 focus-visible:outline-2 focus-visible:outline-cyan-300"
                        style="--sidebar-icon: url('{{ asset('icons/sidebar/house.svg') }}')"
                    />
                </div>

                <nav class="sidebar-scroll min-h-0 flex-1 space-y-6 overflow-y-auto px-3 py-5">
                    @foreach ($navigationGroups as $group => $items)
                        <div>
                            @if (is_string($group))
                                <h2 class="px-3 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $group }}</h2>
                            @endif
                            <div @class(['space-y-1', 'mt-2' => is_string($group)])>
                                @foreach ($items as $item)
                                    @php
                                        $current = request()->routeIs($item['route']);
                                        $active = $current || ($item['route'] === 'subscribers.index' && request()->routeIs('subscribers.details'));
                                    @endphp
                                    <a
                                        href="{{ route($item['route']) }}"
                                        style="--sidebar-icon: url('{{ asset('icons/sidebar/'.$item['icon'].'.svg') }}')"
                                        @if ($current) aria-current="page" @endif
                                        class="{{ $sidebarLinkClasses }} {{ $active ? 'bg-cyan-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                    >
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>

                <x-desktop-user-menu :name="$displayName" :logout-route="$logoutRoute" />
            </aside>

            <div class="min-w-0">
                @unless ($isHome)
                    <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/95 px-4 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95 sm:px-6">
                        <button type="button" class="rounded-md border border-slate-300 px-3 py-2 text-sm lg:hidden dark:border-slate-700" x-on:click="navigationOpen = ! navigationOpen" aria-label="Toggle navigation">
                            Menu
                        </button>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-slate-400">Administration Panel</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $displayName }}</p>
                        </div>
                    </header>
                @endunless

                <main class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>

            <button
                type="button"
                x-cloak
                x-show="navigationOpen"
                x-on:click="navigationOpen = false"
                class="fixed inset-0 z-30 bg-slate-950/60 lg:hidden"
                aria-label="Close navigation"
            ></button>
        </div>

        @fluxScripts
    </body>
</html>
