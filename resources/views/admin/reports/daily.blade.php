<div class="space-y-4">
    <x-admin.panel title="Report filters" class="print:hidden">
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="grid content-start gap-2 text-sm font-medium">
                Report date
                <input type="date" name="report_date" class="rounded-md border border-slate-300 px-3 py-2 dark:border-slate-700 dark:bg-slate-950">
            </label>
            <label class="grid content-start gap-2 text-sm font-medium">
                Staff/User
                <select name="staff_users[]" multiple disabled class="rounded-md border border-slate-300 px-3 py-2 dark:border-slate-700 dark:bg-slate-950"></select>
            </label>
        </div>
        <p class="mt-4 text-sm text-slate-500">Staff options and report aggregation will be available when the backend is connected.</p>
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach (['generate' => 'Generate preview', 'print' => 'Print', 'export' => 'Export'] as $key => $label)
                <button type="button" disabled data-test="daily-{{ $key }}-disabled" class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">{{ $label }}</button>
            @endforeach
        </div>
    </x-admin.panel>

    <x-admin.panel title="Accounting Daily Report">
        <dl class="grid gap-3 text-sm sm:grid-cols-2">
            @foreach (['Report date', 'Prepared by / Selected staff', 'Company', 'Status'] as $label)
                <div><dt class="font-medium">{{ $label }}</dt><dd>Not available</dd></div>
            @endforeach
        </dl>
        @foreach ([
            'Bill Collection Summary' => ['Payment type', 'Amount'],
            'Other Cash Collection Summary' => ['Description', 'Amount'],
            'Expenses Breakdown' => ['Description', 'Amount'],
        ] as $title => $columns)
            <section class="mt-6">
                <h3 class="mb-2 font-semibold">{{ $title }}</h3>
                <x-admin.empty-table :columns="$columns" message="No entries yet" />
            </section>
        @endforeach
        <section class="mt-6">
            <h3 class="mb-2 font-semibold">Cash Status</h3>
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                @foreach (['Total cash available', 'Total expenses', 'Cash on hand', 'Remit', 'Remarks'] as $label)
                    <div><dt class="font-medium">{{ $label }}</dt><dd>Not available</dd></div>
                @endforeach
            </dl>
        </section>
        <div class="mt-8 grid gap-4 text-sm sm:grid-cols-3">
            @foreach (['Prepared by', 'Checked by', 'Approved by'] as $label)
                <p class="border-t border-slate-300 pt-2 dark:border-slate-700">{{ $label }}</p>
            @endforeach
        </div>
    </x-admin.panel>
</div>
