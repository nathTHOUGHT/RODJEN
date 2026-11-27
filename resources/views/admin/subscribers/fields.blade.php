@foreach ($fields as [$name, $id, $label, $type])
    @php
        $isPassword = $type === 'password';
        $oldValue = $isPassword ? '' : old($name);
        $fieldValue = is_scalar($oldValue) ? (string) $oldValue : '';
        $hasError = $errors->has($name);
        $errorId = $id.'-error';
        $autocomplete = match ($name) {
            'first_name' => 'given-name',
            'last_name' => 'family-name',
            'billing_address' => 'street-address',
            'contact_number' => 'tel',
            'email' => 'email',
            'pppoe_password' => 'new-password',
            'external_url', 'image_url' => 'url',
            default => 'off',
        };
    @endphp

    <label for="{{ $id }}" class="grid min-w-0 gap-1.5 text-sm font-medium text-slate-700 dark:text-slate-300">
        {{ $label }}
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $fieldValue }}"
            autocomplete="{{ $autocomplete }}"
            @required(in_array($name, $requiredFields, true))
            @if ($name === 'billing_day') min="1" max="31" @endif
            @if ($name === 'credit_limit') min="0" step="0.01" @endif
            @if ($name === 'mac_address') placeholder="AA:BB:CC:DD:EE:FF" @endif
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @endif
            class="w-full min-w-0 rounded-full border bg-white px-3 py-2 font-normal text-slate-900 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:bg-slate-950 dark:text-white {{ $hasError ? 'border-red-500' : 'border-slate-300 dark:border-slate-700' }}"
        >
        @error($name)
            <span id="{{ $errorId }}" class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </label>
@endforeach
