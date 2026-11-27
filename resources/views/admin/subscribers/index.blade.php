<x-layouts::app :title="__('Subscriber records')">
    <x-admin.page-heading title="Subscriber records" section="Subscriptions" />

    <div class="space-y-6">
        @if (session('status'))
            <div role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <x-admin.panel title="Records">
            <x-slot:actions>
                <a href="{{ route('subscribers.create') }}" class="rounded-md bg-cyan-700 px-3 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Add subscriber</a>
            </x-slot:actions>

            <form method="GET" action="{{ route('subscribers.index') }}" class="mb-5 grid items-end gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1.5fr)_auto]">
                <label for="records-area" class="grid gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Area
                    <input id="records-area" name="area" type="text" value="{{ $area }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-normal normal-case tracking-normal dark:border-slate-700 dark:bg-slate-950">
                </label>

                <label for="records-status" class="grid gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Status
                    <select id="records-status" name="status" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-normal normal-case tracking-normal dark:border-slate-700 dark:bg-slate-950">
                        <option value="">All statuses</option>
                        @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'] as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label for="records-search" class="grid gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Search
                    <input id="records-search" name="q" type="search" value="{{ $search }}" placeholder="Account, name, contact, or plan" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-normal normal-case tracking-normal dark:border-slate-700 dark:bg-slate-950">
                </label>

                <div class="flex gap-2">
                    <button type="submit" class="rounded-md bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Filter</button>
                    @if ($area !== '' || $status !== '' || $search !== '')
                        <a href="{{ route('subscribers.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:border-cyan-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">Clear</a>
                    @endif
                </div>
            </form>

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
                        @forelse ($subscribers as $subscriber)
                            @php
                                $subscription = $subscriber->subscription;
                                $status = $subscription?->status;
                                $statusLabel = $status !== null && $status !== ''
                                    ? str($status)->headline()
                                    : __('Not available');
                                $statusBadge = match ($status) {
                                    'active' => 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
                                    'suspended' => 'bg-amber-50 text-amber-800 dark:bg-amber-950 dark:text-amber-300',
                                    'inactive' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                    default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                };
                                $statusDot = match ($status) {
                                    'active' => 'bg-emerald-700 dark:bg-emerald-400',
                                    'suspended' => 'bg-amber-700 dark:bg-amber-400',
                                    'inactive' => 'bg-slate-500 dark:bg-slate-400',
                                    default => 'bg-slate-500 dark:bg-slate-400',
                                };
                                $planName = $subscription?->plan?->name;
                            @endphp
                            <tr>
                                <th scope="row" class="whitespace-nowrap px-4 py-4 font-semibold">
                                    <a href="{{ route('subscribers.details', $subscriber) }}" class="text-cyan-700 underline underline-offset-4 dark:text-cyan-300">{{ $subscriber->account_number }}</a>
                                </th>
                                <td class="whitespace-nowrap px-4 py-4">
                                    @if ($subscription?->billing_day)
                                        Day {{ $subscription->billing_day }} of each month
                                    @else
                                        <span aria-hidden="true">&mdash;</span><span class="sr-only">Not available</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    <a href="{{ route('subscribers.details', $subscriber) }}" class="font-semibold text-cyan-700 underline underline-offset-4 dark:text-cyan-300">{{ $subscriber->fullName() }}</a>
                                    <span class="block text-xs text-slate-500 dark:text-slate-400">{{ $subscriber->area !== null && $subscriber->area !== '' ? $subscriber->area : __('Area not specified') }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">{{ $planName !== null && $planName !== '' ? $planName : __('Not assigned') }}</td>
                                <td class="whitespace-nowrap px-4 py-4">{{ $subscription?->service_type ? str($subscription->service_type)->headline() : __('Not available') }}</td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    <span @class(['inline-flex items-center gap-1.5 rounded-full px-2 py-1 text-xs font-semibold', $statusBadge])>
                                        <span aria-hidden="true" @class(['size-1.5 rounded-full', $statusDot])></span>
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4"><span aria-hidden="true">&mdash;</span><span class="sr-only">Not available</span></td>
                                <td class="max-w-64 px-4 py-4">{{ $subscriber->remarks !== null && $subscriber->remarks !== '' ? $subscriber->remarks : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">No subscriber records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($subscribers->hasPages())
                <div class="mt-5">{{ $subscribers->links() }}</div>
            @endif
        </x-admin.panel>
    </div>
</x-layouts::app>
