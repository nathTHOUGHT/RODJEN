{{-- Shared administration toolbar rendered inside the admin shell. --}}

@php
    $initials =
        $authenticatedUser?->initials() ??
        str($displayName)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn($part) => str($part)->substr(0, 1)->upper()->value())
            ->implode('');
@endphp

<header
    class="sticky top-2 z-30 flex h-14 min-w-0 items-center gap-2 rounded-xl border border-slate-200 bg-white px-2.5 shadow-xs sm:gap-3 sm:px-3 lg:col-span-2 lg:col-start-1 lg:row-start-1"
    aria-label="Administration toolbar">
    <button type="button"
        class="shrink-0 rounded-md border border-slate-300 px-2.5 py-2 text-sm lg:hidden dark:border-slate-700"
        x-on:click="navigationOpen = !navigationOpen" x-bind:aria-expanded="navigationOpen"
        aria-controls="panel-navigation" aria-label="Toggle navigation">Menu</button>
    <a href="{{ $homeUrl }}" aria-label="RODJEN Technet Home"
        @if ($currentRoute === 'panel.home') aria-current="page" @endif
        class="flex shrink-0 items-center gap-3 rounded-md px-1 py-0.5 hover:opacity-80 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600 sm:px-2">
        <span aria-hidden="true" class="size-8 bg-contain bg-center bg-no-repeat sm:hidden"
            style="background-image: url('{{ asset('brand/rodjen-icon.png') }}')"></span>
        <span aria-hidden="true" class="hidden h-9 w-32 bg-contain bg-center bg-no-repeat sm:block"
            style="background-image: url('{{ asset('brand/rodjen-wordmark.png') }}')"></span>
        <span class="hidden border-l border-slate-200 pl-3 text-sm font-semibold text-slate-700 md:block">Administration
            Panel</span>
    </a>

    <div class="relative ml-auto min-w-0 max-w-52 flex-1" role="search" aria-label="Search panel pages"
        x-data="{
            query: '',
            open: false,
            pages: @js($searchPages),
            get matches() {
                const search = this.query.trim().toLowerCase();
                return this.pages.filter(page => (page.label + ' ' + page.group).toLowerCase().includes(search));
            }
        }" x-on:click.outside="open = false"
        x-on:focusout="if (!$el.contains($event.relatedTarget)) open = false" x-on:keydown.escape.stop="open = false">
        <label for="home-page-search" class="sr-only">Search pages</label>
        <input id="home-page-search" type="search" placeholder="Search pages" autocomplete="off"
            aria-controls="home-page-search-results"
            class="w-full min-w-0 rounded-lg border border-slate-300 bg-slate-50 px-2.5 py-2 text-sm text-slate-900 focus-visible:outline-2 focus-visible:outline-cyan-600 sm:px-3 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
            x-model="query" x-on:focus="open = true" x-on:input="open = true"
            x-on:keydown.enter.prevent="if (open && query.trim() && matches.length) window.location.assign(matches[0].url)">
        <div id="home-page-search-results"
            class="absolute right-0 top-full z-10 mt-2 max-h-[min(22rem,65dvh)] w-64 max-w-[calc(100vw-2rem)] overflow-y-auto rounded-lg border border-slate-200 bg-white p-1.5 shadow-lg sm:w-full dark:border-slate-700 dark:bg-slate-900"
            x-cloak x-show="open && query.trim()">
            <p class="px-2.5 py-2 text-sm text-slate-600 dark:text-slate-400" role="status" x-show="!matches.length">No
                pages found.</p>
            <template x-for="page in matches" x-bind:key="page.url">
                <a x-bind:href="page.url"
                    class="block rounded-md px-2.5 py-2 text-sm text-slate-900 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:text-white dark:hover:bg-slate-800">
                    <span x-text="page.label"></span>
                    <span class="block text-xs text-slate-500 dark:text-slate-400" x-show="page.group"
                        x-text="page.group"></span>
                </a>
            </template>
        </div>
    </div>

    <div class="relative shrink-0" x-data="{ hovered: false, expanded: false }" x-on:mouseenter="hovered = true"
        x-on:mouseleave="hovered = false" x-on:click.outside="expanded = false; hovered = false"
        x-on:focusout="if (!$el.contains($event.relatedTarget)) expanded = false"
        x-on:keydown.escape.stop="expanded = false; hovered = false; $refs.profileButton.focus()">
        <button type="button" aria-label="Profile menu" aria-controls="home-profile-menu"
            x-bind:aria-expanded="hovered || expanded" x-ref="profileButton" x-on:click="expanded = !expanded"
            class="grid size-9 place-items-center rounded-full border border-cyan-200 bg-cyan-50 text-xs font-semibold text-cyan-900 hover:bg-cyan-100 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-cyan-900 dark:bg-cyan-950 dark:text-cyan-200">{{ $initials }}</button>

        <div id="home-profile-menu" x-cloak x-show="hovered || expanded"
            class="absolute right-0 top-full z-10 w-48 pt-2">
            <div
                class="rounded-lg border border-slate-200 bg-white p-1.5 shadow-lg dark:border-slate-700 dark:bg-slate-900">
                <p
                    class="truncate border-b border-slate-100 px-2.5 py-2 text-sm font-medium text-slate-900 dark:border-slate-800 dark:text-white">
                    {{ $displayName }}</p>
                <a href="{{ $settingsUrl }}" style="--sidebar-icon: url('{{ asset('icons/sidebar/plug.svg') }}')"
                    class="sidebar-icon mt-1 flex items-center gap-2 rounded-md px-2.5 py-2 text-sm text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:text-slate-200 dark:hover:bg-slate-800">Settings</a>
                <form method="POST" action="{{ $logoutRoute }}">
                    @csrf
                    <button type="submit" data-test="logout-button"
                        style="--sidebar-icon: url('{{ asset('icons/sidebar/log-out.svg') }}')"
                        class="sidebar-icon mt-1 flex w-full items-center gap-2 rounded-md px-2.5 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:text-slate-200 dark:hover:bg-slate-800">Log
                        out</button>
                </form>
            </div>
        </div>
    </div>
</header>
