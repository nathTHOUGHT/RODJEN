@props(['name', 'logoutRoute'])

<div {{ $attributes->class(['border-t border-slate-700 p-4']) }}>
    <p class="truncate text-sm font-medium text-white">{{ $name }}</p>
    <form method="POST" action="{{ $logoutRoute }}" class="mt-3">
        @csrf
        <button type="submit" class="w-full rounded-md border border-slate-600 px-3 py-2 text-left text-sm text-slate-200 hover:bg-slate-800" data-test="logout-button">
            {{ __('Log out') }}
        </button>
    </form>
</div>
