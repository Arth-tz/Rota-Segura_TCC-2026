<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rota não encontrada — Rota Segura</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Sora', system-ui, sans-serif; }

        /* Road stretching to vanishing point */
        .road-wrap {
            perspective: 600px;
            perspective-origin: 50% 0%;
        }
        .road {
            width: 180px;
            height: 220px;
            margin: 0 auto;
            transform: rotateX(52deg);
            transform-origin: top center;
            position: relative;
            background: #1e293b;
            border-left: 3px solid #334155;
            border-right: 3px solid #334155;
        }
        .road::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 3px;
            margin-left: -1.5px;
            background: repeating-linear-gradient(
                to bottom,
                #f59e0b 0px,
                #f59e0b 22px,
                transparent 22px,
                transparent 40px
            );
            animation: dash 0.8s linear infinite;
        }
        @keyframes dash {
            from { background-position: 0 0; }
            to   { background-position: 0 -40px; }
        }

        /* Van driving up the road */
        .van {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            animation: drive 3s ease-in-out infinite alternate;
        }
        @keyframes drive {
            from { bottom: 10px; }
            to   { bottom: 140px; transform: translateX(-50%) scale(0.45); opacity: 0.6; }
        }

        @media (prefers-reduced-motion: reduce) {
            .road::before { animation: none; }
            .van { animation: none; }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 flex flex-col items-center justify-center px-6 py-12 select-none">

    <!-- Logo -->
    <a href="/" class="flex items-center gap-2 mb-10 opacity-80 hover:opacity-100 transition">
        <svg class="w-7 h-7 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="7" width="16" height="11" rx="2"/>
            <path d="M17 11h3l3 4v2h-6V11z"/>
            <circle cx="5.5" cy="18.5" r="1.5"/>
            <circle cx="13.5" cy="18.5" r="1.5"/>
            <circle cx="20.5" cy="18.5" r="1.5"/>
        </svg>
        <span class="text-amber-400 font-bold text-lg tracking-tight">Rota Segura</span>
    </a>

    <!-- Road animation -->
    <div class="road-wrap mb-6" aria-hidden="true">
        <div class="road">
            <!-- Van SVG -->
            <div class="van">
                <svg width="52" height="28" viewBox="0 0 52 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="1" y="5" width="34" height="18" rx="3" fill="#f59e0b"/>
                    <path d="M35 9h8l6 8v4H35V9z" fill="#fbbf24"/>
                    <rect x="4" y="8" width="9" height="7" rx="1.5" fill="#1e293b" opacity=".6"/>
                    <rect x="16" y="8" width="9" height="7" rx="1.5" fill="#1e293b" opacity=".6"/>
                    <rect x="38" y="12" width="7" height="5" rx="1" fill="#1e293b" opacity=".6"/>
                    <circle cx="10" cy="24" r="3.5" fill="#334155" stroke="#64748b" stroke-width="1.2"/>
                    <circle cx="10" cy="24" r="1.5" fill="#94a3b8"/>
                    <circle cx="30" cy="24" r="3.5" fill="#334155" stroke="#64748b" stroke-width="1.2"/>
                    <circle cx="30" cy="24" r="1.5" fill="#94a3b8"/>
                    <circle cx="44" cy="24" r="3.5" fill="#334155" stroke="#64748b" stroke-width="1.2"/>
                    <circle cx="44" cy="24" r="1.5" fill="#94a3b8"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- 404 -->
    <p class="text-[6.5rem] leading-none font-extrabold text-amber-500 tracking-tight" style="text-shadow: 0 0 60px rgba(245,158,11,0.25);">
        404
    </p>

    <!-- Message -->
    <h1 class="text-2xl font-bold text-white mt-2 mb-3 text-center">
        Essa rota não existe
    </h1>
    <p class="text-slate-400 text-sm leading-relaxed text-center max-w-xs mb-8">
        A página que você procurou não foi encontrada. Pode ter sido removida, ou o endereço está incorreto.
    </p>

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row gap-3 w-full max-w-xs">
        <a href="/"
            class="flex-1 flex items-center justify-center rounded-xl bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white font-semibold text-sm py-3 px-5 transition-colors">
            Ir para o início
        </a>
        <button onclick="history.back()"
            class="flex-1 flex items-center justify-center rounded-xl border border-slate-700 hover:border-slate-500 text-slate-300 hover:text-white font-semibold text-sm py-3 px-5 transition-colors">
            Voltar
        </button>
    </div>

    <!-- Subtle footer note -->
    <p class="text-slate-700 text-xs mt-10">Código de erro: 404</p>
</body>
</html>
