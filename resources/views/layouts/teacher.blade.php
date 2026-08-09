<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة المعلم') — Summit</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body style="display:flex;min-height:100vh;">

    <aside class="sidebar">
        <div style="text-align:center;padding:0.5rem 0 1.75rem;">
            <img src="{{ asset('images/logo.png') }}" alt="شعار المدرسة"
                 style="width:68px;height:68px;object-fit:contain;margin:0 auto 0.75rem;display:block;
                        filter:drop-shadow(0 2px 8px rgba(12, 114, 97, 0.15));">
            <div style="font-size:0.88rem;font-weight:800;color:#0C7261;">ساميت التعليمية</div>
            <div style="font-size:0.7rem;color:#475569;margin-top:0.2rem;font-weight:700;">لوحة المعلم</div>
        </div>

        <nav style="flex:1;">
            <div class="section-title">الرئيسية</div>
            <a href="{{ route('teacher.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> الرئيسية
            </a>

            <div class="section-title">الطلاب والتقدم</div>
            <a href="{{ route('teacher.students.index') }}"
               class="sidebar-link {{ request()->routeIs('teacher.students.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> طلابي
            </a>
            <a href="{{ route('teacher.progress.log') }}"
               class="sidebar-link {{ request()->routeIs('teacher.progress.log') ? 'active' : '' }}">
                <i class="fas fa-edit"></i> تسجيل التقدم اليومي
            </a>
            <a href="{{ route('teacher.progress.history') }}"
               class="sidebar-link {{ request()->routeIs('teacher.progress.history') ? 'active' : '' }}">
                <i class="fas fa-history"></i> السجل التاريخي
            </a>
            <a href="{{ route('teacher.weekly-plans.index') }}"
               class="sidebar-link {{ request()->routeIs('teacher.weekly-plans.*') ? 'active' : '' }}">
                <i class="fas fa-table"></i> الجدول الأسبوعي
            </a>
        </nav>

        <div style="border-top:1px solid #e2e8f0;padding-top:1rem;margin-top:0.5rem;">
            <div style="display:flex;align-items:center;gap:0.65rem;margin-bottom:0.75rem;">
                <div style="width:34px;height:34px;border-radius:50%;
                            background:#0C7261;
                            display:flex;align-items:center;justify-content:center;
                            color:#F8EB2F;font-weight:700;font-size:0.8rem;flex-shrink:0;">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.82rem;font-weight:700;color:#0C7261;
                                overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ auth()->user()->name }}
                    </div>
                    <div style="font-size:0.68rem;color:#475569;font-weight:600;">معلم</div>
                </div>
                @livewire('shared.notification-bell')
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link"
                        style="width:100%;background:none;border:none;cursor:pointer;
                               color:#dc2626;font-family:inherit;text-align:right;">
                    <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    <main style="margin-right:260px;flex:1;padding:2rem;min-height:100vh;max-width:calc(100vw - 260px);">
        @if(session('success'))
            <div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
