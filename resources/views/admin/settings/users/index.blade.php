<x-admin.settings heading="Staff users">
    <x-admin.frontend-notice />
    @auth
        <a href="{{ route('profile.edit') }}" class="inline-flex rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-cyan-800 hover:bg-cyan-50 focus-visible:outline-2 focus-visible:outline-cyan-600">My profile</a>
    @endauth
</x-admin.settings>
