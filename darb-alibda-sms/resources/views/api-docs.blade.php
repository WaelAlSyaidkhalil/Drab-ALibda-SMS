<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Darb Al-Ibda SMS — API Docs</title>
    <link rel="icon" href="{{ url('/favicon.ico') }}">
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui.css">
    <style>
        body { margin: 0; background: #fafafa; }
        .topbar { display: none; }
        #docs-header {
            background: #1b1b1f;
            color: #fff;
            padding: 14px 20px;
            font: 600 15px/1.4 -apple-system, BlinkMacSystemFont, "Segoe UI", Tahoma, sans-serif;
        }
        #docs-header small { display: block; font-weight: 400; opacity: .7; margin-top: 3px; }
    </style>
</head>
<body>
    <div id="docs-header">
        نظام مدرسة درب الإبداع الخاصة — توثيق واجهات API
        <small>Spec: <a href="{{ url('/openapi.yaml') }}" style="color:#7fc4ff">/openapi.yaml</a></small>
    </div>

    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui-bundle.js" crossorigin></script>
    <script src="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui-standalone-preset.js" crossorigin></script>
    <script>
        window.onload = function () {
            window.ui = SwaggerUIBundle({
                url: {!! json_encode(url('/openapi.yaml')) !!},
                dom_id: '#swagger-ui',
                deepLinking: true,
                persistAuthorization: true,
                docExpansion: 'none',
                filter: true,
                tryItOutEnabled: true,
                presets: [SwaggerUIBundle.presets.apis, SwaggerUIStandalonePreset],
                plugins: [SwaggerUIBundle.plugins.DownloadUrl],
                layout: 'StandaloneLayout',
            });
        };
    </script>
</body>
</html>
