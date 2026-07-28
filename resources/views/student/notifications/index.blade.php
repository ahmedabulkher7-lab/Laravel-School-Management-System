@extends('layouts.student')
@section('title', 'كل الإشعارات')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">جميع الإشعارات</h1>
    </div>
</div>

<div class="card" style="max-width:800px;">
    <div class="card-body" style="padding:0;">
        @forelse($notifications as $notif)
            <div style="padding:1rem 1.5rem;border-bottom:1px solid rgba(12, 114, 97, 0.2);display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="color:{{ is_null($notif->read_at) ? '#0C7261' : '#475569' }};font-weight:{{ is_null($notif->read_at) ? '600' : '400' }};">
                        {{ $notif->data['message'] ?? '' }}
                    </div>
                    <div style="font-size:0.75rem;color:#475569;margin-top:0.3rem;">
                        {{ $notif->created_at->locale('ar')->diffForHumans() }}
                    </div>
                </div>
                @if(is_null($notif->read_at))
                <a href="{{ route('student.notifications.read', $notif->id) }}" class="btn-secondary" style="padding:0.3rem 0.6rem;font-size:0.75rem;">
                    تحديد كمقروء
                </a>
                @endif
            </div>
        @empty
            <div style="padding:3rem;text-align:center;color:#475569;">لا توجد إشعارات.</div>
        @endforelse
    </div>
    @if($notifications->hasPages())
    <div style="padding:1rem;border-top:1px solid rgba(12, 114, 97, 0.2);">
        {{ $notifications->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
