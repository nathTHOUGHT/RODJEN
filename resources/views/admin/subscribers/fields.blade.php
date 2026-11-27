{{-- auto matic to all caps but put 'uppercase' after the type  --}}
@foreach ($fields as $field)
    @php
        [$name, $id, $label, $type] = $field;
        $format = $field[4] ?? null;

        $isPassword = $type === 'password';
        $oldValue = $isPassword ? '' : old($name);
        $fieldValue = match ($name) {
            'pppoe_comment' => trim(old('first_name', '') . ' ' . old('last_name', '')),
            default => is_scalar($oldValue) ? (string) $oldValue : '',
        };
        $hasError = $errors->has($name);
        $errorId = $id . '-error';

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
        $attribute = $field[5] ?? null;
        $isContactNumber = in_array($name, ['contact_number', 'contact-number', 'Contact number', 'tel'], true);
    @endphp

    <label for="{{ $id }}" class="grid min-w-0 gap-1.5 text-sm font-medium text-slate-700 dark:text-slate-300">
        {{ $label }}

        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
            value="{{ $format === 'uppercase' ? mb_strtoupper($fieldValue) : $fieldValue }}"
            autocomplete="{{ $autocomplete }}" @required(in_array($name, $requiredFields, true))
            @if ($name === 'billing_day') min="1" max="31" @endif
            @if ($name === 'credit_limit') min="0" step="0.01" @endif
            @if ($name === 'mac_address') placeholder="AA:BB:CC:DD:EE:FF" @endif
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @endif
            @if ($format === 'uppercase') oninput="this.value = this.value.toUpperCase()" @endif
            @if (in_array($name, ['first_name', 'last_name'], true)) data-name-field @endif
            @if ($name === 'pppoe_comment') data-pppoe-comment @endif
            @if ($attribute === 'readonly') readonly @endif
            @if ($isContactNumber) inputmode="numeric"
    maxlength="11"
    minlength="11"
    pattern="09[0-9]{9}"
    placeholder="09XXXXXXXXX" @endif
            class="w-full min-w-0 rounded-full border bg-white px-3 py-2 font-normal text-slate-900 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:bg-slate-950 dark:text-white {{ $hasError ? 'border-red-500' : 'border-slate-300 dark:border-slate-700' }}">

        @error($name)
            <span id="{{ $errorId }}" class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </label>
@endforeach

{{-- but later put on respective file  --}}
<script>
    document.addEventListener('input', function(event) {
        if (!event.target.matches('[data-name-field]')) return;

        const form = event.target.closest('form');
        if (!form) return;

        const firstName = form.querySelector('[name="first_name"]')?.value.trim() || '';
        const lastName = form.querySelector('[name="last_name"]')?.value.trim() || '';
        const comment = form.querySelector('[data-pppoe-comment]');

        if (comment) {
            comment.value = [firstName, lastName].filter(Boolean).join(' ').toUpperCase();
        }
    });

    // for auto add install date right after the comment for pppoe details
    document.addEventListener('change', function(event) {
        if (!event.target.matches('[name="installation_date"]')) return;

        const date = event.target.value;
        if (!date) return;

        const [year, month, day] = date.split('-');

        const formattedDate = `${parseInt(month, 10)}${day}${year}`;

        const form = event.target.closest('form');
        const comment = form?.querySelector('[data-pppoe-comment]');

        if (!comment) return;

        const firstName = form.querySelector('[name="first_name"]')?.value.trim() || '';
        const lastName = form.querySelector('[name="last_name"]')?.value.trim() || '';

        comment.value = [firstName, lastName, formattedDate]
            .filter(Boolean)
            .join(' ')
            .toUpperCase();
    });

    // Philippine mobile number validation
    document.addEventListener('input', function(event) {
        if (!event.target.matches(
                '[name="contact_number"], [name="contact-number"], [name="Contact number"], [name="tel"]'
            )) return;

        event.target.value = event.target.value
            .replace(/\D/g, '')
            .slice(0, 11);
    });
</script>
