<x-layouts::app :title="__('Subscriber records')">
    <x-admin.page-heading title="Subscriber records" section="Subscriptions" />

    <div class="space-y-6">
        <x-admin.frontend-notice />
        <x-admin.panel title="Records">
            <x-slot:actions>
                <a href="{{ route('subscribers.create') }}" class="rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white">Add subscriber</a>
            </x-slot:actions>
            <div class="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                @foreach ([['records-area', 'Area'], ['records-status', 'Status'], ['due-from', 'Due from'], ['due-to', 'Due to'], ['records-search', 'Search']] as [$id, $label])
                    <label for="{{ $id }}" class="grid gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        {{ $label }}
                        <input id="{{ $id }}" type="{{ str_starts_with($id, 'due-') ? 'date' : 'text' }}" value="" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-normal normal-case tracking-normal dark:border-slate-700 dark:bg-slate-950">
                    </label>
                @endforeach
            </div>
            <x-admin.empty-table :columns="['Account', 'Recurring dates', 'Details', 'Subscription', 'Service', 'Status', 'Balance', 'Remarks']" message="No subscriber records yet" />
            <p class="mt-4 text-sm text-slate-400">Pagination will appear when records are available.</p>
        </x-admin.panel>
    </div>
</x-layouts::app>
