<x-layouts::app :title="__('Inventory')">
    <x-admin.page-heading title="Inventory" />
    <div class="space-y-4">
        <x-admin.frontend-notice />
        <x-admin.panel title="Stock">
            <x-admin.empty-table :columns="['Item', 'Category', 'In stock', 'Location', 'Status']" message="No inventory items yet" />
        </x-admin.panel>
    </div>
</x-layouts::app>
