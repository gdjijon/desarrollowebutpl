<?php
include "config.php";

// Inicializar errores y mensaje de captcha
$errores = [];
$mensaje_captcha = "";

// GET (primera carga o recarga)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $captcha_text = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
    $_SESSION['captcha_text'] = $captcha_text;

    // Reiniciar mensajes GET
    $errores = [];
    $mensaje_captcha = "";
} else {
    // POST → usar captcha en sesión
    if(isset($_SESSION['captcha_text'])){
        $captcha_text = $_SESSION['captcha_text'];
    } else {
        
        $captcha_text = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
        $_SESSION['captcha_text'] = $captcha_text;
    }
}

// Procesar formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $usuario_input = trim($_POST['usuario']);
    $password = $_POST['password'];
    $captcha = trim($_POST['captcha']);

    if(empty($usuario_input) || empty($password) || empty($captcha)){
        $errores[] = "Todos los campos son obligatorios.";
    } else {
        // Buscar usuario 
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario_input]);
        $usuarioDB = $stmt->fetch();

        if(!$usuarioDB){
            $errores[] = "Usuario o contraseña incorrectos.";
        } else {
            // Validar captcha
            if($captcha != $_SESSION['captcha_text']){
                $mensaje_captcha = "Captcha incorrecto.";
            } 
            // Verificar contraseña si captcha es correcto
            elseif(!password_verify($password, $usuarioDB['password'])){
                $errores[] = "Usuario o contraseña incorrectos.";
                // Incrementar intentos fallidos
                $stmt = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE id = ?");
                $stmt->execute([$usuarioDB['id']]);
            } else {
                // Login exitoso
                $_SESSION['usuario_id'] = $usuarioDB['id'];
                $_SESSION['usuario_nombre'] = $usuarioDB['nombre'];
                $stmt = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = 0, ultimo_acceso = NOW() WHERE id = ?");
                $stmt->execute([$usuarioDB['id']]);
                header("Location: portal.php");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="bg-login">
<div class="form-container">
    <h2>Iniciar Sesión</h2>

    <?php
    // Mostrar error de usuario/contraseña
    if(!empty($errores)){
        foreach($errores as $e){
            echo "<div class='error'>$e</div>";
        }
    }

    // Mostrar mensaje captcha 
    if($mensaje_captcha != ""){
        echo "<div class='error'>$mensaje_captcha</div>";
    }
    ?>

    <form method="POST" action="">
        <input type="text" name="usuario" placeholder="Usuario (solo letras minúsculas)" required>
        <input type="password" name="password" placeholder="Contraseña" required>

        <div class="captcha"><?php echo $captcha_text; ?></div>
        <input type="text" name="captcha" placeholder="Ingresa el captcha" required>

        <button type="submit">Iniciar Sesión</button>
    </form>
    <p>¿No tienes cuenta? <a href="nuevo_usuario.php">Crear usuario</a></p>
</div>
</body>
</html>