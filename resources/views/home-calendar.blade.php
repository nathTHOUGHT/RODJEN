@php
    $selectedDateKey = $selectedDate->toDateString();
    $selectedDay = collect($days)->first(fn ($day) => $day['date']->toDateString() === $selectedDateKey);
    $selectedNotes = $selectedDay['notes'] ?? collect();
    $repeatLabels = ['none' => 'Does not repeat', 'daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'];
    $oldNote = array_filter(old(), 'is_string');
    $fieldClasses = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-700 dark:bg-slate-950 dark:text-white';
@endphp

<x-layouts::app :title="__('Home')">
    <div x-data @if ($errors->any()) x-init="$nextTick(() => $refs.noteDialog.showModal())" @endif>
        @if (session('status'))
            <p role="status" class="mb-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm font-medium text-cyan-900 dark:border-cyan-900 dark:bg-cyan-950 dark:text-cyan-200">
                {{ session('status') }}
            </p>
        @endif

        <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_17rem]">
            <section aria-labelledby="home-calendar-title" class="min-w-0 rounded-xl border border-slate-200 bg-white p-3 shadow-sm sm:p-5 dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <h1 id="home-calendar-title" class="text-2xl font-semibold text-slate-900 dark:text-white">Calendar</h1>
                    <button
                        type="button"
                        aria-haspopup="dialog"
                        x-on:click="$refs.noteDialog.showModal()"
                        class="rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600"
                    >Add event</button>
                </div>

                <div class="mb-4 flex flex-wrap items-center justify-between gap-x-6 gap-y-3 border-b border-slate-200 dark:border-slate-800">
                    <nav aria-label="Calendar view" class="flex gap-4">
                        @foreach (['day' => 'Daily', 'week' => 'Weekly', 'month' => 'Monthly'] as $view => $label)
                            <a
                                href="{{ route('panel.home', ['view' => $view, 'date' => $selectedDateKey]) }}"
                                @if ($mode === $view) aria-current="page" @endif
                                @class([
                                    '-mb-px border-b-2 px-1 pb-3 text-sm font-semibold focus-visible:outline-2 focus-visible:outline-cyan-600',
                                    'border-cyan-700 text-cyan-800 dark:border-cyan-400 dark:text-cyan-300' => $mode === $view,
                                    'border-transparent text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' => $mode !== $view,
                                ])
                            >
                                {{ $label }}
                            </a>
                        @endforeach
                    </nav>
                </div>

                <div class="mb-4 flex flex-wrap items-center gap-3">
                    <nav aria-label="Calendar date" class="flex items-center gap-1">
                        <a href="{{ route('panel.home', ['view' => $mode, 'date' => $today->toDateString()]) }}" class="mr-2 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm font-semibold hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800">Today</a>
                        <a href="{{ route('panel.home', ['view' => $mode, 'date' => $previousDate->toDateString()]) }}" aria-label="Previous {{ $mode }}" class="grid size-8 place-items-center rounded-md text-xl hover:bg-slate-200 dark:hover:bg-slate-800">&lsaquo;</a>
                        <a href="{{ route('panel.home', ['view' => $mode, 'date' => $nextDate->toDateString()]) }}" aria-label="Next {{ $mode }}" class="grid size-8 place-items-center rounded-md text-xl hover:bg-slate-200 dark:hover:bg-slate-800">&rsaquo;</a>
                    </nav>
                    <h2 class="font-semibold text-slate-900 dark:text-white">{{ $periodLabel }}</h2>
                </div>

                @if ($mode !== 'day')
                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50 text-center text-xs font-semibold text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $weekday)
                                <span class="py-2.5">{{ $weekday }}</span>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-7 gap-px bg-slate-200 dark:bg-slate-800">
                            @foreach ($days as $day)
                                @php
                                    $date = $day['date'];
                                    $dateKey = $date->toDateString();
                                    $isSelected = $dateKey === $selectedDateKey;
                                    $isToday = $dateKey === $today->toDateString();
                                @endphp
                                <a
                                    href="{{ route('panel.home', ['view' => $mode, 'date' => $dateKey]) }}#reminders"
                                    aria-label="{{ $date->format('l, F j, Y') }}{{ $isToday ? ', today' : '' }}{{ $day['notes']->isNotEmpty() ? ', '.$day['notes']->count().' '.str('reminder')->plural($day['notes']->count()) : '' }}"
                                    @if ($isSelected) aria-current="date" @endif
                                    @class([
                                        'min-h-14 min-w-0 px-1 py-2 text-center focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-cyan-600 sm:min-h-24 sm:px-2 sm:text-left',
                                        'bg-cyan-50 dark:bg-cyan-950' => $isSelected,
                                        'bg-white hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800' => ! $isSelected && $day['inPeriod'],
                                        'bg-slate-50 text-slate-500 hover:bg-slate-100 dark:bg-slate-950 dark:text-slate-400 dark:hover:bg-slate-800' => ! $isSelected && ! $day['inPeriod'],
                                        'sm:min-h-32' => $mode === 'week',
                                    ])
                                >
                                    <time datetime="{{ $dateKey }}" @class([
                                        'inline-flex size-7 items-center justify-center rounded-full text-xs font-semibold',
                                        'bg-cyan-700 text-white' => $isToday,
                                    ])>{{ $date->day }}</time>

                                    @if ($day['notes']->isNotEmpty())
                                        <span aria-hidden="true" class="mx-auto mt-1 block size-1.5 rounded-full bg-cyan-700 dark:bg-cyan-400 sm:hidden"></span>
                                        <ul class="mt-1 hidden space-y-1 sm:block">
                                            @foreach ($day['notes']->take(2) as $note)
                                                <li class="truncate rounded bg-cyan-100 px-1.5 py-0.5 text-xs font-medium text-cyan-950 dark:bg-cyan-900 dark:text-cyan-100">{{ $note->title }}</li>
                                            @endforeach
                                            @if ($day['notes']->count() > 2)
                                                <li class="text-xs text-slate-600 dark:text-slate-300">+{{ $day['notes']->count() - 2 }} more</li>
                                            @endif
                                        </ul>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    @include('home-calendar-reminders')
                @endif
            </section>

            <div class="flex min-w-0 flex-col gap-4">
                <x-admin.panel title="Scheduled bills" class="flex-1">
                    <x-slot:actions>
                        <button type="button" aria-label="Add scheduled bill" aria-haspopup="dialog" x-on:click="$refs.billDialog.showModal()" class="rounded-md border border-slate-300 px-3 py-1.5 text-sm font-semibold hover:border-cyan-600 dark:border-slate-700">Add</button>
                    </x-slot:actions>
                    <p class="text-sm text-slate-600 dark:text-slate-400">No scheduled bills to display.</p>
                </x-admin.panel>

                @if ($mode !== 'day')
                    @include('home-calendar-reminders')
                @endif
            </div>
        </div>

        <dialog
            x-ref="noteDialog"
            aria-labelledby="note-dialog-heading"
            class="m-auto max-h-[calc(100dvh-2rem)] w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-xl border border-slate-200 bg-white p-6 text-slate-900 shadow-xl backdrop:bg-slate-950/60 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
        >
            <h2 id="note-dialog-heading" class="text-xl font-semibold">Add event</h2>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Everyone with staff access can see this note.</p>

            <form method="POST" action="{{ route('panel.home.reminders.store') }}" class="mt-5 space-y-4">
                @csrf
                <input type="hidden" name="view" value="{{ $mode }}">
                <div>
                    <label for="note-title" class="mb-1 block text-sm font-semibold">Title</label>
                    <input id="note-title" name="title" value="{{ $oldNote['title'] ?? '' }}" maxlength="80" required autofocus class="{{ $fieldClasses }}" @error('title') aria-invalid="true" aria-describedby="note-title-error" @enderror>
                    @error('title') <p id="note-title-error" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p> @enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="note-date" class="mb-1 block text-sm font-semibold">Date</label>
                        <input id="note-date" name="date" type="date" value="{{ $oldNote['date'] ?? $selectedDateKey }}" required class="{{ $fieldClasses }}" @error('date') aria-invalid="true" aria-describedby="note-date-error" @enderror>
                        @error('date') <p id="note-date-error" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="note-repeat" class="mb-1 block text-sm font-semibold">Repeat</label>
                        <select id="note-repeat" name="repeat" class="{{ $fieldClasses }}" @error('repeat') aria-invalid="true" aria-describedby="note-repeat-error" @enderror>
                            @foreach ($repeatLabels as $frequency => $label)
                                <option value="{{ $frequency }}" @selected(($oldNote['repeat'] ?? 'none') === $frequency)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('repeat') <p id="note-repeat-error" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label for="note-body" class="mb-1 block text-sm font-semibold">Note <span class="font-normal text-slate-500 dark:text-slate-400">(optional)</span></label>
                    <textarea id="note-body" name="body" rows="4" maxlength="1000" class="{{ $fieldClasses }}" @error('body') aria-invalid="true" aria-describedby="note-body-error" @enderror>{{ $oldNote['body'] ?? '' }}</textarea>
                    @error('body') <p id="note-body-error" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p> @enderror
                </div>
                <div class="flex flex-wrap justify-end gap-2 pt-2">
                    <button type="button" x-on:click="$refs.noteDialog.close()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800">Cancel</button>
                    <button type="submit" class="rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Save event</button>
                </div>
            </form>
        </dialog>

        <dialog x-ref="billDialog" id="scheduled-bill-dialog" aria-labelledby="bill-dialog-heading" class="m-auto max-h-[calc(100dvh-2rem)] w-[calc(100%-2rem)] max-w-md overflow-y-auto rounded-xl border border-slate-200 bg-white p-6 text-slate-900 shadow-xl backdrop:bg-slate-950/60 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            <h2 id="bill-dialog-heading" class="text-xl font-semibold">Add scheduled bill</h2>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Scheduling will be available when billing is connected.</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-semibold sm:col-span-2">
                    Subscriber or account number
                    <input type="text" class="{{ $fieldClasses }}">
                </label>
                <label class="grid gap-1 text-sm font-semibold">
                    Due date
                    <input type="date" value="{{ $selectedDateKey }}" class="{{ $fieldClasses }}">
                </label>
                <label class="grid gap-1 text-sm font-semibold">
                    Amount
                    <input type="number" min="0" step="0.01" class="{{ $fieldClasses }}">
                </label>
            </div>
            <form method="dialog" class="mt-5 flex flex-wrap justify-end gap-2">
                <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800">Cancel</button>
                <button type="button" disabled class="cursor-not-allowed rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white opacity-50">Save bill</button>
            </form>
        </dialog>
    </div>
</x-layouts::app>
