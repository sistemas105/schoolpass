<?php
$name  = $model1['name']  ?? '';
$photo = $model1['photo'] ?? '';

// Convertir ruta física a URL pública
$photoUrl = '';
if (!empty($photo)) {
    $photoUrl = URL . str_replace(
        $_SERVER['DOCUMENT_ROOT'],
        '',
        $photo
    );
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso autorizado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .card {
            display: inline-block;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,.15);
            margin-top: 40px;
        }
        img {
            width: 150px;
            border-radius: 12px;
            margin-top: 15px;
        }
        .ok {
            color: green;
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="ok">✔ Acceso autorizado</div>

    <h2><?= htmlspecialchars($name) ?></h2>

    <?php if (!empty($photoUrl)): ?>
        <img src="<?= htmlspecialchars($photoUrl) ?>" alt="Foto del contacto">
    <?php else: ?>
        <p><em>Sin fotografía</em></p>
    <?php endif; ?>
</div>

</body>
</html>
