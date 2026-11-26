<x-layouts::app :title="__('Subscriber records')">
    <x-admin.page-heading title="Subscriber records" section="Subscriptions" />

    <div class="space-y-6">
        <x-admin.frontend-notice />
        <x-admin.panel title="Records">
            <x-slot:actions>
                <a href="{{ route('subscribers.create') }}" class="rounded-md bg-cyan-700 px-3 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Add subscriber</a>
            </x-slot:actions>
            <div class="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                @foreach ([['records-area', 'Area'], ['records-status', 'Status'], ['due-from', 'Due from'], ['due-to', 'Due to'], ['records-search', 'Search']] as [$id, $label])
                    <label for="{{ $id }}" class="grid gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        {{ $label }}
                        <input id="{{ $id }}" type="{{ str_starts_with($id, 'due-') ? 'date' : 'text' }}" value="" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-normal normal-case tracking-normal dark:border-slate-700 dark:bg-slate-950">
                    </label>
                @endforeach
            </div>
            <div class="overflow-x-auto" data-table-scroll>
                <table class="min-w-full border-separate border-spacing-0 text-left text-sm">
                    <thead>
                        <tr>
                            @foreach (['Account', 'Recurring dates', 'Details', 'Subscription', 'Service', 'Status', 'Balance', 'Remarks'] as $column)
                                <th scope="col" class="whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 font-semibold dark:border-slate-700 dark:bg-slate-800">{{ $column }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row" class="whitespace-nowrap px-4 py-4 font-semibold">
                                <a href="{{ route('subscribers.details') }}" class="text-cyan-700 underline underline-offset-4 dark:text-cyan-300">{{ $sampleSubscriber['personal']['Account number'] }}</a>
                            </th>
                            <td class="whitespace-nowrap px-4 py-4">15th of each month</td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <a href="{{ route('subscribers.details') }}" class="font-semibold text-cyan-700 underline underline-offset-4 dark:text-cyan-300">{{ $sampleSubscriber['personal']['Name'] }}</a>
                                <span class="block text-xs text-slate-500 dark:text-slate-400">Sample subscriber</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">{{ $sampleSubscriber['service']['Plan'] }}</td>
                            <td class="whitespace-nowrap px-4 py-4">Fiber</td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                    <span aria-hidden="true" class="size-1.5 rounded-full bg-emerald-700 dark:bg-brand-green"></span>
                                    {{ $sampleSubscriber['status'] }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">{{ $sampleSubscriber['balance'] }}</td>
                            <td class="whitespace-nowrap px-4 py-4">{{ $sampleSubscriber['personal']['Remarks'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-admin.panel>
    </div>
</x-layouts::app>
