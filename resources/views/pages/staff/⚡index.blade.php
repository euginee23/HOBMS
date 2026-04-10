<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Staff Management')] class extends Component {
    public string $search = '';
    public bool $showDeleteModal = false;
    public ?int $deletingUserId = null;

    public function confirmDelete(int $userId): void
    {
        $this->deletingUserId = $userId;
        $this->showDeleteModal = true;
    }

    public function deleteStaff(): void
    {
        $user = User::findOrFail($this->deletingUserId);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            $this->showDeleteModal = false;

            return;
        }

        $user->delete();

        $this->showDeleteModal = false;
        $this->deletingUserId = null;
        session()->flash('success', 'Staff member deleted successfully.');
    }

    public function with(): array
    {
        $staff = User::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();

        return compact('staff');
    }
}; ?>

<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Staff Management</flux:heading>
            <flux:button variant="primary" :href="route('staff.create')" wire:navigate icon="plus">Add Staff</flux:button>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <flux:input wire:model.live.debounce="search" placeholder="Search by name or email..." icon="magnifying-glass" />

        <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-zinc-500 uppercase dark:text-zinc-400">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-zinc-500 uppercase dark:text-zinc-400">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-zinc-500 uppercase dark:text-zinc-400">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-zinc-500 uppercase dark:text-zinc-400">Joined</th>
                        <th class="px-4 py-3 text-right text-xs font-medium tracking-wider text-zinc-500 uppercase dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
                    @forelse($staff as $member)
                        <tr wire:key="staff-{{ $member->id }}">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-900 dark:text-white">{{ $member->name }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $member->email }}</td>
                            <td class="whitespace-nowrap px-4 py-3"><flux:badge :color="$member->role->color()">{{ $member->role->label() }}</flux:badge></td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $member->created_at->format('M d, Y') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:button variant="ghost" size="xs" :href="route('staff.edit', $member)" wire:navigate icon="pencil-square" />
                                    @if($member->id !== auth()->id())
                                        <flux:button variant="ghost" size="xs" wire:click="confirmDelete({{ $member->id }})" icon="trash" class="text-red-600 hover:text-red-700 dark:text-red-400" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-zinc-500">No staff members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model="showDeleteModal">
        <div class="space-y-4">
            <flux:heading size="lg">Delete Staff Member</flux:heading>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Are you sure you want to delete this staff member? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="$set('showDeleteModal', false)">Cancel</flux:button>
                <flux:button variant="danger" wire:click="deleteStaff">Delete</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
