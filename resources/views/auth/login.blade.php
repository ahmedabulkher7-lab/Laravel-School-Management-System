<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - المدرسة الإلكترونية</title>
    <meta name="description" content="تسجيل الدخول إلى نظام إدارة المدرسة الإلكترونية">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
      .login-bg {
        background:
          radial-gradient(ellipse at 80% 10%, rgba(12, 114, 97, 0.1) 0%, transparent 55%),
          radial-gradient(ellipse at 10% 90%, rgba(248, 235, 47, 0.15) 0%, transparent 55%),
          #f8fafc;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        position: relative;
        overflow: hidden;
      }
      .login-bg::before {
        content: '';
        position: absolute;
        width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(12, 114, 97, 0.05) 0%, transparent 70%);
        top: -100px; right: -100px;
        border-radius: 50%;
        animation: pulse 4s ease-in-out infinite;
      }
      @keyframes pulse { 0%,100%{transform:scale(1);} 50%{transform:scale(1.1);} }
      .login-card {
        width: 100%;
        max-width: 440px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(12, 114, 97, 0.2);
        border-radius: 1.5rem;
        padding: 2.75rem;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(255,255,255,0.5);
        position: relative;
        z-index: 1;
        animation: slideUp 0.4s ease;
      }
      @keyframes slideUp { from{opacity:0;transform:translateY(20px);} to{opacity:1;transform:translateY(0);} }
      .input-icon-wrap { position: relative; }
      .input-icon-wrap .icon {
        position: absolute;
        top: 50%; left: 1rem;
        transform: translateY(-50%);
        color: #0C7261;
        pointer-events: none;
      }
      .input-icon-wrap .form-input { padding-left: 2.75rem; }
    </style>
</head>
<body class="login-bg">
    <div class="login-card">
        <!-- Logo -->
        <div style="text-align:center; margin-bottom:2rem;">
            <img src="{{ asset('images/logo.png') }}" alt="شعار المدرسة"
                 style="width:100px;height:100px;object-fit:contain;margin:0 auto 1rem;display:block;
                        filter:drop-shadow(0 4px 12px rgba(12, 114, 97, 0.15));">
            <h1 style="font-size:1.6rem;font-weight:800;color:#0C7261;margin:0;">ساميت التعليمية</h1>
            <p style="color:#475569;font-size:0.95rem;margin-top:0.4rem;font-weight:600;">نظام إدارة التعليم</p>
        </div>

        @if($errors->any())
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope" style="color:#F8EB2F; text-shadow: 0 0 1px #000; margin-left:0.3rem;"></i>
                    البريد الإلكتروني
                </label>
                <div class="input-icon-wrap">
                    <i class="fas fa-at icon"></i>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           class="form-input"
                           placeholder="example@school.com"
                           required autofocus autocomplete="email">
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock" style="color:#F8EB2F; text-shadow: 0 0 1px #000; margin-left:0.3rem;"></i>
                    كلمة المرور
                </label>
                <div class="input-icon-wrap">
                    <i class="fas fa-key icon"></i>
                    <input id="password" type="password" name="password"
                           class="form-input"
                           placeholder="••••••••"
                           required autocomplete="current-password">
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1.75rem;">
                <input type="checkbox" id="remember" name="remember"
                       style="width:16px;height:16px;accent-color:#0C7261;cursor:pointer;">
                <label for="remember" style="color:#0C7261;font-size:0.875rem;cursor:pointer;font-weight:600;">
                    تذكرني
                </label>
            </div>

            <button id="login-submit-btn" type="submit" class="btn-primary"
                    style="width:100%;justify-content:center;padding:0.85rem;font-size:1rem;background:#0C7261;color:white;">
                <i class="fas fa-sign-in-alt" style="color:#F8EB2F;"></i>
                تسجيل الدخول
            </button>
        </form>

        <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid rgba(12, 114, 97, 0.2);
                    text-align:center;font-size:0.78rem;color:#475569;font-weight:600;">
            جميع الحقوق محفوظة &copy; {{ date('Y') }} — ساميت التعليمية
        </div>
    </div>
</body>
</html>
