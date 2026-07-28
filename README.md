# School System

نظام إدارة مدرسة مبني على Laravel 12 مع دعم أدوار المستخدمين: مشرف، معلّم، وطالب.

## نبذة عن المشروع

هذا المشروع هو تطبيق ويب يساعد الإدارة على إدارة الطلاب، المعلمين، المواد، الصفوف، الجداول، والتقارير داخل بيئة مدرسية. يدعم:

- تسجيل دخول المستخدمين وأدوار متعددة (admin، teacher، student)
- إدارة الطلاب، المعلمين، المواد، والمستويات الدراسية
- تنظيم الجداول الدراسية والمهام الخاصة بالمعلمين
- متابعة التقدّم الدراسي للطلاب
- إنشاء وتحميل تقارير الأداء
- نظام إشعارات داخلي لكل دور

## المتطلبات

- PHP 8.2 أو أحدث
- Composer
- Node.js و npm
-  Mysql

## تثبيت المشروع

1. انسخ ملف البيئة:

```bash
cp .env.example .env
```

2. ثبّت الاعتمادات:

```bash
composer install
npm install
```

3. أنشئ مفتاح التطبيق:

```bash
php artisan key:generate
```

4. أنشئ قاعدة بيانات Mysql إذا كنت تستخدم Mysql:

```bash
touch database/database.sqlite
```

5. شغّل الترحيلات:

```bash
php artisan migrate --force
```

6. أنشئ البناء الأمامي:

```bash
npm run build
```

## تشغيل بيئة التطوير

لتشغيل التطبيق محلياً:

```bash
php artisan serve
npm run dev
```

## الأدوار والصفحات الرئيسية

- `admin` - لوحة تحكّم لإدارة الطلاب، المعلمين، المواد، الصفوف، الجداول، التقارير، والإشعارات.
- `teacher` - عرض الطلاب، تسجيل التقدّم، الاطّلاع على السجلات، وتنبيهات الإشعارات.
- `student` - عرض الأداء، جدول الحصص، التقارير الشخصية، والإشعارات.

## اختبارات

لتشغيل الاختبارات:

```bash
php artisan test
```

## بنية المشروع الأساسية

- `app/Http/Controllers` - متحكمات المنافذ الرئيسية لكل دور
- `app/Models` - نماذج البيانات مثل Student، Teacher، Subject، GradeLevel
- `routes/web.php` - تعريف مسارات الويب وأذونات الوصول
- `database/migrations` - إعدادات جداول قاعدة البيانات
- `resources/views` - واجهات المستخدم وملفات Blade

## أوامر مفيدة

- إعداد المشروع مرة واحدة:

```bash
npm run setup
```

- تشغيل خادم التطوير:

```bash
npm run dev
```

- بناء الواجهة الأمامية:

```bash
npm run build
```

## الاعتمادات

- Laravel 12
- Livewire 3.6.4
- Spatie Permission
- Tailwind CSS
- Laravel Vite Plugin
- Dompdf و MPDF

