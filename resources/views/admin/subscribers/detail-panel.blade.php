<x-admin.panel :title="$title" class="min-w-0">
    @if ($editable)
        <x-slot:actions>
            <button type="button" disabled aria-label="Edit {{ $title }}" class="rounded-md border border-slate-300 px-2.5 py-1 text-xs font-medium disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700">Edit</button>
        </x-slot:actions>
    @endif

    <dl class="space-y-2 text-sm">
        @foreach ($fields as $field)
            <div class="flex items-baseline justify-between gap-3">
                <dt class="font-medium text-slate-700 dark:text-slate-300">{{ $field }}</dt>
                <dd class="min-w-0 text-right wrap-anywhere text-slate-500 dark:text-slate-400">
                    @isset($values[$field])
                        {{ $values[$field] }}
                    @else
                        <span aria-hidden="true">&mdash;</span><span class="sr-only">Not available</span>
                    @endisset
                </dd>
            </div>
        @endforeach
    </dl>
</x-admin.panel>
