<x-layouts::app :title="__('System logs')">
    <x-admin.page-heading title="System logs" section="Logs" />
    <div class="space-y-4">
        <x-admin.panel title="Records">
            <x-slot:actions>
                <button type="button" disabled data-test="system-log-export-disabled" class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Export</button>
            </x-slot:actions>
            <div class="mb-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (['Year', 'Log type', 'Entries per page', 'Search'] as $label)
                    <label class="grid gap-1 text-sm font-medium">
                        {{ $label }}
                        <input type="{{ $label === 'Search' ? 'search' : 'text' }}" disabled class="rounded-md border border-slate-300 px-3 py-2 dark:border-slate-700 dark:bg-slate-950">
                    </label>
                @endforeach
            </div>
            <x-admin.empty-table :columns="['Timestamp', 'Description']" message="No system logs yet" />
            <p class="mt-4 text-sm text-slate-500">Pagination will appear when logs are available.</p>
        </x-admin.panel>
    </div>
</x-layouts::app>
