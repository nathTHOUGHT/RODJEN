<x-admin.panel title="Reminders" id="reminders" class="scroll-mt-28">
    @if ($mode !== 'day')
        <x-slot:actions>
            <button type="button" x-on:click="$refs.noteDialog.showModal()" class="text-sm font-semibold text-cyan-800 hover:underline xl:hidden dark:text-cyan-300">Add to this day</button>
        </x-slot:actions>
        <time datetime="{{ $selectedDateKey }}" class="block pb-3 text-sm font-semibold text-slate-900 dark:text-white">{{ $selectedDate->format('l, F j, Y') }}</time>
    @endif
    @if ($selectedNotes->isEmpty())
        <p class="text-sm text-slate-600 dark:text-slate-400">No reminders for this day.</p>
    @else
        <ul class="space-y-2.5">
            @foreach ($selectedNotes as $note)
                <li class="rounded-lg border-l-2 border-cyan-600 bg-slate-50 px-3 py-2.5 dark:border-cyan-400 dark:bg-slate-950">
                    <h3 class="wrap-anywhere text-sm font-semibold text-slate-900 dark:text-white">{{ $note->title }}</h3>
                    @if (filled($note->body))
                        <p class="mt-1 wrap-anywhere text-sm whitespace-pre-wrap text-slate-700 dark:text-slate-300">{{ $note->body }}</p>
                    @endif
                    <p class="mt-2 wrap-anywhere text-xs text-slate-600 dark:text-slate-400">
                        {{ $note->author_name }}
                        @if ($note->repeat !== 'none')
                            <span aria-hidden="true">·</span> {{ $repeatLabels[$note->repeat] }}
                        @endif
                    </p>
                </li>
            @endforeach
        </ul>
    @endif
</x-admin.panel>
