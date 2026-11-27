<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('developers can create staff accounts and assign all user types', function () {
    Notification::fake();
    $developer = User::factory()->create(['user_type' => 'developer']);

    $this->actingAs($developer)
        ->post(route('staff.store'), [
            'name' => 'New Administrator',
            'email' => '  new.admin@example.com ',
            'user_type' => 'admin',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
        ])
        ->assertRedirect(route('staff.index'))
        ->assertSessionHas('status');

    $staff = User::query()->where('email', 'new.admin@example.com')->sole();

    expect($staff->user_type)->toBe('admin')
        ->and($staff->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($staff, VerifyEmail::class);
});

test('superadmins cannot assign the developer role', function () {
    $superadmin = User::factory()->create(['user_type' => 'superadmin']);

    $this->actingAs($superadmin)
        ->from(route('staff.index'))
        ->post(route('staff.store'), [
            'name' => 'Developer Account',
            'email' => 'developer@example.com',
            'user_type' => 'developer',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
        ])
        ->assertRedirect(route('staff.index'))
        ->assertSessionHasErrors(['user_type']);

    $this->assertDatabaseMissing('users', ['email' => 'developer@example.com']);
});

test('admins cannot access staff management or create staff accounts', function () {
    $admin = User::factory()->create(['user_type' => 'admin']);

    $this->actingAs($admin)
        ->get(route('staff.index'))
        ->assertForbidden();

    $this->post(route('staff.store'), [
        'name' => 'Blocked Account',
        'email' => 'blocked@example.com',
        'user_type' => 'admin',
        'password' => 'a-strong-password',
        'password_confirmation' => 'a-strong-password',
    ])->assertForbidden();

    $this->assertDatabaseMissing('users', ['email' => 'blocked@example.com']);
});

test('superadmins cannot change developer accounts', function () {
    $superadmin = User::factory()->create(['user_type' => 'superadmin']);
    $developer = User::factory()->create(['user_type' => 'developer']);

    $this->actingAs($superadmin)
        ->patch(route('staff.update', $developer), ['user_type' => 'admin'])
        ->assertForbidden();

    expect($developer->refresh()->user_type)->toBe('developer');
});

test('staff users can edit their own profile but cannot change their own role', function () {
    $developer = User::factory()->create(['user_type' => 'developer']);

    $this->actingAs($developer)
        ->patch(route('staff.update', $developer), [
            'name' => 'Updated Developer',
            'email' => $developer->email,
            'user_type' => 'admin',
        ])
        ->assertRedirect(route('staff.index'))
        ->assertSessionHas('status', 'Staff profile updated.');

    expect($developer->refresh()->name)->toBe('Updated Developer')
        ->and($developer->user_type)->toBe('developer');
});

test('staff account actions use dialogs with add account in the staff header', function () {
    $developer = User::factory()->create(['user_type' => 'developer']);
    $staff = User::factory()->create(['name' => 'Alex Admin', 'user_type' => 'admin']);

    $html = $this->actingAs($developer)
        ->get(route('staff.index'))
        ->assertOk()
        ->assertSee('id="create-staff-dialog"', false)
        ->assertSee('id="edit-staff-dialog-'.$developer->id.'"', false)
        ->assertSee('id="edit-staff-dialog-'.$staff->id.'"', false)
        ->assertSee('aria-label="Edit profile for '.$developer->name.'"', false)
        ->assertSee('aria-label="You cannot delete your own account"', false)
        ->assertSee('aria-label="Edit profile for '.$staff->name.'"', false)
        ->assertSee('aria-label="Delete account for '.$staff->name.'"', false)
        ->assertSee('action="'.route('staff.destroy', $staff).'"', false)
        ->assertSee("confirm('Delete this staff account? This cannot be undone.')", false)
        ->assertDontSee('Email status')
        ->assertSee('name="_staff_form" value="create"', false)
        ->assertSee('name="_staff_form" value="edit:'.$staff->id.'"', false)
        ->getContent();

    expect($html)->toMatch('/<header[^>]*>.*?<h2[^>]*>Staff Accounts<\/h2>.*?<button[^>]*>Add Account<\/button>/s');
    expect($html)->toMatch('/<td[^>]*>\s*<div[^>]*>\s*<span>Developer<\/span>.*?aria-label="Edit profile for '.$developer->name.'".*?aria-label="You cannot delete your own account".*?<\/div>\s*<\/td>/s');

    preg_match('/<dialog[^>]*id="edit-staff-dialog-'.$developer->id.'"[^>]*>(.*?)<\/dialog>/s', $html, $selfDialog);

    expect($selfDialog[1])
        ->toContain('name="name"')
        ->toContain('name="email"')
        ->toContain('name="current_password"')
        ->toContain('name="password"')
        ->toContain('name="password_confirmation"')
        ->not->toContain('name="user_type"');

    preg_match('/<dialog[^>]*id="edit-staff-dialog-'.$staff->id.'"[^>]*>(.*?)<\/dialog>/s', $html, $staffDialog);

    expect($staffDialog[1])
        ->not->toContain('name="current_password"')
        ->not->toContain('name="password"')
        ->not->toContain('name="password_confirmation"');
});

test('developers can delete other staff accounts', function () {
    $developer = User::factory()->create(['user_type' => 'developer']);
    $staff = User::factory()->create(['user_type' => 'admin']);

    $this->actingAs($developer)
        ->delete(route('staff.destroy', $staff))
        ->assertRedirect(route('staff.index'))
        ->assertSessionHas('status', 'Staff account deleted.');

    $this->assertDatabaseMissing('users', ['id' => $staff->id]);
});

test('staff users cannot delete their own account', function () {
    $developer = User::factory()->create(['user_type' => 'developer']);

    $this->actingAs($developer)
        ->delete(route('staff.destroy', $developer))
        ->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $developer->id]);
});

test('superadmins cannot delete developer accounts', function () {
    $superadmin = User::factory()->create(['user_type' => 'superadmin']);
    $developer = User::factory()->create(['user_type' => 'developer']);

    $this->actingAs($superadmin)
        ->delete(route('staff.destroy', $developer))
        ->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $developer->id]);
});

test('editing a staff profile can change its name email and assignable role', function () {
    Notification::fake();
    $developer = User::factory()->create(['user_type' => 'developer']);
    $staff = User::factory()->create(['user_type' => 'admin']);

    $this->actingAs($developer)
        ->patch(route('staff.update', $staff), [
            '_staff_form' => 'edit:'.$staff->id,
            'name' => 'Alex Manager',
            'email' => '  alex.manager@example.com ',
            'user_type' => 'superadmin',
        ])
        ->assertRedirect(route('staff.index'))
        ->assertSessionHas('status', 'Staff profile updated. A verification email has been sent to the new address.');

    $staff->refresh();

    expect($staff->name)->toBe('Alex Manager')
        ->and($staff->email)->toBe('alex.manager@example.com')
        ->and($staff->user_type)->toBe('superadmin')
        ->and($staff->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($staff, VerifyEmail::class);
});

test('staff users can change their own password after confirming the current password', function () {
    $developer = User::factory()->create([
        'user_type' => 'developer',
        'password' => Hash::make('current-password'),
    ]);

    $this->actingAs($developer)
        ->patch(route('staff.update', $developer), [
            'name' => $developer->name,
            'email' => $developer->email,
            'current_password' => 'current-password',
            'password' => 'replacement-password',
            'password_confirmation' => 'replacement-password',
        ])
        ->assertRedirect(route('staff.index'))
        ->assertSessionHas('status', 'Staff profile updated.');

    expect(Hash::check('replacement-password', $developer->refresh()->password))->toBeTrue();
});

test('staff users must provide their current password to change it', function () {
    $developer = User::factory()->create([
        'user_type' => 'developer',
        'password' => Hash::make('current-password'),
    ]);

    $this->actingAs($developer)
        ->from(route('staff.index'))
        ->patch(route('staff.update', $developer), [
            '_staff_form' => 'edit:'.$developer->id,
            'name' => $developer->name,
            'email' => $developer->email,
            'current_password' => 'incorrect-password',
            'password' => 'replacement-password',
            'password_confirmation' => 'replacement-password',
        ])
        ->assertRedirect(route('staff.index'))
        ->assertSessionHasErrors(['current_password']);

    expect(Hash::check('current-password', $developer->refresh()->password))->toBeTrue();
});

test('staff users cannot change another account password', function () {
    $developer = User::factory()->create(['user_type' => 'developer']);
    $staff = User::factory()->create([
        'password' => Hash::make('current-password'),
    ]);

    $this->actingAs($developer)
        ->from(route('staff.index'))
        ->patch(route('staff.update', $staff), [
            '_staff_form' => 'edit:'.$staff->id,
            'name' => $staff->name,
            'email' => $staff->email,
            'user_type' => $staff->user_type,
            'password' => 'replacement-password',
            'password_confirmation' => 'replacement-password',
        ])
        ->assertRedirect(route('staff.index'))
        ->assertSessionHasErrors(['password', 'password_confirmation']);

    expect(Hash::check('current-password', $staff->refresh()->password))->toBeTrue();
});

test('invalid add account submission reopens only the create dialog', function () {
    $developer = User::factory()->create(['user_type' => 'developer']);

    $this->actingAs($developer)
        ->from(route('staff.index'))
        ->followingRedirects()
        ->post(route('staff.store'), [
            '_staff_form' => 'create',
            'name' => '',
            'email' => 'invalid',
            'user_type' => 'admin',
            'password' => 'short',
            'password_confirmation' => 'different',
        ])
        ->assertOk()
        ->assertSee('x-init="$nextTick(() => $refs.createStaffDialog.showModal())"', false)
        ->assertSee('The name field is required.');
});

test('invalid role update reopens that staff member dialog', function () {
    $developer = User::factory()->create(['user_type' => 'developer']);
    $staff = User::factory()->create(['user_type' => 'admin']);

    $this->actingAs($developer)
        ->from(route('staff.index'))
        ->followingRedirects()
        ->patch(route('staff.update', $staff), [
            '_staff_form' => 'edit:'.$staff->id,
        ])
        ->assertOk()
        ->assertSee('x-init="$nextTick(() => $refs[\'editStaffDialog'.$staff->id.'\'].showModal())"', false)
        ->assertSee('The user type field is required.');
});
