<x-layouts::app :title="__('Dashboard')">
    <x-admin.page-heading title="Dashboard" section="Overview">
        <x-slot:description>Welcome to the frontend foundation for subscriber, reporting, and network operations.</x-slot:description>
    </x-admin.page-heading>

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-3">
            @foreach (['Subscriber summary', 'Collection summary', 'Network summary'] as $summary)
                <x-admin.panel :title="$summary">
                    <p class="text-sm text-slate-500 dark:text-slate-400">No data available</p>
                </x-admin.panel>
            @endforeach
        </div>

        <x-admin.panel title="Month overview">
            <x-slot:actions>
                <label class="sr-only" for="dashboard-month">Month</label>
                <select id="dashboard-month" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950">
                    <option>Month</option>
                </select>
                <label class="sr-only" for="dashboard-year">Year</label>
                <select id="dashboard-year" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950">
                    <option>Year</option>
                </select>
            </x-slot:actions>

            <div class="overflow-x-auto" data-table-scroll>
                <div class="min-w-175">
                    <div class="grid grid-cols-7 border-l border-t border-slate-200 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:border-slate-700">
                        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="border-b border-r border-slate-200 bg-slate-50 px-2 py-3 dark:border-slate-700 dark:bg-slate-800">{{ $day }}</div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-7 border-l border-slate-200 dark:border-slate-700">
                        @foreach (range(1, 35) as $cell)
                            <div class="min-h-20 border-b border-r border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900"></div>
                        @endforeach
                    </div>
                </div>
            </div>
            <p class="mt-4 text-center text-sm text-slate-500 dark:text-slate-400">No activity scheduled</p>
        </x-admin.panel>
    </div>
</x-layouts::app>
