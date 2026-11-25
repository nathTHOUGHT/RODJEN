<x-layouts::app :title="__('Add subscriber')">
    @php
        $personalRows = [
            [
                ['account-number', 'Account number', 'text'],
                ['first-name', 'First name', 'text'],
                ['last-name', 'Last name', 'text'],
            ],
            [['landmark', 'Landmark', 'text']],
            [['billing-address', 'Billing address', 'text']],
            [
                ['contact-number', 'Contact number', 'tel'],
                ['email-address', 'Email address', 'email'],
            ],
            [['coordinates', 'Coordinates', 'text']],
            [['remarks', 'Remarks', 'text']],
        ];
        $subscriptionFields = [
            ['installation-date', 'Installation date', 'date'],
            ['plan', 'Plan', 'text'],
            ['bill-generation', 'Bill generation', 'text'],
        ];
        $mikrotikFields = [
            ['pppoe-name', 'PPPoE name', 'text'],
            ['pppoe-password', 'PPPoE password', 'password'],
            ['pppoe-profile', 'PPPoE profile', 'text'],
            ['pppoe-comment', 'PPPoE comment', 'text'],
        ];
        $additionalFields = [
            ['area', 'Area', 'text'],
            ['subscription-date', 'Subscription date', 'date'],
            ['credit-limit', 'Credit limit', 'number'],
            ['external-url', 'External URL', 'url'],
            ['image-url', 'Image URL', 'url'],
            ['olt-device', 'OLT device', 'text'],
            ['pon-port', 'PON port', 'text'],
            ['nap-device', 'NAP device', 'text'],
            ['nap-port', 'NAP port', 'text'],
            ['onu-name', 'ONU name', 'text'],
            ['caller-id', 'Caller ID or PPPoE identifier', 'text'],
            ['mac-address', 'MAC address', 'text'],
            ['network-remarks', 'Network remarks', 'text'],
        ];
    @endphp

    <x-admin.page-heading title="Add subscriber" section="Subscriptions" />

    <div class="space-y-4" data-ui-only-form>
        <x-admin.frontend-notice />
        <div class="grid gap-4 xl:grid-cols-2">
            <x-admin.panel title="Personal Details">
                <div class="space-y-3">
                    @foreach ($personalRows as $fields)
                        <div @class(['grid gap-3', 'sm:grid-cols-2' => count($fields) === 2, 'sm:grid-cols-3' => count($fields) === 3])>
                            @include('admin.subscribers.fields')
                        </div>
                    @endforeach
                </div>
            </x-admin.panel>

            <div class="min-w-0 space-y-4">
                <x-admin.panel title="Subscription Details">
                    <div class="grid gap-3">
                        @include('admin.subscribers.fields', ['fields' => $subscriptionFields])
                    </div>
                </x-admin.panel>

                <x-admin.panel title="MikroTik Details">
                    <div class="grid gap-3">
                        @include('admin.subscribers.fields', ['fields' => $mikrotikFields])
                    </div>
                </x-admin.panel>

                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('subscribers.index') }}" class="min-w-28 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-center text-sm font-medium hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800">Cancel</a>
                    <button type="button" disabled data-test="subscriber-create-disabled" class="min-w-28 cursor-not-allowed rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white opacity-50">Submit</button>
                </div>
            </div>
        </div>

        <details class="rounded-xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <summary class="cursor-pointer rounded-xl px-5 py-3 text-sm font-semibold text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-cyan-600 dark:text-white">Additional details</summary>
            <div class="grid gap-3 px-5 pb-5 sm:grid-cols-2 xl:grid-cols-3">
                @include('admin.subscribers.fields', ['fields' => $additionalFields])
            </div>
        </details>
    </div>
</x-layouts::app>
