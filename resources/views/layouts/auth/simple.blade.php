<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-100">
        <main class="grid min-h-screen place-items-center p-6">
            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-200 bg-slate-900 px-8 py-7 text-white dark:border-slate-800">
                    <a href="{{ route('home') }}" class="block" aria-label="{{ config('app.name', 'ISP Admin') }}">
                        <span class="block text-xs font-semibold uppercase tracking-[0.22em] text-cyan-300">ISP Operations</span>
                        <span class="mt-1 block text-xl font-semibold">Administration Panel</span>
                    </a>
                </div>

                <div class="p-8">
                    {{ $slot }}
                </div>
            </div>
        </main>

        @fluxScripts
    </body>
</html>
