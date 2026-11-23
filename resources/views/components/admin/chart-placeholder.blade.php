@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->class(['relative min-h-64 overflow-hidden rounded-lg border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-950']) }}>
    <div class="relative z-10 text-center">
        <h3 class="font-semibold text-slate-800 dark:text-slate-100">{{ $title }}</h3>
        @if ($subtitle)
            <p class="mt-1 text-xs uppercase tracking-[0.16em] text-slate-400">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="absolute inset-x-5 bottom-5 top-20 grid grid-rows-4 border-b border-l border-slate-300/80 dark:border-slate-700">
        @foreach (range(1, 4) as $line)
            <span class="border-t border-slate-200 dark:border-slate-800"></span>
        @endforeach
    </div>
</div>
