<x-layouts::app :title="__('Job Order')">
    <x-admin.page-heading title="Job Order" />
    <div class="space-y-4">
        <x-admin.frontend-notice />
        <x-admin.panel title="Job orders">
            <x-admin.empty-table :columns="['Order number', 'Subscriber', 'Job type', 'Assigned to', 'Scheduled date', 'Status']" message="No job orders yet" />
        </x-admin.panel>
    </div>
</x-layouts::app>
