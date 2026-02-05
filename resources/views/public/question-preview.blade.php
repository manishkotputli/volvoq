<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $question->title }}</title>

    {{-- SEO --}}
    @if($question->status !== 'published')
        <meta name="robots" content="noindex,nofollow">
    @else
        <meta name="robots" content="index,follow">
    @endif

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
          content="{{ Str::limit(strip_tags($question->body),160) }}">

    <style>
        /* =====================
           GLOBAL THEME
        ===================== */
        :root {
            --bg:#f6f7fb;
            --card:#ffffff;
            --text:#111827;
            --muted:#6b7280;
            --border:#e5e7eb;
            --primary:#4f46e5;
            --success:#16a34a;
            --warning:#d97706;

            /* CODE LIGHT */
            --code-bg:#f3f4f6;
            --code-text:#111827;
        }

        body.dark-code {
            --code-bg:#0f172a;
            --code-text:#e5e7eb;
        }

        * { box-sizing:border-box; }

        body {
            margin:0;
            font-family:system-ui,-apple-system,BlinkMacSystemFont,
                        "Segoe UI",Roboto,Ubuntu,Arial,sans-serif;
            background:var(--bg);
            color:var(--text);
            line-height:1.7;
        }

        .container {
            max-width:900px;
            margin:40px auto;
            padding:0 16px;
        }

        .card {
            background:var(--card);
            border:1px solid var(--border);
            border-radius:12px;
            padding:24px;
            margin-bottom:24px;
        }

        h1,h2,h3 { margin-top:0; }

        .question-title {
            font-size:1.8rem;
            font-weight:700;
            margin-bottom:10px;
            word-break:break-word;
        }

        .question-meta {
            display:flex;
            gap:12px;
            flex-wrap:wrap;
            font-size:.85rem;
            color:var(--muted);
            margin-bottom:16px;
        }

        .badge {
            padding:4px 10px;
            border-radius:999px;
            font-size:.75rem;
            font-weight:600;
            text-transform:uppercase;
        }

        .badge.review { background:#fef3c7; color:var(--warning); }
        .badge.published { background:#dcfce7; color:var(--success); }

        /* =====================
           CONTENT (HTML SAFE)
        ===================== */
        .content p { margin:0 0 12px; }
        .content ul, .content ol { padding-left:22px; }
        .content blockquote {
            border-left:4px solid var(--primary);
            padding-left:14px;
            color:var(--muted);
            margin:16px 0;
        }

        /* =====================
           CODE BLOCK SYSTEM
        ===================== */
        .code-box {
            background:var(--code-bg);
            color:var(--code-text);
            border-radius:10px;
            border:1px solid var(--border);
            margin:16px 0;
            overflow:hidden;
        }

        .code-header {
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:8px 12px;
            background:rgba(0,0,0,.03);
            font-size:.75rem;
        }

        .code-header button {
            border:1px solid var(--border);
            background:transparent;
            padding:4px 10px;
            border-radius:6px;
            cursor:pointer;
            font-size:.75rem;
        }

        pre {
            margin:0;
            padding:16px;
            overflow-x:auto;
            font-size:.9rem;
            line-height:1.6;
        }

        code {
            font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;
        }

        /* =====================
           OUTPUT BLOCK
        ===================== */
        .output-box {
            background:#111827;
            color:#e5e7eb;
            border-radius:10px;
            padding:16px;
            margin-top:8px;
            font-family:ui-monospace,monospace;
            font-size:.9rem;
            overflow-x:auto;
        }

        /* =====================
           ANSWERS
        ===================== */
        .answers-title {
            font-size:1.25rem;
            font-weight:600;
            margin-bottom:14px;
        }

        .answer {
            border-top:1px solid var(--border);
            padding-top:18px;
            margin-top:18px;
        }

        footer {
            text-align:center;
            color:var(--muted);
            font-size:.85rem;
            padding:32px 0;
        }

        @media(max-width:640px){
            .question-title{font-size:1.45rem;}
        }
    </style>
</head>
<body>

<div class="container">

    {{-- QUESTION --}}
    <div class="card">

        <h1 class="question-title">{{ $question->title }}</h1>

        <div class="question-meta">
            <span>Category:
                <strong>{{ $question->category->name ?? 'General' }}</strong>
            </span>
            <span>Asked {{ $question->created_at->diffForHumans() }}</span>
            <span class="badge {{ $question->status }}">
                {{ ucfirst($question->status) }}
            </span>
        </div>

        {{-- QUESTION BODY --}}
        <div class="content">
            {!! $question->body !!}
        </div>

    </div>

    {{-- ANSWERS --}}
    <div class="card">
        <div class="answers-title">
            {{ $question->answers->count() }} Answer(s)
        </div>

        @foreach($question->answers as $ans)
            <div class="answer content">

                {!! $ans->content !!}

            </div>
        @endforeach
    </div>

</div>

<footer>
    © {{ date('Y') }} Mindora · Preview Mode
</footer>

<script>
/* =====================
   CODE ENHANCEMENTS
===================== */
document.querySelectorAll('pre').forEach(pre => {

    const wrapper = document.createElement('div');
    wrapper.className = 'code-box';

    const header = document.createElement('div');
    header.className = 'code-header';

    header.innerHTML = `
        <span>Code</span>
        <div>
            <button onclick="copyCode(this)">Copy</button>
            <button onclick="toggleCodeTheme()">Dark</button>
        </div>
    `;

    pre.parentNode.insertBefore(wrapper, pre);
    wrapper.appendChild(header);
    wrapper.appendChild(pre);
});

function toggleCodeTheme() {
    document.body.classList.toggle('dark-code');
}

function copyCode(btn) {
    const code = btn.closest('.code-box').querySelector('pre').innerText;
    navigator.clipboard.writeText(code);
    btn.innerText = 'Copied';
    setTimeout(() => btn.innerText = 'Copy', 1200);
}
</script>

</body>
</html>
