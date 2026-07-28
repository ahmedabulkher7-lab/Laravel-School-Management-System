<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة المدير') — Summit</title>
    <meta name="description" content="لوحة تحكم المدير - نظام إدارة المدرسة الإلكترونية">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body style="display:flex;min-height:100vh;">

    <!-- Sidebar (right for RTL) -->
    <aside class="sidebar">
        <!-- Logo & branding -->
        <div style="text-align:center;padding:0.5rem 0 1.75rem;">
            <img src="{{ asset('images/logo.png') }}" alt="شعار المدرسة"
                 style="width:68px;height:68px;object-fit:contain;margin:0 auto 0.75rem;display:block;
                        filter:drop-shadow(0 2px 8px rgba(12, 114, 97, 0.15));">
            <div style="font-size:0.88rem;font-weight:800;color:#0C7261;letter-spacing:0.02em;">
                ساميت التعليمية
            </div>
            <div style="font-size:0.7rem;color:#475569;margin-top:0.2rem;font-weight:700;">لوحة المدير</div>
        </div>

        <!-- Navigation -->
        <nav style="flex:1;">
            <div class="section-title">الرئيسية</div>
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> لوحة التحكم
            </a>

            <div class="section-title">إدارة المستخدمين</div>
            <a href="{{ route('admin.students.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i> الطلاب
            </a>
            <a href="{{ route('admin.teachers.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                <i class="fas fa-chalkboard-teacher"></i> المعلمون
            </a>

            <div class="section-title">الإعدادات الأكاديمية</div>
            <a href="{{ route('admin.subjects.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                <i class="fas fa-book"></i> المواد الدراسية
            </a>
            <a href="{{ route('admin.grade-levels.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.grade-levels.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i> المراحل الدراسية
            </a>
            <a href="{{ route('admin.assignments.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}">
                <i class="fas fa-link"></i> التعيينات
            </a>
            <a href="{{ route('admin.schedules.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> الجداول الدراسية
            </a>

            <div class="section-title">المتابعة</div>
            <a href="{{ route('admin.progress.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.progress.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> سجلات التقدم
            </a>
            <a href="{{ route('admin.reports.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-pdf"></i> التقارير الأسبوعية
            </a>
        </nav>

        <!-- User footer -->
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
                    <div style="font-size:0.68rem;color:#475569;font-weight:600;">مدير النظام</div>
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

    <!-- Main Content -->
    <main style="margin-right:260px;flex:1;padding:2rem;min-height:100vh;max-width:calc(100vw - 260px);">

        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
