<x-layouts::app :title="__('Add subscriber')">
    @php
        $personalDetails = [
            [
                ['account_number', 'account-number', 'Account number', 'text'],
                ['first_name', 'first-name', 'First name', 'text', 'uppercase'],
                ['last_name', 'last-name', 'Last name', 'text', 'uppercase'],
            ],
            [['landmark', 'landmark', 'Landmark', 'text', 'uppercase']],
            [['billing_address', 'billing-address', 'Billing address', 'text', 'uppercase']],
            [
                ['contact_number', 'contact-number', 'Contact number', 'tel'],
                ['email', 'email-address', 'Email address', 'email'],
            ],
            [['coordinates', 'coordinates', 'Coordinates', 'text']],
            [['remarks', 'remarks', 'Remarks', 'text', 'uppercase']],
        ];
        $installationFields = [
            ['installation_date', 'installation-date', 'Installation date', 'date'],
        ];
        $subscriptionFields = [
            ['billing_day', 'bill-generation', 'Bill generation day (1–31)', 'number'],
        ];
        $mikrotikFields = [
            ['pppoe_username', 'pppoe-name', 'PPPoE name', 'text'],
            ['pppoe_password', 'pppoe-password', 'PPPoE password', 'password'],
            ['pppoe_profile', 'pppoe-profile', 'PPPoE profile', 'text'],
            ['pppoe_comment', 'pppoe-comment', 'PPPoE comment', 'text', null, 'readonly'],
        ];
        $additionalFields = [
            ['area', 'area', 'Area', 'text'],
            ['subscription_date', 'subscription-date', 'Subscription date', 'date'],
            ['credit_limit', 'credit-limit', 'Credit limit', 'number'],
            ['external_url', 'external-url', 'External URL', 'url'],
            ['image_url', 'image-url', 'Image URL', 'url'],
            ['olt_device', 'olt-device', 'OLT device', 'text'],
            ['pon_port', 'pon-port', 'PON port', 'text'],
            ['nap_device', 'nap-device', 'NAP device', 'text'],
            ['nap_port', 'nap-port', 'NAP port', 'text'],
            ['onu_name', 'onu-name', 'ONU name', 'text'],
            ['caller_id', 'caller-id', 'Caller ID or PPPoE identifier', 'text'],
            ['mac_address', 'mac-address', 'MAC address', 'text'],
            ['network_remarks', 'network-remarks', 'Network remarks', 'text'],
        ];
        $requiredFields = ['account_number', 'first_name', 'last_name', 'billing_address'];
    @endphp

    <x-admin.page-heading title="Add subscriber" section="Subscriptions" />

    <form method="POST" action="{{ route('subscribers.store') }}" class="space-y-4">
        @csrf

        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                Please correct the highlighted fields and submit the form again.
            </div>
        @endif

        <div class="grid h-fit gap-4 xl:grid-cols-2">
            <!-- Personal Details -->
            <x-admin.panel title="Personal Details">
                <div class="space-y-3">
                    @foreach ($personalDetails as $fields)
                        <div @class([
                            'grid gap-3',
                            'sm:grid-cols-2' => count($fields) > 1,
                            'xl:grid-cols-3' => count($fields) === 3,
                        ])>
                            @include('admin.subscribers.fields')
                        </div>
                    @endforeach
                </div>
            </x-admin.panel>

            <!-- Subscription Configuration -->
            <div class="min-w-0 space-y-4">
                <x-admin.panel title="Subscription Configuration">
                    <div class="grid gap-3">
                        @php
                            $oldPlanId = old('plan_id');
                        @endphp
                        <div class="grid gap-3 sm:grid-cols-2 sm:items-start">
                            @include('admin.subscribers.fields', ['fields' => $installationFields])
                            <div class="grid min-w-0 gap-1.5">
                                <select id="plan" name="plan_id"
                                    aria-label="Plan"
                                    @if ($errors->has('plan_id')) aria-invalid="true" aria-describedby="plan-error" @endif
                                    class="h-11 w-full min-w-0 rounded-full border bg-white px-4 text-sm font-normal text-slate-900 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:bg-slate-950 dark:text-white {{ $errors->has('plan_id') ? 'border-red-500' : 'border-slate-300 dark:border-slate-700' }}">
                                    <option value="">No plan assigned</option>
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan->id }}" @selected(is_scalar($oldPlanId) && (string) $oldPlanId === (string) $plan->id)>
                                            {{ $plan->name }} —
                                            {{ $plan->speed_mbps ? number_format($plan->speed_mbps) . ' Mbps · ' : '' }}PHP
                                            {{ number_format((float) $plan->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id')
                                    <span id="plan-error" class="text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @include('admin.subscribers.fields', ['fields' => $subscriptionFields])
                    </div>
                </x-admin.panel>

                <!-- MikroTik Configuration -->
                <x-admin.panel title="MikroTik Configuration">
                    <div class="grid gap-3">
                        @include('admin.subscribers.fields', ['fields' => $mikrotikFields])
                    </div>
                </x-admin.panel>

                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('subscribers.index') }}"
                        class="min-w-28 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-center text-sm font-medium hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800">Cancel</a>
                    <button type="submit"
                        class="min-w-28 rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600">Submit</button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('click', async function(event) {
                const button = event.target.closest('[data-account-number-generate]');
                if (!button) return;

                const form = button.closest('form');
                const input = form?.querySelector('[data-account-number-input]');
                const message = form?.querySelector('[data-account-number-message]');
                if (!input || !message) return;

                button.disabled = true;
                message.textContent = '';

                try {
                    const response = await fetch(@json(route('subscribers.account-number')), {
                        headers: { Accept: 'application/json' },
                    });
                    const result = await response.json();

                    if (!response.ok) {
                        throw new Error(result.message || 'Could not generate an account number.');
                    }

                    input.value = result.account_number;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                } catch (error) {
                    message.textContent = error instanceof Error
                        ? error.message
                        : 'Could not generate an account number. Please try again.';
                } finally {
                    button.disabled = false;
                }
            });
        </script>

        {{-- Additional details section is currently commented out. Uncomment the following block to enable it.
        <details class="rounded-xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <summary class="cursor-pointer rounded-xl px-5 py-3 text-sm font-semibold text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-cyan-600 dark:text-white">Additional details</summary>
            <div class="grid gap-3 px-5 pb-5 sm:grid-cols-2 xl:grid-cols-3">
                @include('admin.subscribers.fields', ['fields' => $additionalFields])
            </div>
        </details>
         --}}
    </form>
</x-layouts::app>
