<x-admin.panel :title="$title" compact class="min-w-0">
    @if ($editable)
        <x-slot:actions>
            <button type="button"
                x-on:click="document.getElementById('{{ $editDialog }}').showModal()"
                aria-label="Edit {{ $title }}"
                title="Edit {{ $title }}"
                aria-haspopup="dialog"
                aria-controls="{{ $editDialog }}"
                class="grid size-5 place-items-center rounded-md border border-slate-300 text-slate-700 hover:border-cyan-600 hover:text-cyan-700 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-700 dark:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                    class="size-4">
                    <path d="M12 20h9" />
                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" />
                </svg>
                <span class="sr-only">Edit {{ $title }}</span>
            </button>
        </x-slot:actions>
    @endif

    <dl class="space-y-2 text-sm">
        @foreach ($fields as $field)
            <div class="{{ $rowLayout ?? 'flex items-baseline justify-between gap-3' }}">
                <dt class="font-medium text-slate-700 dark:text-slate-300">{{ $field }}</dt>
                <dd class="min-w-0 {{ $valueAlignment ?? 'text-right' }} wrap-anywhere text-slate-500 dark:text-slate-400">
                    @isset($values[$field])
                        @if ($field === 'External URL')
                            <a href="{{ $values[$field] }}" target="_blank" rel="noopener noreferrer" class="text-cyan-700 underline underline-offset-2 hover:text-cyan-800 dark:text-cyan-300 dark:hover:text-cyan-200">{{ $values[$field] }}</a>
                        @else
                            {{ $values[$field] }}
                        @endif
                    @else
                        <span aria-hidden="true">&mdash;</span><span class="sr-only">Not available</span>
                    @endisset
                </dd>
            </div>
        @endforeach
    </dl>
</x-admin.panel>
