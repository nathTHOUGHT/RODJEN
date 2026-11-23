@props([
    'title',
    'section' => null,
])

<header {{ $attributes->class(['mb-6']) }}>
    @if ($section)
        <p class="text-sm font-medium text-cyan-700 dark:text-cyan-400">{{ $section }}</p>
    @endif
    <h1 class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white">{{ $title }}</h1>
    @if (isset($description))
        <div class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $description }}</div>
    @endif
</header>
