<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body
        class="rodjen-auth min-h-screen bg-slate-100 text-slate-800 antialiased"
        style="--rodjen-auth-light: url('{{ asset('brand/mockup-office.jpg') }}');"
    >
        <main class="grid min-h-screen place-items-center p-6">
            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
                <div class="border-b border-slate-200 bg-slate-50 px-8 py-7 text-slate-900">
                    <a href="{{ route('home') }}" class="flex items-center gap-4 rounded-md focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-cyan-600" aria-label="RODJEN Technet Administration Panel">
                        <span
                            aria-hidden="true"
                            class="block size-18 shrink-0 bg-contain bg-center bg-no-repeat"
                            style="background-image: url('{{ asset('brand/rodjen-stacked.png') }}')"
                        ></span>
                        <span>
                            <span class="block text-xs font-semibold uppercase tracking-wider text-cyan-700">RODJEN Technet</span>
                            <span class="mt-1 block text-xl font-semibold">Administration Panel</span>
                        </span>
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
