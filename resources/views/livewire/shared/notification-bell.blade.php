<div style="position:relative;display:inline-block;">
    <button wire:click="toggleOpen"
            id="notification-bell-btn"
            style="background:none;border:none;cursor:pointer;position:relative;padding:0.3rem;">
        <i class="fas fa-bell" style="color:#475569;font-size:1.1rem;transition:color 0.2s;"
           onmouseover="this.style.color='#0C7261'" onmouseout="this.style.color='#475569'"></i>
        @if($unreadCount > 0)
            <span style="position:absolute;top:-3px;right:-3px;background:#ef4444;color:white;
                         border-radius:50%;width:18px;height:18px;font-size:0.65rem;
                         display:flex;align-items:center;justify-content:center;font-weight:800;
                         border:2px solid #ffffff;">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    @if($open)
    <div style="position:fixed;bottom:80px;right:275px;width:320px;
                background:#ffffff;border:1px solid #e2e8f0;
                border-radius:1rem;box-shadow:0 15px 50px rgba(0,0,0,0.1);
                z-index:9999;overflow:hidden;"
         wire:click.outside="toggleOpen">

        <!-- Header -->
        <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;
                    display:flex;justify-content:space-between;align-items:center;
                    background:#f8fafc;">
            <span style="font-weight:800;color:#0C7261;font-size:0.95rem;">
                <i class="fas fa-bell" style="color:#F8EB2F;margin-left:0.4rem;text-shadow:0 0 1px #000;"></i>
                الإشعارات
                @if($unreadCount > 0)
                    <span class="badge badge-yellow" style="margin-right:0.4rem;font-size:0.7rem;">
                        {{ $unreadCount }} جديد
                    </span>
                @endif
            </span>
            @if($unreadCount > 0)
                <button wire:click="markAllRead"
                        style="background:none;border:none;color:#0C7261;font-size:0.8rem;font-weight:700;
                               cursor:pointer;font-family:inherit;padding:0;text-decoration:underline;">
                    قراءة الكل
                </button>
            @endif
        </div>

        <!-- List -->
        <div style="max-height:350px;overflow-y:auto;background:#ffffff;">
            @forelse($notifications as $notif)
                <div style="padding:1rem 1.25rem;border-bottom:1px solid #f1f5f9;
                            transition:background 0.2s;cursor:pointer;
                            {{ is_null($notif->read_at) ? 'background:#f0fdf4;' : 'background:#ffffff;' }}"
                     onmouseover="this.style.background='{{ is_null($notif->read_at) ? '#dcfce7' : '#f8fafc' }}'"
                     onmouseout="this.style.background='{{ is_null($notif->read_at) ? '#f0fdf4' : '#ffffff' }}'">
                    
                    @if(is_null($notif->read_at))
                        <div style="width:8px;height:8px;border-radius:50%;background:#0C7261;
                                    float:right;margin:0.35rem 0 0 0.5rem;box-shadow:0 0 0 2px rgba(12,114,97,0.2);"></div>
                    @endif
                    <div style="font-size:0.85rem;font-weight:{{ is_null($notif->read_at) ? '700' : '600' }};
                                color:{{ is_null($notif->read_at) ? '#0C7261' : '#475569' }};
                                line-height:1.5;">
                        {{ $notif->data['message'] ?? '' }}
                    </div>
                    <div style="font-size:0.7rem;color:#94a3b8;margin-top:0.4rem;clear:both;font-weight:600;">
                        <i class="fas fa-clock" style="margin-left:0.2rem;"></i>
                        {{ $notif->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <div style="padding:3rem 2rem;text-align:center;color:#64748b;">
                    <i class="fas fa-bell-slash" style="font-size:3rem;display:block;margin-bottom:1rem;color:#cbd5e1;"></i>
                    <div style="font-size:0.9rem;font-weight:700;color:#475569;">لا توجد إشعارات جديدة</div>
                </div>
            @endforelse
        </div>
    </div>
    @endif
</div>
