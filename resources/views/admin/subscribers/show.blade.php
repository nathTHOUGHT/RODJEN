<x-layouts::app :title="__('Subscriber details')">
    <x-admin.page-heading title="Subscriber details" section="Subscriptions">
        <x-slot:description>Sample subscriber · {{ $sampleSubscriber['personal']['Name'] }} · {{ $sampleSubscriber['personal']['Account number'] }}</x-slot:description>
    </x-admin.page-heading>

    @php
        $actionClass = 'rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium dark:border-slate-700 dark:bg-slate-900';
        $actions = [
            'Archive' => null,
            'Thermal' => null,
            'A4' => null,
            'A4 SOA' => null,
            'Transaction' => 'transaction',
            'Comment' => 'comment',
            'Mark as pulled out' => null,
            'Promise' => 'promise',
        ];
    @endphp

    <div x-data class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-slate-100 p-3 dark:border-slate-800 dark:bg-slate-900/60">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap gap-2" role="group" aria-label="Subscriber actions">
                    @foreach ($actions as $label => $dialog)
                        <button
                            type="button"
                            @if ($dialog)
                                x-on:click="document.getElementById('subscriber-{{ $dialog }}-dialog').showModal()"
                                aria-haspopup="dialog"
                            @else
                                disabled
                            @endif
                            class="{{ $actionClass }} hover:enabled:border-cyan-600 disabled:cursor-not-allowed disabled:opacity-60"
                        >{{ $label }}</button>
                    @endforeach
                </div>
                <a href="{{ route('subscribers.index') }}" class="{{ $actionClass }} hover:border-cyan-600">Return to records</a>
            </div>

            <dl class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3 xl:grid-cols-5">
                @foreach (['Auto cut count', 'Reactivate count', 'Pulled out count', 'Reactivate from pullout count', 'Promise count'] as $label)
                    <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 dark:border-slate-800 dark:bg-slate-950">
                        <dt class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ $label }}</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-slate-900 dark:text-white"><span aria-hidden="true">&mdash;</span><span class="sr-only">Not available</span></dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,.85fr)_minmax(0,1.45fr)]">
            @include('admin.subscribers.detail-panel', [
                'title' => 'Personal Details',
                'fields' => ['Account number', 'Name', 'Landmark', 'Address', 'Contact number', 'Email', 'Coordinates', 'Remarks'],
                'values' => $sampleSubscriber['personal'],
                'editable' => true,
            ])

            @include('admin.subscribers.detail-panel', [
                'title' => 'Service Details',
                'fields' => ['Plan', 'MRC', 'Discount', 'Installation date', 'Due date', 'Billing status', 'Next due date', 'Billing period', 'Last due date', 'Promise date'],
                'values' => $sampleSubscriber['service'],
                'editable' => true,
            ])

            <x-admin.panel title="Comment list" class="min-w-0">
                <x-admin.empty-table :columns="['Comment', 'Timestamp', 'Commenter']" message="No comments yet" />
            </x-admin.panel>

            <div class="grid min-w-0 content-start gap-4">
                @include('admin.subscribers.detail-panel', [
                    'title' => 'MikroTik Details',
                    'fields' => ['Name', 'Password', 'Profile', 'Comment'],
                    'values' => $sampleSubscriber['mikrotik'],
                    'editable' => true,
                ])

                @include('admin.subscribers.detail-panel', [
                    'title' => 'MikroTik parameters',
                    'fields' => ['IP address', 'Caller ID', 'Uptime', 'Last logout'],
                    'values' => $sampleSubscriber['parameters'],
                    'editable' => false,
                ])
            </div>

            <div class="grid min-w-0 content-start gap-4 xl:col-span-2">
                <div class="flex flex-wrap gap-2 rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-800 dark:bg-slate-900" role="group" aria-label="Transaction categories">
                    @foreach (['Credit', 'Debit', 'Rebate', 'Discount'] as $label)
                        <button type="button" disabled class="{{ $actionClass }} flex-1 disabled:cursor-not-allowed disabled:opacity-60">{{ $label }}</button>
                    @endforeach
                </div>
                <x-admin.panel title="Transaction list" class="min-w-0">
                    <x-admin.empty-table :columns="['Plan / Service / Product', 'Account', 'Credit', 'Debit', 'Reference no.', 'Amount']" message="No transactions yet" />
                </x-admin.panel>
            </div>
        </div>

        <details class="rounded-xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <summary class="cursor-pointer rounded-xl px-5 py-3 text-sm font-semibold text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-cyan-600 dark:text-white">Additional details</summary>
            <div class="grid gap-4 p-4 md:grid-cols-2">
                @foreach ([
                    'Service information' => ['Service state', 'Subscription'],
                    'Network information' => ['Coverage map', 'Customer location', 'Network path', 'PON details'],
                    'Transaction summary' => ['Current balance'],
                ] as $title => $fields)
                    @include('admin.subscribers.detail-panel', ['title' => $title, 'fields' => $fields, 'editable' => false])
                @endforeach

                @foreach (['System logs' => 'No system logs yet', 'Activity logs' => 'No activity logs yet'] as $title => $message)
                    <x-admin.panel :title="$title" class="min-w-0">
                        <x-admin.empty-table :columns="['Timestamp', 'Description']" :message="$message" />
                    </x-admin.panel>
                @endforeach

                <x-admin.panel title="Account actions" class="md:col-span-2">
                    <div class="flex flex-wrap gap-2">
                        @foreach (['Message', 'Support ticket', 'Job order'] as $action)
                            <button type="button" disabled class="{{ $actionClass }} disabled:opacity-60">{{ $action }}</button>
                        @endforeach
                    </div>
                    @foreach ([
                        'remove' => ['Remove account', [], 'Confirm'],
                        'settings' => ['Account settings', ['service_type' => 'Service type', 'service_username' => 'Username', 'service_password' => 'Password', 'subscription' => 'Subscription', 'local_address' => 'Local address', 'remote_address' => 'Remote address', 'caller_id' => 'Caller ID', 'credit_limit' => 'Credit limit'], 'Update'],
                        'billing' => ['Deactivate billing', [], 'Confirm'],
                        'override' => ['Override date', ['override_date' => 'Override date'], 'Update'],
                    ] as $key => [$title, $fields, $action])
                        <details class="mt-4 rounded-lg border border-slate-200 p-4 dark:border-slate-700">
                            <summary class="cursor-pointer text-sm font-semibold">{{ $title }}</summary>
                            @if ($fields)
                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    @foreach ($fields as $name => $label)
                                        <label class="grid gap-1 text-sm">
                                            {{ $label }}
                                            <input name="{{ $name }}" type="{{ $name === 'service_password' ? 'password' : (str_ends_with($name, '_date') ? 'date' : 'text') }}" value="" disabled class="min-w-0 rounded-md border border-slate-300 px-3 py-2 dark:border-slate-700 dark:bg-slate-950">
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            <button type="button" disabled data-test="subscriber-{{ $key }}-disabled" class="{{ $actionClass }} mt-4 disabled:opacity-60">{{ $action }}</button>
                        </details>
                    @endforeach
                </x-admin.panel>
            </div>
        </details>

        @include('admin.subscribers.detail-dialogs')
    </div>
</x-layouts::app>
