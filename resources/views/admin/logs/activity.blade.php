<x-layouts::app :title="__('Activity logs')">
    <x-admin.page-heading title="Activity logs" section="Logs" />
    <div class="space-y-4">
        <x-admin.chart-placeholder title="Activity notes" subtitle="Monthly / Weekly" />
        <x-admin.panel title="Records">
            <x-slot:actions>
                <button type="button" disabled data-test="activity-add-disabled" class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Add note</button>
            </x-slot:actions>
            <div class="mb-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (['Year', 'Type'] as $label)
                    <label class="grid gap-1 text-sm font-medium">
                        {{ $label }}
                        <input type="{{ $label === 'Search' ? 'search' : 'text' }}" disabled class="rounded-md border border-slate-300 px-3 py-2 dark:border-slate-700 dark:bg-slate-950">
                    </label>
                @endforeach
            </div>
            <x-admin.empty-table :columns="['Select', 'Timestamp', 'Information', 'Updated by']" message="No activity notes yet" />
        </x-admin.panel>
    </div>
</x-layouts::app>
