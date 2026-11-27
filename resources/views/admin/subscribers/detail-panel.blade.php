<x-admin.panel :title="$title" class="min-w-0">
    @if ($editable)
        <x-slot:actions>
            <button type="button"
                x-on:click="document.getElementById('{{ $editDialog }}').showModal()"
                aria-label="Edit {{ $title }}"
                aria-haspopup="dialog"
                aria-controls="{{ $editDialog }}"
                class="rounded-md border border-slate-300 px-2.5 py-1 text-xs font-medium hover:border-cyan-600 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-700">Edit</button>
        </x-slot:actions>
    @endif

    <dl class="space-y-2 text-sm">
        @foreach ($fields as $field)
            <div class="flex items-baseline justify-between gap-3">
                <dt class="font-medium text-slate-700 dark:text-slate-300">{{ $field }}</dt>
                <dd class="min-w-0 text-right wrap-anywhere text-slate-500 dark:text-slate-400">
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
