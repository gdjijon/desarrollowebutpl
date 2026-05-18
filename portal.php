<?php
include "config.php";

// Verificar sesión activa
if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit();
}

// Control de inactividad ( luego de 15 minutos)
if(isset($_SESSION['ultimo_acceso'])){
    $inactivo = 900; // 15 minutos
    $tiempo_transcurrido = time() - $_SESSION['ultimo_acceso'];
    if($tiempo_transcurrido > $inactivo){
        session_destroy();
        header("Location: login.php");
        exit();
    }
}
$_SESSION['ultimo_acceso'] = time();

// Obtener datos del usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();


$opcion = isset($_GET['op']) ? $_GET['op'] : 'perfil';


$mensaje = "";

// Actualizar datos (nombre y correo)
if($opcion == 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    if(empty($nombre) || empty($correo)){
        $mensaje = "<div class='error'>Todos los campos son obligatorios.</div>";
    } elseif(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
        $mensaje = "<div class='error'>Correo no válido.</div>";
    } else {
        // Verificar correo único
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ? AND id != ?");
        $stmt->execute([$correo, $usuario['id']]);
        if($stmt->rowCount() > 0){
            $mensaje = "<div class='error'>Correo ya registrado.</div>";
        } else {
            // Actualizar DB
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, correo = ? WHERE id = ?");
            $stmt->execute([$nombre, $correo, $usuario['id']]);
            $mensaje = "<div class='exito'>Datos actualizados correctamente.</div>";
            $usuario['nombre'] = $nombre;
            $usuario['correo'] = $correo;
        }
    }
}

// Cambiar contraseña
if($opcion == 'cambiar' && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $actual = $_POST['actual'];
    $nueva = $_POST['nueva'];
    $confirmar = $_POST['confirmar'];

    if(!password_verify($actual, $usuario['password'])){
        $mensaje = "<div class='error'>Contraseña actual incorrecta.</div>";
    } elseif($nueva !== $confirmar){
        $mensaje = "<div class='error'>Las nuevas contraseñas no coinciden.</div>";
    } elseif(strlen($nueva) < 8 || 
             !preg_match('/[A-Z]/',$nueva) || 
             !preg_match('/[a-z]/',$nueva) || 
             !preg_match('/[0-9]/',$nueva) || 
             !preg_match('/[\W]/',$nueva)){
        $mensaje = "<div class='error'>La nueva contraseña debe tener mínimo 8 caracteres, incluir al menos 1 mayúscula, 1 minúscula, 1 número y 1 símbolo.</div>";
    } else {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $usuario['id']]);
        $mensaje = "<div class='exito'>Contraseña actualizada correctamente.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Portal Privado</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="bg-portal"> <div class="portal-container">
    <div class="portal-menu">
        <button onclick="location.href='portal.php?op=perfil'">Perfil</button>
        <button onclick="location.href='portal.php?op=actualizar'">Actualizar Datos</button>
        <button onclick="location.href='portal.php?op=cambiar'">Actualizar Contraseña</button>
        <button onclick="location.href='logout.php'">Cerrar Sesión</button>
    </div>
    
    <div class="portal-content">
        <?php echo $mensaje; ?>

        <?php if($opcion == 'perfil'): ?>
            <h2>Perfil de Usuario</h2>
            <p><strong>Usuario:</strong> <?php echo $usuario['usuario']; ?></p>
            <p><strong>Nombre completo:</strong> <?php echo $usuario['nombre']; ?></p>
            <p><strong>Correo:</strong> <?php echo $usuario['correo']; ?></p>
            <p><strong>Cédula:</strong> <?php echo $usuario['cedula']; ?></p>
            <p><strong>Fecha de registro:</strong> <?php echo $usuario['fecha_registro']; ?></p>

        <?php elseif($opcion == 'actualizar'): ?>
            <h2>Actualizar Datos</h2>
            <form method="POST">
                <input type="text" name="nombre" value="<?php echo $usuario['nombre']; ?>" placeholder="Nombre completo" required>
                <input type="email" name="correo" value="<?php echo $usuario['correo']; ?>" placeholder="Correo electrónico" required>
                <button type="submit">Guardar Cambios</button>
            </form>

        <?php elseif($opcion == 'cambiar'): ?>
            <h2>Actualizar Contraseña</h2>
            <form method="POST">
                <input type="password" name="actual" placeholder="Contraseña actual" required>
                <input type="password" name="nueva" placeholder="Nueva contraseña" required>
                <input type="password" name="confirmar" placeholder="Confirmar nueva contraseña" required>
                <button type="submit">Actualizar Contraseña</button>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>