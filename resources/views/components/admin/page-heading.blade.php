@props([
    'title',
    'section' => null,
])

<header {{ $attributes->class(['mb-6']) }}>
    @if ($section)
        <p class="text-xs font-semibold uppercase tracking-wider text-cyan-700">{{ $section }}</p>
    @endif
    <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ $title }}</h1>
    @if (isset($description))
        <div class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $description }}</div>
    @endif
</header>
