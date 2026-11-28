<x-layouts::app :title="__('Subscriber details')">
    <x-admin.page-heading title="Subscriber details" section="Subscriptions">
        <x-slot:description>{{ $subscriber->fullName() }} &middot; {{ $subscriber->account_number }}</x-slot:description>
    </x-admin.page-heading>

    @php
        $actionClass =
            'rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium dark:border-slate-700 dark:bg-slate-900';
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
        @if (session('status'))
            <div role="status"
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-100 p-3 dark:border-slate-800 dark:bg-slate-900/60 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex flex-nowrap gap-2 overflow-x-auto xl:shrink-0" role="group" aria-label="Subscriber actions">
                    @foreach ($actions as $label => $dialog)
                        <button type="button" aria-label="{{ $label }}" title="{{ $label }}"
                            @if ($dialog) x-on:click="document.getElementById('subscriber-{{ $dialog }}-dialog').showModal()"
                                aria-haspopup="dialog"
                            @else
                                disabled @endif
                            class="grid size-9 shrink-0 place-items-center rounded-lg border border-slate-300 bg-white text-slate-700 hover:enabled:border-cyan-600 hover:enabled:text-cyan-700 focus-visible:outline-2 focus-visible:outline-cyan-600 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true" class="size-4">
                                @switch ($label)
                                    @case('Archive')
                                        <path d="M3 4h18v4H3z" />
                                        <path d="M5 8v12h14V8M10 12h4" />
                                    @break
                                    @case('Thermal')
                                        <path d="M7 8V3h10v5M6 17H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2" />
                                        <path d="M7 14h10v7H7zM9 17h6M18 12h.01" />
                                    @break
                                    @case('A4')
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <path d="M14 2v6h6M8 13h8M8 17h8" />
                                    @break
                                    @case('A4 SOA')
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <path d="M14 2v6h6M8 12h8M8 16h8M8 20h8M11 12v8" />
                                    @break
                                    @case('Transaction')
                                        <path d="M4 3v18l3-2 3 2 3-2 3 2 4-2V3l-4 2-3-2-3 2-3-2z" />
                                        <path d="M8 9h8M8 13h8M8 17h4" />
                                    @break
                                    @case('Comment')
                                        <path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5 8.4 8.4 0 0 1-4-.99L3 21l1.99-5.5A8.4 8.4 0 0 1 4 11.5 8.5 8.5 0 0 1 12.5 3h.5a8.5 8.5 0 0 1 8 8z" />
                                    @break
                                    @case('Mark as pulled out')
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M8 12h8" />
                                    @break
                                    @case('Promise')
                                        <rect x="3" y="5" width="18" height="16" rx="2" />
                                        <path d="M16 3v4M8 3v4M3 10h18M9 15l2 2 4-4" />
                                    @break
                                @endswitch
                            </svg>
                            <span class="sr-only">{{ $label }}</span>
                        </button>
                    @endforeach
            </div>

            <dl class="grid min-w-0 grid-cols-2 gap-2 sm:grid-cols-3 xl:ml-auto xl:w-fit xl:flex-none xl:grid-cols-5">
                @foreach ([
                    'Auto cut count' => 'Auto-cut',
                    'Reactivate count' => 'Reactivated',
                    'Pulled out count' => 'Pulled out',
                    'Reactivate from pullout count' => 'Post-pullout',
                    'Promise count' => 'Promised',
                ] as $label => $shortLabel)
                    <div
                        title="{{ $label }}"
                        class="flex flex-col items-center justify-center rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-center dark:border-slate-800 dark:bg-slate-950 xl:w-24">
                        <dt class="text-[10px] leading-3.5 font-medium text-slate-600 dark:text-slate-400">
                            <span aria-hidden="true">{{ $shortLabel }}</span>
                            <span class="sr-only">{{ $label }}</span>
                        </dt>
                        <dd class="mt-0.5 text-base leading-5 font-semibold text-slate-900 dark:text-white"><span
                                aria-hidden="true">&mdash;</span><span class="sr-only">Not available</span></dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,.85fr)_minmax(0,1.45fr)]">
            @include('admin.subscribers.detail-panel', [
                'title' => 'Personal Details',
                'fields' => [
                    'Account number',
                    'Name',
                    'Landmark',
                    'Address',
                    'Contact number',
                    'Email',
                    'Coordinates',
                    'Remarks',
                    'External URL',
                ],
                'values' => $subscriberData['personal'],
                'editable' => true,
                'editDialog' => 'subscriber-personal-edit-dialog',
                'valueAlignment' => 'text-left',
                'rowLayout' => 'grid grid-cols-[minmax(8rem,max-content)_minmax(0,1fr)] items-baseline gap-3',
            ])

            @include('admin.subscribers.detail-panel', [
                'title' => 'Service Details',
                'fields' => [
                    'Plan',
                    'MRC',
                    'Installation date',
                    'Bill generation',
                    'Due date',
                    'Billing status',
                    'Next due date',
                    'Billing period',
                    'Last due date',
                    'Promise date',
                ],
                'values' => $subscriberData['service'],
                'editable' => true,
                'editDialog' => 'subscriber-service-edit-dialog',
                'valueAlignment' => 'text-left',
                'rowLayout' => 'grid grid-cols-[minmax(8rem,max-content)_minmax(0,1fr)] items-baseline gap-3',
            ])

            <x-admin.panel class="min-w-0 self-start">
                <div class="-m-5 max-h-80 overflow-auto overscroll-contain scrollbar-hidden" data-table-scroll>
                    <table class="w-full min-w-[34rem] table-fixed border-separate border-spacing-0 text-left text-sm">
                        <colgroup>
                            <col class="w-1/2">
                            <col class="w-[30%]">
                            <col class="w-1/5">
                        </colgroup>
                        <thead>
                            <tr>
                                @foreach (['Comment', 'Timestamp (Manila time)', 'Commenter'] as $column)
                                    <th scope="col"
                                        class="sticky top-0 z-10 whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 text-left text-sm font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $column }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subscriber->comments as $comment)
                                <tr>
                                    <td class="max-w-sm break-words border-b border-slate-200 px-4 py-3 text-left dark:border-slate-800">
                                        <span class="block whitespace-pre-wrap">{{ $comment->comment }}</span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap border-b border-slate-200 px-4 py-3 text-left dark:border-slate-800">
                                        {{ $comment->created_at->timezone('Asia/Manila')->format('M j, Y g:i A') }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap border-b border-slate-200 px-4 py-3 text-left dark:border-slate-800">
                                        {{ $comment->commenter_name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"
                                        class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">
                                        No comments yet
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-admin.panel>

            <div class="grid min-w-0 content-start gap-4">
                @include('admin.subscribers.detail-panel', [
                    'title' => 'MikroTik Details',
                    'fields' => ['Name', 'Password', 'Profile', 'Comment'],
                    'values' => $subscriberData['mikrotik'],
                    'editable' => true,
                    'editDialog' => 'subscriber-mikrotik-edit-dialog',
                    'valueAlignment' => 'text-left',
                    'rowLayout' => 'grid grid-cols-[minmax(8rem,max-content)_minmax(0,1fr)] items-baseline gap-3',
                ])

                @include('admin.subscribers.detail-panel', [
                    'title' => 'MikroTik parameters',
                    'fields' => ['IP address', 'Caller ID', 'MAC address', 'Uptime', 'Last logout'],
                    'values' => $subscriberData['parameters'],
                    'editable' => false,
                    'valueAlignment' => 'text-left',
                    'rowLayout' => 'grid grid-cols-[minmax(8rem,max-content)_minmax(0,1fr)] items-baseline gap-3',
                ])
            </div>

            <div class="grid min-w-0 content-start gap-4 xl:col-span-2">
                <div class="flex flex-wrap gap-2 rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-800 dark:bg-slate-900"
                    role="group" aria-label="Transaction categories">
                    @foreach (['Credit', 'Debit', 'Rebate', 'Discount'] as $label)
                        <button type="button" disabled
                            class="{{ $actionClass }} flex-1 disabled:cursor-not-allowed disabled:opacity-60">{{ $label }}</button>
                    @endforeach
                </div>
                <x-admin.panel title="Transaction list" class="min-w-0">
                    <x-admin.empty-table :columns="['Plan / Service / Product', 'Account', 'Credit', 'Debit', 'Reference no.', 'Amount']" message="No transactions yet" />
                </x-admin.panel>
            </div>
        </div>

        <details class="rounded-xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <summary
                class="cursor-pointer rounded-xl px-5 py-3 text-sm font-semibold text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-cyan-600 dark:text-white">
                Additional details</summary>
            <div class="grid gap-4 p-4 md:grid-cols-2">
                @foreach ([
        'Service information' => ['Service state', 'Subscription'],
        'Network information' => ['External URL', 'Image URL', 'OLT device', 'PON port', 'NAP device', 'NAP port', 'ONU name', 'Network remarks'],
        'Transaction summary' => ['Current balance'],
    ] as $title => $fields)
                    @include('admin.subscribers.detail-panel', [
                        'title' => $title,
                        'fields' => $fields,
                        'values' => $subscriberData['additional'],
                        'editable' => false,
                    ])
                @endforeach

                @foreach (['System logs' => 'No system logs yet', 'Activity logs' => 'No activity logs yet'] as $title => $message)
                    <x-admin.panel :title="$title" class="min-w-0">
                        <x-admin.empty-table :columns="['Timestamp', 'Description']" :message="$message" />
                    </x-admin.panel>
                @endforeach

                <x-admin.panel title="Account actions" class="md:col-span-2">
                    <div class="flex flex-wrap gap-2">
                        @foreach (['Message', 'Support ticket', 'Job order'] as $action)
                            <button type="button" disabled
                                class="{{ $actionClass }} disabled:opacity-60">{{ $action }}</button>
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
                                            <input name="{{ $name }}"
                                                type="{{ $name === 'service_password' ? 'password' : (str_ends_with($name, '_date') ? 'date' : 'text') }}"
                                                value="" disabled
                                                class="min-w-0 rounded-md border border-slate-300 px-3 py-2 dark:border-slate-700 dark:bg-slate-950">
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            <button type="button" disabled data-test="subscriber-{{ $key }}-disabled"
                                class="{{ $actionClass }} mt-4 disabled:opacity-60">{{ $action }}</button>
                        </details>
                    @endforeach
                </x-admin.panel>
            </div>
        </details>

        @include('admin.subscribers.detail-dialogs')
    </div>
</x-layouts::app>
