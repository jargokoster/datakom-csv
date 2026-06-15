<!doctype html>
<html>
<head>
    <title>Datakom CSV Processor API Docs</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist/swagger-ui.css">
</head>
<body>
    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist/swagger-ui-bundle.js"></script>

    <script>
        window.onload = function() {
            SwaggerUIBundle({
                url: "/api/openapi.json"
                , dom_id: "#swagger-ui"
                , persistAuthorization: true
            });
        };

    </script>
</body>
</html>
