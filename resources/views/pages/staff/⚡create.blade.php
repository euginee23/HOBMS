<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Add Staff Member')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'receptionist';

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:' . implode(',', array_column(UserRole::cases(), 'value'))],
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ]);

        session()->flash('success', 'Staff member created successfully.');
        $this->redirect(route('staff.index'), navigate: true);
    }
}; ?>

<div>
    <div class="space-y-6">
        <div>
            <flux:button variant="ghost" size="sm" :href="route('staff.index')" wire:navigate icon="arrow-left">Back to Staff</flux:button>
            <flux:heading size="xl" class="mt-2">Add Staff Member</flux:heading>
        </div>

        <form wire:submit="save" class="max-w-xl space-y-6">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 space-y-4">
                <flux:input wire:model="name" label="Full Name" placeholder="John Doe" required />
                <flux:input wire:model="email" label="Email Address" type="email" placeholder="john@hobms.test" required />

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:input wire:model="password" label="Password" type="password" required />
                    <flux:input wire:model="password_confirmation" label="Confirm Password" type="password" required />
                </div>

                <flux:select wire:model="role" label="Role" required>
                    @foreach(UserRole::cases() as $userRole)
                        <flux:select.option value="{{ $userRole->value }}">{{ $userRole->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="flex gap-3">
                <flux:button type="submit" variant="primary">Create Staff Member</flux:button>
                <flux:button variant="ghost" :href="route('staff.index')" wire:navigate>Cancel</flux:button>
            </div>
        </form>
    </div>
</div>
