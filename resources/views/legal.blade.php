<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | Summit Academy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&family=Fredoka:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { background:#f3efff; color:#211642; direction:ltr; font-family:'DM Sans',Arial,sans-serif; margin:0; }
        main { margin:0 auto; max-width:760px; padding:90px 24px; }
        a { color:#663bd3; font-weight:700; }
        h1 { font-family:'Fredoka',sans-serif; font-size:clamp(2.6rem,7vw,4.5rem); letter-spacing:-.06em; line-height:.95; margin:0 0 1.2rem; }
        p { color:#615b75; font-size:1.05rem; line-height:1.75; max-width:650px; }
        .brand { color:#211642; display:inline-block; font-family:'Fredoka',sans-serif; font-size:1.25rem; margin-bottom:70px; text-decoration:none; }.brand span { color:#ff754a; }
        .back { border:2px solid #211642; border-radius:999px; color:#211642; display:inline-block; margin-top:25px; padding:.72rem 1.1rem; text-decoration:none; }.back:hover { background:#ffc94d; }
    </style>
</head>
<body>
    <main><a class="brand" href="{{ route('home') }}">Summit<span>.</span> Academy</a><h1>{{ $heading }}</h1><p>{{ $content }}</p><a class="back" href="{{ route('home') }}">← Back to Summit Academy</a></main>
</body>
</html>
