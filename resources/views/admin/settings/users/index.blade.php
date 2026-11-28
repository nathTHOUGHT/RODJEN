@php
    $dialogClasses = 'm-auto max-h-[calc(100dvh-2rem)] w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-xl border border-slate-200 bg-white p-0 text-slate-900 shadow-xl backdrop:bg-slate-950/60 dark:border-slate-700 dark:bg-slate-900 dark:text-white';
    $fieldClasses = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-700 dark:bg-slate-950 dark:text-white';
    $primaryButtonClasses = 'rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600';
    $secondaryButtonClasses = 'rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800';
    $actor = auth()->user();
@endphp

<x-admin.settings heading="Staff users">
    <div
        x-data
        class="space-y-6"
        @if ($errors->any() && old('_staff_form') === 'create')
            x-init="$nextTick(() => $refs.createStaffDialog.showModal())"
        @endif
    >
        <x-admin.panel title="Staff Accounts">
            <x-slot:actions>
                <button
                    type="button"
                    x-ref="addStaffButton"
                    x-on:click="$refs.createStaffDialog.showModal()"
                    aria-haspopup="dialog"
                    class="{{ $primaryButtonClasses }}"
                >Add Account</button>
            </x-slot:actions>

            @if ($users->isEmpty())
                <p class="py-8 text-center text-sm text-slate-500 dark:text-slate-400">No staff accounts found.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full border-separate border-spacing-0 text-left text-sm">
                        <thead>
                            <tr>
                                @foreach (['Name', 'Email', 'User type'] as $heading)
                                    <th scope="col" class="whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ $heading }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $staffUser)
                                @php
                                    $canEditProfile = $actor !== null
                                        && ($actor->user_type === \App\Enums\UserType::Developer->value
                                            || $staffUser->user_type !== \App\Enums\UserType::Developer->value);
                                    $canDeleteProfile = $actor !== null
                                        && $actor->id !== $staffUser->id
                                        && ($actor->user_type === \App\Enums\UserType::Developer->value
                                            || $staffUser->user_type !== \App\Enums\UserType::Developer->value);
                                @endphp
                                <tr>
                                    <th scope="row" class="border-b border-slate-200 px-4 py-3 font-medium dark:border-slate-800">
                                        {{ $staffUser->name }}
                                    </th>
                                    <td class="border-b border-slate-200 px-4 py-3 dark:border-slate-800">{{ $staffUser->email }}</td>
                                    <td class="border-b border-slate-200 px-4 py-3 dark:border-slate-800">
                                        <div class="flex items-center justify-between gap-3">
                                            <span>{{ str($staffUser->user_type)->headline() }}</span>
                                            @if ($canEditProfile || $canDeleteProfile)
                                                <div class="flex shrink-0 items-center gap-1">
                                                    @if ($canEditProfile)
                                                        <button
                                                            type="button"
                                                            x-ref="editStaffButton{{ $staffUser->id }}"
                                                            x-on:click="$refs['editStaffDialog{{ $staffUser->id }}'].showModal()"
                                                            aria-haspopup="dialog"
                                                            aria-label="Edit profile for {{ $staffUser->name }}"
                                                            title="Edit profile"
                                                            class="inline-grid size-8 place-items-center rounded-md text-slate-500 hover:bg-cyan-50 hover:text-cyan-800 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-cyan-300"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-4">
                                                                <path d="M12 20h9" />
                                                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                    @if ($canDeleteProfile)
                                                        <form method="POST" action="{{ route('staff.destroy', $staffUser) }}" onsubmit="return confirm('Delete this staff account? This cannot be undone.')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button
                                                                type="submit"
                                                                aria-label="Delete account for {{ $staffUser->name }}"
                                                                title="Delete account"
                                                                class="inline-grid size-8 place-items-center rounded-md text-slate-500 hover:bg-red-50 hover:text-red-700 focus-visible:outline-2 focus-visible:outline-red-600 dark:text-slate-400 dark:hover:bg-red-950 dark:hover:text-red-300"
                                                            >
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-4">
                                                                    <path d="M3 6h18" />
                                                                    <path d="M8 6V4h8v2" />
                                                                    <path d="m19 6-1 14H6L5 6" />
                                                                    <path d="M10 11v5M14 11v5" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @elseif ($actor?->id === $staffUser->id)
                                                        <button
                                                            type="button"
                                                            disabled
                                                            aria-label="You cannot delete your own account"
                                                            title="You cannot delete your own account"
                                                            class="inline-grid size-8 cursor-not-allowed place-items-center rounded-md text-slate-300 dark:text-slate-600"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="size-4">
                                                                <path d="M3 6h18" />
                                                                <path d="M8 6V4h8v2" />
                                                                <path d="m19 6-1 14H6L5 6" />
                                                                <path d="M10 11v5M14 11v5" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="mt-5">{{ $users->links() }}</div>
                @endif
            @endif
        </x-admin.panel>

        <dialog
            x-ref="createStaffDialog"
            id="create-staff-dialog"
            aria-labelledby="create-staff-title"
            class="{{ $dialogClasses }}"
            x-on:close="$refs.addStaffButton.focus()"
        >
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                <h2 id="create-staff-title" class="text-lg font-semibold">Add Staff Account</h2>
                <button type="button" x-on:click="$refs.createStaffDialog.close()" aria-label="Close add staff account dialog" class="grid size-8 place-items-center rounded-md text-xl text-slate-500 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:hover:bg-slate-800 dark:hover:text-white">&times;</button>
            </div>

            <form method="POST" action="{{ route('staff.store') }}">
                @csrf
                <input type="hidden" name="_staff_form" value="create">
                <div class="space-y-4 px-5 py-5">
                    @if ($errors->any() && old('_staff_form') === 'create')
                        <div role="alert" class="rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-200">
                            <ul class="list-inside list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <label class="grid gap-1.5 text-sm font-medium">
                        Name
                        <input name="name" type="text" maxlength="255" required value="{{ old('_staff_form') === 'create' ? old('name') : '' }}" class="{{ $fieldClasses }}">
                    </label>
                    <label class="grid gap-1.5 text-sm font-medium">
                        Email
                        <input name="email" type="email" maxlength="255" required value="{{ old('_staff_form') === 'create' ? old('email') : '' }}" class="{{ $fieldClasses }}">
                    </label>
                    <label class="grid gap-1.5 text-sm font-medium">
                        User type
                        <select name="user_type" required class="{{ $fieldClasses }}">
                            @foreach ($assignableTypes as $type)
                                <option value="{{ $type->value }}" @selected(old('_staff_form') === 'create' && old('user_type', 'admin') === $type->value)>{{ str($type->value)->headline() }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid gap-1.5 text-sm font-medium">
                        Temporary password
                        <input name="password" type="password" minlength="12" required autocomplete="new-password" class="{{ $fieldClasses }}">
                        <span class="text-xs font-normal text-slate-500 dark:text-slate-400">At least 12 characters. The user must verify their email before signing in to the panel.</span>
                    </label>
                    <label class="grid gap-1.5 text-sm font-medium">
                        Confirm temporary password
                        <input name="password_confirmation" type="password" minlength="12" required autocomplete="new-password" class="{{ $fieldClasses }}">
                    </label>
                </div>
                <div class="flex justify-end gap-2 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                    <button type="button" x-on:click="$refs.createStaffDialog.close()" class="{{ $secondaryButtonClasses }}">Cancel</button>
                    <button type="submit" class="{{ $primaryButtonClasses }}">Create Account</button>
                </div>
            </form>
        </dialog>

        @foreach ($users as $staffUser)
            @php
                $canEditProfile = $actor !== null
                    && ($actor->user_type === \App\Enums\UserType::Developer->value
                        || $staffUser->user_type !== \App\Enums\UserType::Developer->value);
                $canChangeRole = $actor !== null
                    && $actor->id !== $staffUser->id
                    && ($actor->user_type === \App\Enums\UserType::Developer->value
                        || $staffUser->user_type !== \App\Enums\UserType::Developer->value);
                $isSelfProfile = $actor !== null && $actor->is($staffUser);
                $editFormId = 'edit:'.$staffUser->id;
            @endphp
            @if ($canEditProfile)
                <dialog
                    x-ref="editStaffDialog{{ $staffUser->id }}"
                    id="edit-staff-dialog-{{ $staffUser->id }}"
                    aria-labelledby="edit-staff-title-{{ $staffUser->id }}"
                    class="{{ $dialogClasses }}"
                    @if ($errors->any() && old('_staff_form') === $editFormId)
                        x-init="$nextTick(() => $refs['editStaffDialog{{ $staffUser->id }}'].showModal())"
                    @endif
                    x-on:close="$refs['editStaffButton{{ $staffUser->id }}'].focus()"
                >
                    <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                        <h2 id="edit-staff-title-{{ $staffUser->id }}" class="text-lg font-semibold">Edit profile</h2>
                        <button type="button" x-on:click="$refs['editStaffDialog{{ $staffUser->id }}'].close()" aria-label="Close edit staff role dialog" class="grid size-8 place-items-center rounded-md text-xl text-slate-500 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:hover:bg-slate-800 dark:hover:text-white">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('staff.update', $staffUser) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="_staff_form" value="{{ $editFormId }}">
                        <div class="space-y-4 px-5 py-5">
                            @if ($errors->any() && old('_staff_form') === $editFormId)
                                <div role="alert" class="rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-200">
                                    <ul class="list-inside list-disc">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <label class="grid gap-1.5 text-sm font-medium">
                                Name
                                <input name="name" type="text" maxlength="255" required value="{{ old('_staff_form') === $editFormId ? old('name', $staffUser->name) : $staffUser->name }}" class="{{ $fieldClasses }}">
                            </label>
                            <label class="grid gap-1.5 text-sm font-medium">
                                Email
                                <input name="email" type="email" maxlength="255" required value="{{ old('_staff_form') === $editFormId ? old('email', $staffUser->email) : $staffUser->email }}" class="{{ $fieldClasses }}">
                                <span class="text-xs font-normal text-slate-500 dark:text-slate-400">Changing the email address requires verifying it again.</span>
                            </label>
                            @if ($isSelfProfile)
                                <fieldset class="space-y-4 rounded-lg border border-slate-200 p-4 dark:border-slate-700">
                                    <legend class="px-1 text-sm font-semibold">Change your password</legend>
                                    <p class="text-xs font-normal text-slate-500 dark:text-slate-400">Leave these fields blank to keep your current password. Your current password is required to save a new one.</p>
                                    <label class="grid gap-1.5 text-sm font-medium">
                                        Current password
                                        <input name="current_password" type="password" autocomplete="current-password" class="{{ $fieldClasses }}">
                                    </label>
                                    <label class="grid gap-1.5 text-sm font-medium">
                                        New password
                                        <input name="password" type="password" minlength="12" autocomplete="new-password" class="{{ $fieldClasses }}">
                                    </label>
                                    <label class="grid gap-1.5 text-sm font-medium">
                                        Confirm new password
                                        <input name="password_confirmation" type="password" minlength="12" autocomplete="new-password" class="{{ $fieldClasses }}">
                                    </label>
                                </fieldset>
                            @endif
                            @if ($canChangeRole)
                                <label class="grid gap-1.5 text-sm font-medium">
                                    User type
                                    <select name="user_type" required class="{{ $fieldClasses }}">
                                        @foreach ($assignableTypes as $type)
                                            <option value="{{ $type->value }}" @selected(old('_staff_form') === $editFormId && old('user_type', $staffUser->user_type) === $type->value)>{{ str($type->value)->headline() }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            @else
                                <p class="text-sm text-slate-600 dark:text-slate-300">User type: {{ str($staffUser->user_type)->headline() }} (you cannot change your own role)</p>
                            @endif
                        </div>
                        <div class="flex justify-end gap-2 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                            <button type="button" x-on:click="$refs['editStaffDialog{{ $staffUser->id }}'].close()" class="{{ $secondaryButtonClasses }}">Cancel</button>
                            <button type="submit" class="{{ $primaryButtonClasses }}">Save changes</button>
                        </div>
                    </form>
                </dialog>
            @endif
        @endforeach
    </div>
</x-admin.settings>
