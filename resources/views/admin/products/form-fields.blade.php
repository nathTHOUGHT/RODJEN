@php
    $fieldClasses = 'w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-700 dark:bg-slate-950 dark:text-white';
    $labelClasses = 'grid gap-1.5 text-sm font-medium text-slate-700 dark:text-slate-200';
    $oldFormContext = old('_plan_form');
    $restoreOldInput = is_string($oldFormContext) && hash_equals($formContext, $oldFormContext);
    $scalarValue = static function (string $key, mixed $fallback) use ($restoreOldInput): string {
        $value = $restoreOldInput ? old($key) : $fallback;

        return is_scalar($value) ? (string) $value : (is_scalar($fallback) ? (string) $fallback : '');
    };
    $nameValue = $scalarValue('name', $plan?->name ?? '');
    $profileValue = $scalarValue('profile_group', $plan?->profile_group ?? 'default-encryption');
    $transactionTypeValue = $scalarValue('transaction_type', $plan?->transaction_type ?? 'mrc');
    $speedValue = $scalarValue('speed_mbps', $plan?->speed_mbps ?? '');
    $priceValue = $scalarValue('price', $plan?->price ?? '');
@endphp

<label for="{{ $fieldPrefix }}-name" class="{{ $labelClasses }}">
    Product Name
    <input
        id="{{ $fieldPrefix }}-name"
        name="name"
        type="text"
        value="{{ $nameValue }}"
        maxlength="255"
        placeholder="Enter Product Name"
        required
        @if ($autofocus) autofocus @endif
        class="{{ $fieldClasses }}"
        @if ($restoreOldInput)
            @error('name') aria-invalid="true" aria-describedby="{{ $fieldPrefix }}-name-error" @enderror
        @endif
    >
    @if ($restoreOldInput)
        @error('name') <span id="{{ $fieldPrefix }}-name-error" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</span> @enderror
    @endif
</label>

<label for="{{ $fieldPrefix }}-profile" class="{{ $labelClasses }}">
    Profile
    <select
        id="{{ $fieldPrefix }}-profile"
        name="profile_group"
        required
        class="{{ $fieldClasses }}"
        @if ($restoreOldInput)
            @error('profile_group') aria-invalid="true" aria-describedby="{{ $fieldPrefix }}-profile-error" @enderror
        @endif
    >
        <optgroup label="PPPoE profiles">
            @foreach ($profileGroups as $value => $label)
                <option value="{{ $value }}" @selected($profileValue === $value)>{{ $label }}</option>
            @endforeach
        </optgroup>
    </select>
    @if ($restoreOldInput)
        @error('profile_group') <span id="{{ $fieldPrefix }}-profile-error" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</span> @enderror
    @endif
</label>

<label for="{{ $fieldPrefix }}-type" class="{{ $labelClasses }}">
    Type
    <select
        id="{{ $fieldPrefix }}-type"
        name="transaction_type"
        required
        class="{{ $fieldClasses }}"
        @if ($restoreOldInput)
            @error('transaction_type') aria-invalid="true" aria-describedby="{{ $fieldPrefix }}-type-error" @enderror
        @endif
    >
        @foreach ($transactionTypes as $value => $label)
            <option value="{{ $value }}" @selected($transactionTypeValue === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @if ($restoreOldInput)
        @error('transaction_type') <span id="{{ $fieldPrefix }}-type-error" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</span> @enderror
    @endif
</label>

<label for="{{ $fieldPrefix }}-speed" class="{{ $labelClasses }}">
    Speed (Mbps)
    <span class="flex overflow-hidden rounded-lg border border-slate-300 bg-white focus-within:outline-2 focus-within:outline-cyan-600 dark:border-slate-700 dark:bg-slate-950">
        <input
            id="{{ $fieldPrefix }}-speed"
            name="speed_mbps"
            type="number"
            value="{{ $speedValue }}"
            min="1"
            max="100000"
            step="1"
            inputmode="numeric"
            placeholder="Enter Speed"
            required
            class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2.5 text-center text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none dark:text-white"
            @if ($restoreOldInput)
                @error('speed_mbps') aria-invalid="true" aria-describedby="{{ $fieldPrefix }}-speed-error" @enderror
            @endif
        >
        <span aria-hidden="true" class="grid place-items-center border-l border-slate-300 bg-slate-50 px-3 text-sm font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">Mbps</span>
    </span>
    @if ($restoreOldInput)
        @error('speed_mbps') <span id="{{ $fieldPrefix }}-speed-error" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</span> @enderror
    @endif
</label>

<label for="{{ $fieldPrefix }}-price" class="{{ $labelClasses }}">
    Price
    <span class="flex overflow-hidden rounded-lg border border-slate-300 bg-white focus-within:outline-2 focus-within:outline-cyan-600 dark:border-slate-700 dark:bg-slate-950">
        <span aria-hidden="true" class="grid place-items-center border-r border-slate-300 bg-slate-50 px-3 text-sm font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">₱</span>
        <input
            id="{{ $fieldPrefix }}-price"
            name="price"
            type="number"
            value="{{ $priceValue }}"
            min="0"
            max="99999999.99"
            step="0.01"
            inputmode="decimal"
            placeholder="Enter Price"
            required
            class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2.5 text-center text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none dark:text-white"
            @if ($restoreOldInput)
                @error('price') aria-invalid="true" aria-describedby="{{ $fieldPrefix }}-price-error" @enderror
            @endif
        >
    </span>
    @if ($restoreOldInput)
        @error('price') <span id="{{ $fieldPrefix }}-price-error" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</span> @enderror
    @endif
</label>
