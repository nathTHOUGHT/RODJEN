@props([
    'columns',
    'message' => 'No records yet',
])

<div class="overflow-x-auto" data-table-scroll>
    <table class="min-w-full border-separate border-spacing-0 text-left text-sm">
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th scope="col" class="whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="{{ count($columns) }}" class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">{{ $message }}</td>
            </tr>
        </tbody>
    </table>
</div>
