@props(['heading'])

@php
    $tabs = require resource_path('data/panel-settings-tabs.php');
    if (auth()->user()?->user_type === \App\Enums\UserType::Admin->value) {
        unset($tabs['integrations.scheduler'], $tabs['integrations.settings'], $tabs['router.index'], $tabs['staff.index']);
    }
    $active = request()->routeIs('router.backups') ? 'router.index' : request()->route()?->getName();
@endphp

<x-admin.workspace title="Settings" :tabs="$tabs" :active="$active">
    <div class="space-y-4">
        <h2 class="text-lg font-semibold text-slate-900">{{ $heading }}</h2>
        {{ $slot }}
    </div>
</x-admin.workspace>
