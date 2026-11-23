<x-layouts::app :title="__('Add subscriber')">
    <x-admin.page-heading title="Add subscriber" section="Subscriptions">
        <x-slot:description>Prepare the customer and network profile. Saving remains unavailable until the backend is connected.</x-slot:description>
    </x-admin.page-heading>

    <div class="space-y-6" data-ui-only-form>
        <x-admin.frontend-notice />

        <x-admin.panel title="General profile">
            @php
                $generalFields = [
                    ['account-number', 'Account number', 'text'],
                    ['first-name', 'First name', 'text'],
                    ['last-name', 'Last name', 'text'],
                    ['billing-address', 'Billing address', 'text'],
                    ['area', 'Area', 'text'],
                    ['contact-number', 'Contact number', 'tel'],
                    ['email-address', 'Email address', 'email'],
                    ['coordinates', 'Coordinates', 'text'],
                    ['subscription-date', 'Subscription date', 'date'],
                    ['credit-limit', 'Credit limit', 'number'],
                    ['remarks', 'Remarks', 'text'],
                    ['external-url', 'External URL', 'url'],
                    ['image-url', 'Image URL', 'url'],
                ];
            @endphp
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($generalFields as [$id, $label, $type])
                    <label for="{{ $id }}" class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 {{ in_array($id, ['billing-address', 'remarks']) ? 'md:col-span-2' : '' }}">
                        {{ $label }}
                        <input id="{{ $id }}" type="{{ $type }}" value="" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 font-normal dark:border-slate-700 dark:bg-slate-950">
                    </label>
                @endforeach
            </div>
        </x-admin.panel>

        <x-admin.panel title="Network profile">
            @php
                $networkFields = [
                    ['olt-device', 'OLT device'],
                    ['pon-port', 'PON port'],
                    ['nap-device', 'NAP device'],
                    ['nap-port', 'NAP port'],
                    ['onu-name', 'ONU name'],
                    ['caller-id', 'Caller ID or PPPoE identifier'],
                    ['mac-address', 'MAC address'],
                    ['network-remarks', 'Remarks'],
                ];
            @endphp
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($networkFields as [$id, $label])
                    <label for="{{ $id }}" class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                        {{ $label }}
                        <input id="{{ $id }}" type="text" value="" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 font-normal dark:border-slate-700 dark:bg-slate-950">
                    </label>
                @endforeach
            </div>
        </x-admin.panel>

        <div class="flex justify-end gap-3">
            <a href="{{ route('subscribers.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium dark:border-slate-700">Cancel</a>
            <button type="button" disabled data-test="subscriber-create-disabled" class="cursor-not-allowed rounded-lg bg-cyan-600 px-4 py-2.5 text-sm font-semibold text-white opacity-50">Create subscriber</button>
        </div>
    </div>
</x-layouts::app>
