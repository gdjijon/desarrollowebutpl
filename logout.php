<?php
include "config.php";

// Destruir la sesión
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cerrar Sesión</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="bg-portal"> <div class="form-container">
    <h2>Cerrar Sesión</h2>
    <div class="exito">
        Has cerrado sesión correctamente.
    </div>
    <button onclick="location.href='login.php'">Volver al Inicio</button>
</div>
</body>
</html>