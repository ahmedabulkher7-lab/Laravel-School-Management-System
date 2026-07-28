<?php
namespace App\Livewire\Shared;

use Livewire\Component;

class NotificationBell extends Component
{
    public bool $open = false;

    public function toggleOpen(): void
    {
        $this->open = !$this->open;
    }

    public function markAllRead(): void
    {
        auth()->user()?->unreadNotifications->markAsRead();
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.shared.notification-bell', [
            'unreadCount'   => auth()->user()?->unreadNotifications()->count() ?? 0,
            'notifications' => auth()->user()?->notifications()->latest()->limit(10)->get() ?? collect(),
        ]);
    }
}
