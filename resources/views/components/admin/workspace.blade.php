@props(['title', 'tabs', 'active'])

<x-layouts::app :title="$title">
    <x-admin.page-heading :title="$title" />

    <nav aria-label="{{ $title }} sections" class="mb-4 flex flex-wrap gap-1.5 rounded-xl border border-slate-200 bg-white p-1.5 shadow-xs">
        @foreach ($tabs as $route => $label)
            <a
                href="{{ route($route) }}"
                @if ($route === $active) aria-current="page" @endif
                @class([
                    'rounded-lg px-3 py-2 text-sm font-medium focus-visible:outline-2 focus-visible:outline-cyan-600',
                    'bg-cyan-100 text-cyan-900' => $route === $active,
                    'text-slate-700 hover:bg-slate-100 hover:text-cyan-900' => $route !== $active,
                ])
            >{{ $label }}</a>
        @endforeach
    </nav>

    <div class="space-y-4">
        {{ $slot }}
    </div>
</x-layouts::app>
