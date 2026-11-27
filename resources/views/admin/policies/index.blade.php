<x-admin.settings heading="Policies and conditions">
    <div class="space-y-6">
        <x-admin.frontend-notice />
        <x-admin.panel title="Policy rules">
            <x-slot:actions>
                <button type="button" disabled class="cursor-not-allowed rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white opacity-50">Add policy</button>
            </x-slot:actions>
            <label for="policy-search" class="mb-5 ml-auto grid max-w-sm gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                Search
                <input id="policy-search" type="search" value="" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-normal normal-case tracking-normal dark:border-slate-700 dark:bg-slate-950">
            </label>
            <x-admin.empty-table :columns="['Policy', 'PPPoE action', 'PPPoE conditions', 'Hotspot action', 'Hotspot conditions', 'IPoE action', 'IPoE conditions']" message="No policies or conditions yet" />
        </x-admin.panel>
    </div>
</x-admin.settings>
