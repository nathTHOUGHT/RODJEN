@php
    $dialogClasses = 'm-auto max-h-[calc(100dvh-2rem)] w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-xl border border-slate-200 bg-white p-5 text-slate-900 shadow-xl backdrop:bg-slate-950/60 dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:p-6';
    $labelClasses = 'grid min-w-0 gap-1.5 text-sm font-medium';
    $fieldClasses = 'w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2 font-normal focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-700 dark:bg-slate-950';
    $cancelClasses = 'rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-700 dark:hover:bg-slate-800';
    $disabledClasses = 'cursor-not-allowed rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white opacity-50';
@endphp

<dialog id="subscriber-promise-dialog" aria-labelledby="subscriber-promise-title" class="{{ $dialogClasses }}" x-data="{ date: '' }">
    <h2 id="subscriber-promise-title" class="text-lg font-semibold">Promise date</h2>

    <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <label class="{{ $labelClasses }}">
            Select date
            <input type="date" name="promise_date" class="{{ $fieldClasses }}" x-model="date">
        </label>
        <div class="{{ $labelClasses }}">
            <span id="subscriber-promise-selection">Selected date</span>
            <output aria-labelledby="subscriber-promise-selection" class="{{ $fieldClasses }}" x-text="date || 'No date selected'">No date selected</output>
        </div>
    </div>

    <form method="dialog" class="mt-5 flex justify-end gap-2">
        <button type="submit" class="{{ $cancelClasses }}">Cancel</button>
        <button type="button" disabled data-test="subscriber-promise-disabled" class="{{ $disabledClasses }}">Save</button>
    </form>
</dialog>

<dialog
    id="subscriber-comment-dialog"
    aria-labelledby="subscriber-comment-title"
    class="{{ $dialogClasses }}"
    x-data="{ timestamp: new Date().toLocaleString() }"
    x-on:toggle="if ($el.open) timestamp = new Date().toLocaleString()"
>
    <h2 id="subscriber-comment-title" class="text-lg font-semibold">Comment</h2>

    <dl class="mt-4 flex flex-wrap gap-x-2 text-sm">
        <dt class="font-medium">Timestamp</dt>
        <dd class="text-slate-600 dark:text-slate-400" x-text="timestamp"></dd>
    </dl>
    <label class="mt-4 {{ $labelClasses }}">
        Comment
        <textarea name="comment" rows="4" class="{{ $fieldClasses }}"></textarea>
    </label>

    <form method="dialog" class="mt-5 flex justify-end gap-2">
        <button type="submit" class="{{ $cancelClasses }}">Cancel</button>
        <button type="button" disabled data-test="subscriber-comment-disabled" class="{{ $disabledClasses }}">Save</button>
    </form>
</dialog>

<dialog id="subscriber-transaction-dialog" aria-labelledby="subscriber-transaction-title" class="{{ $dialogClasses }}">
    <h2 id="subscriber-transaction-title" class="text-lg font-semibold">Transaction details</h2>

    <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <label class="{{ $labelClasses }}">
            Transaction date
            <input name="transaction_date" type="date" value="" class="{{ $fieldClasses }}">
        </label>
        <div class="{{ $labelClasses }}">
            <span id="subscriber-transaction-balance">Current balance</span>
            <output aria-labelledby="subscriber-transaction-balance" class="{{ $fieldClasses }}">Not available</output>
        </div>
        <label class="{{ $labelClasses }}">
            Transaction type
            <select name="transaction_type" class="{{ $fieldClasses }}">
                <option value="">Select transaction type</option>
                <option value="payment">Payment</option>
                <option value="invoice">Invoice</option>
                <option value="rebate">Rebate</option>
                <option value="discount">Discount</option>
            </select>
        </label>
        <label class="{{ $labelClasses }}">
            Credit date
            <input name="credit_date" type="date" value="" class="{{ $fieldClasses }}">
        </label>
        <label class="{{ $labelClasses }} sm:col-span-2">
            Description
            <input name="description" type="text" value="" class="{{ $fieldClasses }}">
        </label>
        <label class="{{ $labelClasses }}">
            Amount
            <input name="amount" type="number" step="0.01" value="" class="{{ $fieldClasses }}">
        </label>
        <label class="{{ $labelClasses }}">
            Payment mode
            <input name="payment_mode" type="text" value="" class="{{ $fieldClasses }}">
        </label>
        <label class="{{ $labelClasses }} sm:col-span-2">
            Optional reference
            <input name="reference" type="text" value="" class="{{ $fieldClasses }}">
        </label>
    </div>

    <form method="dialog" class="mt-5 flex justify-end gap-2">
        <button type="submit" class="{{ $cancelClasses }}">Cancel</button>
        <button type="button" disabled data-test="subscriber-transaction-disabled" class="{{ $disabledClasses }}">Submit</button>
    </form>
</dialog>
