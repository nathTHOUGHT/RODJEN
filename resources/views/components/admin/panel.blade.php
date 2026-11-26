@props(['title' => null])

<section {{ $attributes->class(['overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900']) }}>
    @if ($title || isset($actions))
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-slate-50/70 px-5 py-4 dark:border-slate-800 dark:bg-slate-900">
            @if ($title)
                <h2 class="font-semibold text-slate-900 dark:text-white">{{ $title }}</h2>
            @endif
            @isset($actions)
                <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
            @endisset
        </header>
    @endif
    <div class="p-5">{{ $slot }}</div>
</section>
