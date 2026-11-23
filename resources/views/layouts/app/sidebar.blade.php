@php
    $authenticatedUser = auth()->user();
    $previewUser = session('preview.user');
    $displayName = $authenticatedUser?->name ?? data_get($previewUser, 'name', 'Admin');
    $logoutRoute = $authenticatedUser ? route('logout') : route('preview.logout');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-700 antialiased dark:bg-slate-950 dark:text-slate-200">
        <div x-data="{ navigationOpen: false }" class="min-h-screen lg:grid lg:grid-cols-[260px_minmax(0,1fr)]">
            <aside
                class="fixed inset-y-0 left-0 z-40 flex w-65 -translate-x-full flex-col bg-slate-900 transition-transform lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
                :class="navigationOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                aria-label="Primary navigation"
            >
                <div class="border-b border-slate-700 p-4">
                    <x-app-logo href="{{ route('dashboard') }}" />
                </div>

                <nav class="flex-1 overflow-y-auto px-3 py-5">
                    <p class="px-3 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">Workspace</p>
                    <a href="{{ route('dashboard') }}" class="mt-2 block rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-cyan-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        Dashboard
                    </a>
                </nav>

                <x-desktop-user-menu :name="$displayName" :logout-route="$logoutRoute" />
            </aside>

            <div class="min-w-0">
                <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/95 px-4 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95 sm:px-6">
                    <button type="button" class="rounded-md border border-slate-300 px-3 py-2 text-sm lg:hidden dark:border-slate-700" x-on:click="navigationOpen = ! navigationOpen" aria-label="Toggle navigation">
                        Menu
                    </button>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-[0.16em] text-slate-400">Administration Panel</p>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $displayName }}</p>
                    </div>
                </header>

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
