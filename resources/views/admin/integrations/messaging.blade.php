<x-layouts::app :title="__('Chats')">
    <x-admin.page-heading title="Chats" section="Messaging" />
    <div class="space-y-4">
        <x-admin.frontend-notice />
        <x-admin.panel title="Conversations">
            <p class="py-8 text-center text-sm text-slate-500">No conversations yet</p>
        </x-admin.panel>
    </div>
</x-layouts::app>
