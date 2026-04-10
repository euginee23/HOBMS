<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit Staff Member')] class extends Component {
    public User $user;

    public string $name = '';
    public string $email = '';
    public string $role = '';

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
            'role' => ['required', 'in:' . implode(',', array_column(UserRole::cases(), 'value'))],
        ]);

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ]);

        session()->flash('success', 'Staff member updated successfully.');
        $this->redirect(route('staff.index'), navigate: true);
    }
}; ?>

<div>
    <div class="space-y-6">
        <div>
            <flux:button variant="ghost" size="sm" :href="route('staff.index')" wire:navigate icon="arrow-left">Back to Staff</flux:button>
            <flux:heading size="xl" class="mt-2">Edit Staff Member</flux:heading>
        </div>

        <form wire:submit="save" class="max-w-xl space-y-6">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 space-y-4">
                <flux:input wire:model="name" label="Full Name" required />
                <flux:input wire:model="email" label="Email Address" type="email" required />

                <flux:select wire:model="role" label="Role" required>
                    @foreach(UserRole::cases() as $userRole)
                        <flux:select.option value="{{ $userRole->value }}">{{ $userRole->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="flex gap-3">
                <flux:button type="submit" variant="primary">Update Staff Member</flux:button>
                <flux:button variant="ghost" :href="route('staff.index')" wire:navigate>Cancel</flux:button>
            </div>
        </form>
    </div>
</div>
