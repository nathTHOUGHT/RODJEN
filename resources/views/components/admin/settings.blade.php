@props(['heading'])

@php
    $tabs = require resource_path('data/panel-settings-tabs.php');
    $active = request()->routeIs('router.backups') ? 'router.index' : request()->route()?->getName();
@endphp

<x-admin.workspace title="Settings" :tabs="$tabs" :active="$active">
    <div class="space-y-4">
        <h2 class="text-lg font-semibold text-slate-900">{{ $heading }}</h2>
        {{ $slot }}
    </div>
</x-admin.workspace>
