<x-layouts::app :title="__('Products and services')">
    <x-admin.page-heading title="Products and services" section="Subscriptions" />

    <div class="space-y-6">
        <x-admin.frontend-notice />
        <x-admin.panel title="Service catalogue">
            <x-slot:actions>
                <button type="button" disabled class="cursor-not-allowed rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white opacity-50">Add product</button>
            </x-slot:actions>
            <label for="product-search" class="mb-5 ml-auto grid max-w-sm gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                Search
                <input id="product-search" type="search" value="" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-normal normal-case tracking-normal dark:border-slate-700 dark:bg-slate-950">
            </label>
            <x-admin.empty-table :columns="['Product', 'Profile group', 'Transaction type', 'Price', 'Usage or account count']" message="No products or services yet" />
        </x-admin.panel>
    </div>
</x-layouts::app>
