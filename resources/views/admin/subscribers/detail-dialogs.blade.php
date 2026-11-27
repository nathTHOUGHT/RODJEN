@php
    $dialogClasses = 'm-auto max-h-[calc(100dvh-2rem)] w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-xl border border-slate-200 bg-white p-5 text-slate-900 shadow-xl backdrop:bg-slate-950/60 dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:p-6';
    $labelClasses = 'grid min-w-0 gap-1.5 text-sm font-medium';
    $fieldClasses = 'w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2 font-normal focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-700 dark:bg-slate-950';
    $cancelClasses = 'rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-700 dark:hover:bg-slate-800';
    $disabledClasses = 'cursor-not-allowed rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white opacity-50';
@endphp

<dialog
    id="subscriber-personal-edit-dialog"
    aria-labelledby="subscriber-personal-edit-title"
    class="{{ $dialogClasses }}"
    @if ($errors->hasAny(['account_number', 'first_name', 'last_name', 'area', 'landmark', 'billing_address', 'contact_number', 'email', 'coordinates', 'remarks', 'external_url'])) x-init="$el.showModal()" @endif
>
    <h2 id="subscriber-personal-edit-title" class="text-lg font-semibold">Edit Personal Details</h2>

    <form method="post" action="{{ route('subscribers.personal.update', $subscriber) }}" class="mt-4">
        @csrf
        @method('PATCH')
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ([
                'account_number' => ['Account number', 'text', $subscriber->account_number],
                'first_name' => ['First name', 'text', $subscriber->first_name],
                'last_name' => ['Last name', 'text', $subscriber->last_name],
                'area' => ['Area', 'text', $subscriber->area],
                'landmark' => ['Landmark', 'text', $subscriber->landmark],
                'billing_address' => ['Billing address', 'text', $subscriber->billing_address],
                'contact_number' => ['Contact number', 'tel', $subscriber->contact_number],
                'email' => ['Email address', 'email', $subscriber->email],
                'coordinates' => ['Coordinates', 'text', $subscriber->coordinates],
                'external_url' => ['External URL', 'url', $subscriber->external_url],
            ] as $name => [$label, $type, $value])
                <label class="{{ $labelClasses }} {{ in_array($name, ['billing_address', 'external_url'], true) ? 'sm:col-span-2' : '' }}">
                    {{ $label }}
                    <input name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" @required(in_array($name, ['account_number', 'first_name', 'last_name', 'billing_address'], true))
                        @if ($errors->has($name)) aria-invalid="true" @endif
                        class="{{ $fieldClasses }}">
                    @error($name)
                        <span role="alert" class="text-sm font-normal text-red-600">{{ $message }}</span>
                    @enderror
                </label>
            @endforeach
            <label class="{{ $labelClasses }} sm:col-span-2">
                Remarks
                <textarea name="remarks" rows="3" class="{{ $fieldClasses }}">{{ old('remarks', $subscriber->remarks) }}</textarea>
                @error('remarks')
                    <span role="alert" class="text-sm font-normal text-red-600">{{ $message }}</span>
                @enderror
            </label>
        </div>
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" x-on:click="$el.closest('dialog').close()" class="{{ $cancelClasses }}">Cancel</button>
            <button type="submit" class="rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800 focus-visible:outline-2 focus-visible:outline-cyan-600">Save changes</button>
        </div>
    </form>
</dialog>

<dialog
    id="subscriber-service-edit-dialog"
    aria-labelledby="subscriber-service-edit-title"
    class="{{ $dialogClasses }}"
    @if ($errors->hasAny(['plan_id', 'installation_date', 'subscription_date', 'billing_day', 'credit_limit', 'service_type', 'status'])) x-init="$el.showModal()" @endif
>
    <h2 id="subscriber-service-edit-title" class="text-lg font-semibold">Edit Service Details</h2>

    @php($subscription = $subscriber->subscription)
    <form method="post" action="{{ route('subscribers.service.update', $subscriber) }}" class="mt-4">
        @csrf
        @method('PATCH')
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="{{ $labelClasses }} sm:col-span-2">
                Plan
                <select name="plan_id" class="{{ $fieldClasses }}">
                    <option value="">No plan assigned</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" @selected((string) old('plan_id', $subscription?->plan_id) === (string) $plan->id)>
                            {{ $plan->name }} — PHP {{ number_format((float) $plan->price, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('plan_id')
                    <span role="alert" class="text-sm font-normal text-red-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="{{ $labelClasses }}">
                Service type
                <input name="service_type" type="text" maxlength="32" required value="{{ old('service_type', $subscription?->service_type) }}" class="{{ $fieldClasses }}">
                @error('service_type')
                    <span role="alert" class="text-sm font-normal text-red-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="{{ $labelClasses }}">
                Billing status
                <input name="status" type="text" maxlength="32" required value="{{ old('status', $subscription?->status) }}" class="{{ $fieldClasses }}">
                @error('status')
                    <span role="alert" class="text-sm font-normal text-red-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="{{ $labelClasses }}">
                Installation date
                <input name="installation_date" type="date" value="{{ old('installation_date', $subscription?->installation_date?->format('Y-m-d')) }}" class="{{ $fieldClasses }}">
                @error('installation_date')
                    <span role="alert" class="text-sm font-normal text-red-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="{{ $labelClasses }}">
                Subscription date
                <input name="subscription_date" type="date" value="{{ old('subscription_date', $subscription?->subscription_date?->format('Y-m-d')) }}" class="{{ $fieldClasses }}">
                @error('subscription_date')
                    <span role="alert" class="text-sm font-normal text-red-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="{{ $labelClasses }}">
                Bill generation day
                <input name="billing_day" type="number" min="1" max="31" value="{{ old('billing_day', $subscription?->billing_day) }}" class="{{ $fieldClasses }}">
                @error('billing_day')
                    <span role="alert" class="text-sm font-normal text-red-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="{{ $labelClasses }}">
                Credit limit
                <input name="credit_limit" type="number" min="0" max="99999999.99" step="0.01" value="{{ old('credit_limit', $subscription?->credit_limit) }}" class="{{ $fieldClasses }}">
                @error('credit_limit')
                    <span role="alert" class="text-sm font-normal text-red-600">{{ $message }}</span>
                @enderror
            </label>
        </div>
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" x-on:click="$el.closest('dialog').close()" class="{{ $cancelClasses }}">Cancel</button>
            <button type="submit" class="rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800 focus-visible:outline-2 focus-visible:outline-cyan-600">Save changes</button>
        </div>
    </form>
</dialog>

<dialog
    id="subscriber-mikrotik-edit-dialog"
    aria-labelledby="subscriber-mikrotik-edit-title"
    class="{{ $dialogClasses }}"
    @if ($errors->hasAny(['username', 'password', 'profile'])) x-init="$el.showModal()" @endif
>
    <h2 id="subscriber-mikrotik-edit-title" class="text-lg font-semibold">Edit MikroTik Details</h2>

    @php($networkAccount = $subscriber->subscription?->networkAccount)
    <form method="post" action="{{ route('subscribers.mikrotik.update', $subscriber) }}" class="mt-4">
        @csrf
        @method('PATCH')
        <div class="grid gap-4">
            <label class="{{ $labelClasses }}">
                Name
                <input name="username" type="text" maxlength="255" value="{{ old('username', $networkAccount?->username) }}" class="{{ $fieldClasses }}">
                @error('username')
                    <span role="alert" class="text-sm font-normal text-red-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="{{ $labelClasses }}">
                Password
                <input name="password" type="text" maxlength="255" autocomplete="new-password" value="{{ old('password', $networkAccount?->password) }}" class="{{ $fieldClasses }}">
                @error('password')
                    <span role="alert" class="text-sm font-normal text-red-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="{{ $labelClasses }}">
                Profile
                <input name="profile" type="text" maxlength="255" value="{{ old('profile', $networkAccount?->profile) }}" class="{{ $fieldClasses }}">
                @error('profile')
                    <span role="alert" class="text-sm font-normal text-red-600">{{ $message }}</span>
                @enderror
            </label>
            <div class="{{ $labelClasses }}">
                Comment
                <p class="{{ $fieldClasses }} min-h-10 whitespace-pre-wrap text-slate-600 dark:text-slate-300">{{ $networkAccount?->comment ?: 'No comment' }}</p>
            </div>
        </div>
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" x-on:click="$el.closest('dialog').close()" class="{{ $cancelClasses }}">Cancel</button>
            <button type="submit" class="rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800 focus-visible:outline-2 focus-visible:outline-cyan-600">Save changes</button>
        </div>
    </form>
</dialog>

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
    @if ($errors->has('comment')) x-init="$el.showModal()" @endif
>
    <h2 id="subscriber-comment-title" class="text-lg font-semibold">Comment</h2>

    <form method="post" action="{{ route('subscribers.comments.store', $subscriber) }}" class="mt-4">
        @csrf
        <label class="{{ $labelClasses }}">
            Comment
            <textarea name="comment" rows="4" maxlength="5000" required @if ($errors->has('comment')) aria-describedby="subscriber-comment-error" @endif class="{{ $fieldClasses }}">{{ old('comment') }}</textarea>
        </label>
        @error('comment')
            <p id="subscriber-comment-error" role="alert" class="mt-2 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
        @enderror
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" x-on:click="$el.closest('dialog').close()" class="{{ $cancelClasses }}">Cancel</button>
            <button type="submit" class="rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800 focus-visible:outline-2 focus-visible:outline-cyan-600">Save</button>
        </div>
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
