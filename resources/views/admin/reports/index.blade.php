@php
    $reports = [
        'daily' => ['title' => 'Daily accounting report', 'icon' => 'calendar-days'],
        'transactions' => ['title' => 'Payment and transaction history', 'icon' => 'arrow-left-right'],
        'expenses' => ['title' => 'Expenses', 'icon' => 'receipt'],
    ];
@endphp

<x-layouts::app :title="__('Reports')">
    <x-admin.page-heading title="Reports">
        <x-slot:description>Report data will become available when the backend is connected.</x-slot:description>
    </x-admin.page-heading>

    <nav aria-label="Report sections" class="mb-8 grid gap-3 sm:grid-cols-3 print:hidden">
        @foreach ($reports as $report => $details)
            <a
                href="#{{ $report }}"
                data-report-card="{{ $report }}"
                style="--sidebar-icon: url('{{ asset('icons/sidebar/'.$details['icon'].'.svg') }}')"
                class="sidebar-icon flex items-center gap-2.5 rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:border-cyan-600 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-800 dark:bg-slate-900 dark:text-white dark:hover:border-cyan-400"
            >
                {{ $details['title'] }}
            </a>
        @endforeach
    </nav>

    <div class="space-y-8">
        @foreach ($reports as $report => $details)
            <section id="{{ $report }}" aria-labelledby="report-{{ $report }}-title" tabindex="-1" class="scroll-mt-24 border-t border-slate-200 pt-6 dark:border-slate-800">
                <h2 id="report-{{ $report }}-title" class="mb-4 text-xl font-semibold text-slate-900 dark:text-white">{{ $details['title'] }}</h2>
                @include('admin.reports.'.$report)
            </section>
        @endforeach
    </div>
</x-layouts::app>
