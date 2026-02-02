<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Puri Digital</title>

    {{-- SEO & Transparency --}}
    <meta name="description" content="Official website of Puri Digital. This page is temporarily in maintenance mode for security improvements.">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">

    {{-- Security --}}
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <meta http-equiv="Referrer-Policy" content="no-referrer">

    {{-- No JS, No External Assets --}}
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f9fafb;
            color: #1f2937;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            text-align: center;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 12px;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 16px;
        }
        .badge {
            display: inline-block;
            background: #e5f0ff;
            color: #1d4ed8;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 14px;
            margin-bottom: 20px;
        }
        footer {
            margin-top: 24px;
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <main class="container">
        <div class="badge">Maintenance Mode</div>

        <h1>Welcome to Puri Digital</h1>

        <p>
            This website is currently undergoing maintenance and security improvements.
        </p>

        <p>
            No user interaction, data collection, or authentication is available at this time.
        </p>

        <p>
            Please check back later.
        </p>

        <footer>
            © {{ date('Y') }} Puri Digital
        </footer>
    </main>
</body>
</html>
