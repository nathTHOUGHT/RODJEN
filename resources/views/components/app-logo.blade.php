@props([
    'sidebar' => false,
])

<a {{ $attributes->class(['block rounded-lg px-3 py-2 text-white']) }}>
    <span class="block text-[10px] font-semibold uppercase tracking-[0.2em] text-cyan-300">ISP Operations</span>
    <span class="mt-1 block text-sm font-semibold">Administration Panel</span>
</a>
