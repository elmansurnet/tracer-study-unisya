<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Tracer Study UNISYA</title>
    <meta name="description" content="Sistem Informasi Tracer Study Universitas Islam Syarifuddin Lumajang" />

    <!-- Preconnect fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300..800&family=Inter:wght@300..700&display=swap" rel="stylesheet" />

    @vite(['resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
</body>
</html>