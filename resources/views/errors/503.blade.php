@php
    // Eigenständige Seite: kein Vite, keine Session, keine DB – funktioniert auch
    // vorgerendert mit `php artisan down --render="errors::503"`.
    $message = isset($exception) ? trim($exception->getMessage()) : '';
    $retryAfter = isset($exception) && method_exists($exception, 'getHeaders')
        ? ($exception->getHeaders()['Retry-After'] ?? null)
        : null;
    $logoPath = public_path('img/logo.svg');
    $logo = is_file($logoPath) ? 'data:image/svg+xml;base64,'.base64_encode(file_get_contents($logoPath)) : null;
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta http-equiv="refresh" content="{{ is_numeric($retryAfter) ? max(30, (int) $retryAfter) : 60 }}">
    <title>Wartungsarbeiten – {{ config('app.name', 'Zopfaktion') }}</title>
    <style>
        :root {
            --rot: #c41333;
            --blau: #323394;
            --text: #1f2937;
            --gedaempft: #6b7280;
            --hintergrund: #f6f6f8;
            --karte: #ffffff;
            --rand: #e5e7eb;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --text: #f3f4f6;
                --gedaempft: #9ca3af;
                --hintergrund: #111827;
                --karte: #1f2937;
                --rand: #374151;
            }
            .logo { filter: brightness(0) invert(1); }
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            background: var(--hintergrund);
            color: var(--text);
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
        }
        .karte {
            width: 100%;
            max-width: 32rem;
            background: var(--karte);
            border: 1px solid var(--rand);
            border-top: 4px solid var(--rot);
            border-radius: 0.75rem;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        }
        .logo { display: block; width: 100%; max-width: 16rem; height: auto; margin: 0 auto 2rem; }
        .icon { width: 3rem; height: 3rem; margin: 0 auto 1rem; color: var(--blau); }
        @media (prefers-color-scheme: dark) { .icon { color: #8b8cf0; } }
        h1 { font-size: 1.5rem; line-height: 1.3; margin: 0 0 0.75rem; }
        p { margin: 0 0 1rem; color: var(--gedaempft); }
        .hinweis {
            margin: 1.25rem 0;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            background: rgba(50, 51, 148, 0.08);
            color: var(--text);
            text-align: left;
        }
        .button {
            display: inline-block;
            margin-top: 0.5rem;
            padding: 0.625rem 1.25rem;
            border: 0;
            border-radius: 0.5rem;
            background: var(--rot);
            color: #fff;
            font: inherit;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }
        .button:hover { background: #a30f2a; }
        .button:focus-visible { outline: 3px solid var(--blau); outline-offset: 2px; }
        .klein { margin-top: 1.5rem; font-size: 0.875rem; margin-bottom: 0; }
    </style>
</head>
<body>
    <main class="karte">
        @if ($logo)
            <img class="logo" src="{{ $logo }}" alt="{{ config('app.name', 'Zopfaktion') }}">
        @endif

        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
        </svg>

        <h1>Wir sind gleich wieder da</h1>
        <p>Gerade finden Wartungsarbeiten statt. Die Seite ist in Kürze wieder erreichbar – deine Daten bleiben dabei unverändert.</p>

        @if ($message !== '' && $message !== 'Service Unavailable')
            <div class="hinweis" role="status">{{ $message }}</div>
        @endif

        <button type="button" class="button" onclick="window.location.reload()">Erneut versuchen</button>

        <p class="klein">Die Seite lädt sich automatisch neu.</p>
    </main>
</body>
</html>
