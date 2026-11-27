<?php

namespace App\Http\Controllers;

use App\Concerns\PasswordValidationRules;
use App\Enums\UserType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffUserController extends Controller
{
    use PasswordValidationRules;

    public function index(Request $request): View
    {
        $actorType = $this->actorType($request);

        return view('admin.settings.users.index', [
            'users' => User::query()->orderBy('name')->orderBy('id')->paginate(20),
            'assignableTypes' => $actorType->assignableTypes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $actorType = $this->actorType($request);
        $request->merge([
            'email' => is_string($request->input('email'))
                ? mb_strtolower(trim($request->input('email')))
                : $request->input('email'),
        ]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'user_type' => ['required', Rule::in($this->assignableValues($actorType))],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $user = User::query()->create($data);
        $user->sendEmailVerificationNotification();

        return to_route('staff.index')->with('status', __('Staff account created. A verification email has been sent.'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $actorType = $this->actorType($request);
        $isSelf = $user->is($request->user());
        abort_if(
            $actorType === UserType::Superadmin && $user->user_type === UserType::Developer->value,
            403,
        );

        $request->merge([
            'email' => is_string($request->input('email'))
                ? mb_strtolower(trim($request->input('email')))
                : $request->input('email'),
        ]);
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
        ];

        if (! $isSelf) {
            $rules['user_type'] = ['required', Rule::in($this->assignableValues($actorType))];
            $rules['current_password'] = ['prohibited'];
            $rules['password'] = ['prohibited'];
            $rules['password_confirmation'] = ['prohibited'];
        } elseif (
            $request->filled('current_password')
            || $request->filled('password')
            || $request->filled('password_confirmation')
        ) {
            $rules['current_password'] = $this->currentPasswordRules();
            $rules['password'] = $this->passwordRules();
        }

        $data = $request->validate($rules);
        $emailChanged = $user->email !== $data['email'];

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (! $isSelf) {
            $user->user_type = $data['user_type'];
        }
        if ($emailChanged) {
            $user->email_verified_at = null;
        }
        if ($isSelf && isset($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        $message = $emailChanged
            ? __('Staff profile updated. A verification email has been sent to the new address.')
            : __('Staff profile updated.');

        return to_route('staff.index')->with('status', $message);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $actorType = $this->actorType($request);

        abort_if($user->is($request->user()), 403);
        abort_if(
            $actorType === UserType::Superadmin && $user->user_type === UserType::Developer->value,
            403,
        );

        $user->delete();

        return to_route('staff.index')->with('status', __('Staff account deleted.'));
    }

    private function actorType(Request $request): UserType
    {
        $userType = UserType::tryFrom((string) $request->user()?->user_type);

        if ($userType?->canManageStaff()) {
            return $userType;
        }

        if (
            config('preview.enabled')
            && app()->environment(['local', 'testing'])
            && $request->session()->has('preview.user')
        ) {
            return UserType::Developer;
        }

        abort(403);
    }

    /** @return list<string> */
    private function assignableValues(UserType $actorType): array
    {
        return array_map(
            static fn (UserType $type): string => $type->value,
            $actorType->assignableTypes(),
        );
    }
}
