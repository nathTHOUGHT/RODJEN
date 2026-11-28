<x-layouts::app :title="__('Subscriber details')">
    <x-admin.page-heading title="Subscriber details" section="Subscriptions">
        <x-slot:description>{{ $subscriber->fullName() }} &middot; {{ $subscriber->account_number }}</x-slot:description>
    </x-admin.page-heading>

    @php
        $actionClass =
            'rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium dark:border-slate-700 dark:bg-slate-900';
        $actions = [
            'Archive' => null,
            'Thermal' => 'billing-statement',
            'A4' => 'billing-statement',
            'A4 SOA' => 'billing-statement',
            'Transaction' => 'transaction',
            'Comment' => 'comment',
            'Mark as pulled out' => null,
            'Promise' => 'promise',
        ];
    @endphp

    <div x-data class="space-y-4">
        <div
            class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-100 p-3 dark:border-slate-800 dark:bg-slate-900/60 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex flex-nowrap gap-2 overflow-x-auto xl:shrink-0" role="group" aria-label="Subscriber actions">
                @foreach ($actions as $label => $dialog)
                    <button type="button" aria-label="{{ $label }}" title="{{ $label }}"
                        @if (in_array($label, ['Thermal', 'A4', 'A4 SOA'], true)) data-statement-format="{{ $label === 'A4 SOA' ? 'a4-soa' : strtolower($label) }}"
                            aria-haspopup="dialog"
                        @elseif ($dialog)
                            x-on:click="document.getElementById('subscriber-{{ $dialog }}-dialog').showModal()"
                            aria-haspopup="dialog"
                        @else
                            disabled @endif
                        class="grid size-9 shrink-0 place-items-center rounded-lg border border-slate-300 bg-white text-slate-700 hover:enabled:border-cyan-600 hover:enabled:text-cyan-700 focus-visible:outline-2 focus-visible:outline-cyan-600 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                            class="size-4">
                            @switch ($label)
                                @case('Archive')
                                    <path d="M3 4h18v4H3z" />
                                    <path d="M5 8v12h14V8M10 12h4" />
                                @break

                                @case('Thermal')
                                    <path
                                        d="M7 8V3h10v5M6 17H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2" />
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
                                    <path
                                        d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5 8.4 8.4 0 0 1-4-.99L3 21l1.99-5.5A8.4 8.4 0 0 1 4 11.5 8.5 8.5 0 0 1 12.5 3h.5a8.5 8.5 0 0 1 8 8z" />
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
                    <div title="{{ $label }}"
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
                                    <td
                                        class="max-w-sm break-words border-b border-slate-200 px-4 py-3 text-left dark:border-slate-800">
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

            <section aria-label="Balance and transaction summary"
                class="grid min-w-0 content-start items-stretch gap-4 xl:col-span-2 xl:grid-cols-[minmax(10rem,.75fr)_minmax(0,2.25fr)]">
                <div aria-label="Current balance"
                    class="relative flex h-full min-h-24 flex-col items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-white p-3 dark:border-slate-800 dark:bg-slate-900">
                    <span
                        class="absolute -left-7 top-3 w-24 -rotate-45 border-y border-slate-300 bg-slate-100 py-1 text-center text-[9px] font-semibold tracking-wider text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">BALANCE</span>
                    <p class="text-center font-medium text-slate-500 dark:text-slate-400">
                        <span class="text-xs">PHP</span>
                        <span class="text-2xl font-semibold text-slate-800 dark:text-slate-100">
                            {{ $subscriberData['additional']['Current balance'] ?? '—' }}
                        </span>
                    </p>
                    <p
                        class="mt-1 text-center text-[10px] font-medium tracking-wide text-slate-500 dark:text-slate-400">
                        ADVANCE CREDIT: <span class="text-slate-700 dark:text-slate-200">PHP —</span>
                    </p>
                </div>

                <div class="grid min-w-0 content-start gap-4">
                    <dl aria-label="Transaction summary"
                        class="grid min-w-0 auto-rows-fr grid-cols-2 gap-2 xl:grid-cols-4">
                        @foreach (['Total payment received', 'Total invoice created', 'Total rebates applied', 'Total discounts claimed'] as $label)
                            <div
                                class="h-full min-h-24 min-w-0 rounded-lg border border-slate-200 bg-white p-3 dark:border-slate-800 dark:bg-slate-900">
                                <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-x-2 gap-y-1">
                                    <dt
                                        class="text-[10px] font-medium uppercase leading-4 text-slate-500 dark:text-slate-400">
                                        {{ $label }}</dt>
                                    <dt
                                        class="text-right text-[10px] font-medium uppercase leading-4 text-slate-500 dark:text-slate-400">
                                        Total count</dt>
                                    <dd class="text-sm font-semibold text-slate-900 dark:text-white">PHP —</dd>
                                    <dd class="text-right text-sm font-semibold text-slate-900 dark:text-white">—</dd>
                                </div>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <x-admin.panel class="min-w-0 self-start xl:col-span-2">
                    <div class="-m-5 max-h-80 overflow-auto overscroll-contain scrollbar-hidden" data-table-scroll>
                        <table
                            class="w-full min-w-[48rem] table-fixed border-separate border-spacing-0 text-left text-sm">
                            <caption class="sr-only">Transaction list</caption>
                            <colgroup>
                                <col class="w-[26%]">
                                <col class="w-[15%]">
                                <col class="w-[10%]">
                                <col class="w-[10%]">
                                <col class="w-[20%]">
                                <col class="w-[19%]">
                            </colgroup>
                            <thead>
                                <tr>
                                    @foreach (['Plan / Service / Product', 'Account', 'Credit', 'Debit', 'Reference no.', 'Amount'] as $column)
                                        <th scope="col"
                                            class="sticky top-0 z-10 whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 text-left text-sm font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            {{ $column }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6"
                                        class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">
                                        No transactions yet
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </x-admin.panel>
            </section>
        </div>

        <details class="rounded-xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <summary
                class="cursor-pointer rounded-xl px-5 py-3 text-sm font-semibold text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-cyan-600 dark:text-white">
                Additional details</summary>
            <div class="grid gap-4 p-4 md:grid-cols-2">
                @foreach ([
        'Service information' => ['Service state', 'Subscription'],
        'Network information' => ['External URL', 'Image URL', 'OLT device', 'PON port', 'NAP device', 'NAP port', 'ONU name', 'Network remarks'],
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

        <div id="subscriber-billing-statement-print" class="hidden" data-billing-format="thermal"
            data-billing-day="{{ $subscriber->subscription?->billing_day ?? '' }}"
            data-bill-generation-days="{{ $subscriber->subscription?->bill_generation_days ?? 7 }}">
            @include('admin.subscribers.billing-statement')
            @include('admin.subscribers.billing-statement-a4')
            @include('admin.subscribers.account-statement-a4')
        </div>

        <style id="subscriber-billing-page-style">
            @page {
                size: 48mm 210mm;
                margin: 0;
            }
        </style>

        <script>
            (() => {
                const monthSelect = document.getElementById('subscriber-billing-month');
                const printStatement = document.getElementById('subscriber-billing-statement-print');
                const dialog = document.getElementById('subscriber-billing-statement-dialog');
                const preview = document.getElementById('subscriber-billing-statement-preview');
                const pageStyle = document.getElementById('subscriber-billing-page-style');
                const monthFilter = document.querySelector('[data-billing-month-filter]');
                const dialogTitle = document.getElementById('subscriber-billing-statement-title');
                const printButton = document.querySelector('[data-print-billing-statement]');

                if (!monthSelect || !printStatement || !dialog || !preview || !pageStyle || !monthFilter || !dialogTitle ||
                    !printButton) {
                    throw new Error('Billing statement print controls could not be found.');
                }

                // Keep the dashboard's layout out of the printed document.
                document.body.append(printStatement);

                const setBillingFormat = (format) => {
                    if (!['thermal', 'a4', 'a4-soa'].includes(format)) {
                        throw new Error('Select a valid print format.');
                    }

                    const isA4 = format !== 'thermal';

                    printStatement.dataset.billingFormat = format;
                    dialog.dataset.billingFormat = format;
                    preview.dataset.billingFormat = format;
                    preview.tabIndex = isA4 ? 0 : -1;
                    monthFilter.classList.toggle('hidden', format === 'a4-soa');
                    const title = format === 'a4-soa' ?
                        'Print A4 statement of account' :
                        format === 'a4' ?
                        'Print A4 billing statement' :
                        'Print thermal billing statement';
                    dialogTitle.textContent = title;
                    printButton.setAttribute('aria-label', title);
                    printButton.setAttribute('title', title);
                    pageStyle.textContent = isA4 ?
                        '@page { size: A4; margin: 12mm; }' :
                        '@page { size: 48mm 210mm; margin: 0; }';
                };

                const formatDate = (date) => {
                    const monthName = new Intl.DateTimeFormat('en-US', {
                        month: 'short',
                        timeZone: 'UTC',
                    }).format(date);
                    const day = String(date.getUTCDate()).padStart(2, '0');

                    return `${date.getUTCFullYear()}-${monthName}-${day}`;
                };

                const updateStatementDates = () => {
                    const [year, month] = monthSelect.value.split('-').map(Number);
                    if (!Number.isInteger(year) || !Number.isInteger(month) || month < 1 || month > 12) {
                        throw new Error('Select a valid billing month before printing.');
                    }

                    const billingDay = Number(printStatement.dataset.billingDay);
                    const generationDays = Number(printStatement.dataset.billGenerationDays);
                    let billingDate = 'Not configured';
                    let dueDate = 'Not configured';
                    let billingPeriod;

                    if (Number.isInteger(billingDay) && billingDay >= 1 && billingDay <= 31) {
                        if (!Number.isInteger(generationDays) || generationDays < 0) {
                            throw new Error('The bill generation schedule is invalid.');
                        }

                        const lastDayOfMonth = new Date(Date.UTC(year, month, 0)).getUTCDate();
                        const due = new Date(Date.UTC(year, month - 1, Math.min(billingDay, lastDayOfMonth)));
                        const bill = new Date(due);
                        bill.setUTCDate(bill.getUTCDate() - generationDays);
                        const periodStart = new Date(Date.UTC(
                            bill.getUTCFullYear(),
                            bill.getUTCMonth() - 1,
                            bill.getUTCDate() + 1,
                        ));

                        billingDate = formatDate(bill);
                        dueDate = formatDate(due);
                        billingPeriod = `From ${formatDate(periodStart)} to ${formatDate(bill)}`;
                    } else {
                        const periodStart = new Date(Date.UTC(year, month - 1, 1));
                        const periodEnd = new Date(Date.UTC(year, month, 0));
                        billingPeriod = `From ${formatDate(periodStart)} to ${formatDate(periodEnd)}`;
                    }

                    document.querySelectorAll('[data-print-billing-date]').forEach((element) => {
                        element.textContent = billingDate;
                    });
                    document.querySelectorAll('[data-print-billing-period]').forEach((element) => {
                        element.textContent = billingPeriod;
                    });
                    document.querySelectorAll('[data-print-due-date]').forEach((element) => {
                        element.textContent = dueDate;
                    });
                };

                monthSelect.addEventListener('change', updateStatementDates);
                updateStatementDates();

                document.querySelectorAll('[data-statement-format]').forEach((button) => {
                    button.addEventListener('click', () => {
                        setBillingFormat(button.dataset.statementFormat);
                        dialog.showModal();
                        preview.scrollLeft = 0;
                    });
                });

                printButton.addEventListener('click', () => {
                    updateStatementDates();
                    setBillingFormat(dialog.dataset.billingFormat);
                    dialog.close();
                    window.print();
                });
            })();
        </script>

        <style>
            #subscriber-billing-statement-dialog:is([data-billing-format="a4"],
            [data-billing-format="a4-soa"]) {
                max-width: 50rem;
            }

            #subscriber-billing-statement-preview:is([data-billing-format="a4"],
            [data-billing-format="a4-soa"]) {
                overflow-x: auto;
            }

            #subscriber-billing-statement-preview :is([data-billing-layout="a4"], [data-billing-layout="a4-soa"]) {
                width: 186mm;
                min-width: 36rem;
            }

            #subscriber-billing-statement-preview [data-billing-layout] {
                display: none;
            }

            #subscriber-billing-statement-preview[data-billing-format="thermal"] [data-billing-layout="thermal"],
            #subscriber-billing-statement-preview[data-billing-format="a4"] [data-billing-layout="a4"],
            #subscriber-billing-statement-preview[data-billing-format="a4-soa"] [data-billing-layout="a4-soa"] {
                display: block;
            }

            @media print {
                body:has(> #subscriber-billing-statement-print) {
                    min-height: 0;
                    background: #fff;
                }

                body:has(> #subscriber-billing-statement-print)> :not(#subscriber-billing-statement-print) {
                    display: none !important;
                }

                #subscriber-billing-statement-print [data-billing-layout] {
                    display: none !important;
                }

                #subscriber-billing-statement-print[data-billing-format="thermal"] [data-billing-layout="thermal"],
                #subscriber-billing-statement-print[data-billing-format="a4"] [data-billing-layout="a4"],
                #subscriber-billing-statement-print[data-billing-format="a4-soa"] [data-billing-layout="a4-soa"] {
                    display: block !important;
                }

                #subscriber-billing-statement-print {
                    display: block !important;
                    width: 100%;
                    max-width: none;
                    color: #000 !important;
                    background: #fff !important;
                }

                #subscriber-billing-statement-print[data-billing-format="thermal"] article {
                    width: 100% !important;
                    max-width: none !important;
                    padding: 2mm !important;
                    box-sizing: border-box;
                    border: 0 !important;
                    border-radius: 0 !important;
                    box-shadow: none !important;
                    font-size: 10pt !important;
                    line-height: 1.35 !important;
                    color: #000 !important;
                    background: #fff !important;
                }

                #subscriber-billing-statement-print[data-billing-format="thermal"] article p {
                    font-size: 10pt !important;
                    line-height: 1.35 !important;
                }

                #subscriber-billing-statement-print[data-billing-format="thermal"] article header p,
                #subscriber-billing-statement-print[data-billing-format="thermal"] article footer p {
                    font-size: 8pt !important;
                    line-height: 1.25 !important;
                }

                #subscriber-billing-statement-print[data-billing-format="thermal"] article h1,
                #subscriber-billing-statement-print[data-billing-format="thermal"] article section h2 {
                    font-size: 11pt !important;
                    line-height: 1.25 !important;
                }

                #subscriber-billing-statement-print[data-billing-format="thermal"] article [data-receipt-name] {
                    font-size: 12pt !important;
                    line-height: 1.25 !important;
                }

                #subscriber-billing-statement-print[data-billing-format="thermal"] article [data-receipt-total] {
                    font-size: 16pt !important;
                    line-height: 1.2 !important;
                }

                #subscriber-billing-statement-print[data-billing-format="a4"] article,
                #subscriber-billing-statement-print[data-billing-format="a4-soa"] article {
                    width: 100% !important;
                    max-width: none !important;
                    padding: 0 !important;
                    border: 0 !important;
                    border-radius: 0 !important;
                    box-shadow: none !important;
                    background: #fff !important;
                }

                #subscriber-billing-statement-print [data-billing-layout="a4"] {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                #subscriber-billing-statement-print [data-billing-layout="a4-soa"] {
                    color: #000 !important;
                }

                #subscriber-billing-statement-print[data-billing-format="a4-soa"] .a4-soa-section-title {
                    border-bottom: 1px solid #000 !important;
                    color: #000 !important;
                    background: #fff !important;
                }

                #subscriber-billing-statement-print[data-billing-format="a4-soa"] table {
                    width: 100% !important;
                    table-layout: auto !important;
                }

                #subscriber-billing-statement-print[data-billing-format="a4-soa"] thead {
                    display: table-header-group;
                }

                #subscriber-billing-statement-print[data-billing-format="a4-soa"] tr {
                    break-inside: avoid;
                }

                #subscriber-billing-statement-print img {
                    display: block;
                    height: auto;
                    max-width: 100%;
                    object-fit: contain;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }
        </style>
    </div>
</x-layouts::app>
