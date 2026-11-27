{{-- auto matic to all caps but put 'uppercase' after the type  --}}
@foreach ($fields as $field)
    @php
        [$name, $id, $label, $type] = $field;
        $format = $field[4] ?? null;

        $isPassword = $type === 'password';
        $oldValue = $isPassword ? '' : old($name);
        $oldFirstName = old('first_name', '');
        $oldLastName = old('last_name', '');
        $fieldValue = match ($name) {
            'pppoe_comment' => trim(
                (is_scalar($oldFirstName) ? (string) $oldFirstName : '')
                .' '.(is_scalar($oldLastName) ? (string) $oldLastName : '')
            ),
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
        $placeholder = match ($name) {
            'account_number' => 'Account No.',
            'mac_address' => 'AA:BB:CC:DD:EE:FF',
            default => $isContactNumber ? 'Mobile No.' : 'Enter '.mb_strtolower($label),
        };
    @endphp

    <div class="grid min-w-0 gap-1.5">
        @if ($name === 'account_number')
            <div class="relative">
        @endif
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
            value="{{ $format === 'uppercase' ? mb_strtoupper($fieldValue) : $fieldValue }}"
            autocomplete="{{ $autocomplete }}" @required(in_array($name, $requiredFields, true))
            aria-label="{{ $label }}" placeholder="{{ $placeholder }}"
            @if ($name === 'account_number') inputmode="numeric" maxlength="8" minlength="8" pattern="[0-9]{8}" data-account-number-input @endif
            @if ($name === 'billing_day') min="1" max="31" @endif
            @if ($name === 'credit_limit') min="0" step="0.01" @endif
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @endif
            @if ($format === 'uppercase') oninput="this.value = this.value.toUpperCase()" @endif
            @if ($name === 'account_number') oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8)" @endif
            @if (in_array($name, ['first_name', 'last_name'], true)) data-name-field @endif
            @if ($name === 'pppoe_comment') data-pppoe-comment @endif
            @if ($attribute === 'readonly') readonly @endif
            @if ($isContactNumber) inputmode="numeric"
    maxlength="11"
    minlength="11"
    pattern="09[0-9]{9}" @endif
            class="h-11 w-full min-w-0 rounded-full border bg-white px-4 text-sm font-normal text-slate-900 placeholder:text-slate-400 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:bg-slate-950 dark:text-white {{ $hasError ? 'border-red-500' : 'border-slate-300 dark:border-slate-700' }} {{ $name === 'account_number' ? 'pr-12' : '' }}">
        @if ($name === 'account_number')
                <button type="button" data-account-number-generate aria-label="Generate account number" title="Generate account number"
                    class="absolute right-1.5 top-1/2 inline-flex size-8 -translate-y-1/2 items-center justify-center rounded-full text-cyan-700 hover:bg-cyan-50 focus-visible:outline-2 focus-visible:outline-cyan-600 disabled:cursor-wait disabled:opacity-50 dark:text-cyan-300 dark:hover:bg-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" aria-hidden="true" class="size-4">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                </button>
            </div>
        @endif

        @error($name)
            <span id="{{ $errorId }}" class="text-sm font-normal text-red-600">{{ $message }}</span>
        @enderror
        @if ($name === 'account_number')
            <span data-account-number-message role="status" aria-live="polite" class="text-xs font-normal text-red-600"></span>
        @endif
    </div>
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
