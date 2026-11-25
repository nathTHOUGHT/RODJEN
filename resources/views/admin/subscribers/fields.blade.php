@foreach ($fields as [$id, $label, $type])
    <label for="{{ $id }}" class="grid min-w-0 gap-1.5 text-sm font-medium text-slate-700 dark:text-slate-300">
        {{ $label }}
        <input
            id="{{ $id }}"
            type="{{ $type }}"
            value=""
            @if ($type === 'password') autocomplete="new-password" @endif
            class="w-full min-w-0 rounded-full border border-slate-300 bg-white px-3 py-2 font-normal text-slate-900 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
        >
    </label>
@endforeach
