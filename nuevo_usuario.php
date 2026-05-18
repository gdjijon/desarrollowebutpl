<?php
include "config.php";

$errores = [];

// Validación de contraseña segura
function validarPassword($password) {
    return preg_match('/[A-Z]/', $password) &&  // requisito 1 mayúscula
           preg_match('/[a-z]/', $password) &&  // requisito 1 minúscula
           preg_match('/[0-9]/', $password) &&  // requisito 1 número
           preg_match('/[\%\+\-\&]/', $password) &&   // requisito 1 símbolo permitido: %, +, -, &
           strlen($password) >= 8;              // requisito 8 caracteres mínimo
}

// Procesar formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $cedula = trim($_POST['cedula']);
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];
    $confirmar = $_POST['confirmar'];
    $captcha = trim($_POST['captcha']);

    // Validaciones
    if(empty($cedula) || empty($nombre) || empty($usuario) || empty($correo) || empty($password) || empty($confirmar) || empty($captcha)){
        $errores[] = "Todos los campos son obligatorios.";
    }

    // Validación estricta de Cédula: Solo números y exactamente 10 dígitos
    if(!preg_match('/^[0-9]{10}$/', $cedula) && !empty($cedula)){
        $errores[] = "La cédula debe ser exclusivamente numérica y tener exactamente 10 dígitos.";
    }

    if(!preg_match('/^[a-z]+$/', $usuario) && !empty($usuario)){
        $errores[] = "Usuario (nickname) solo puede contener letras minúsculas.";
    }

    if(!filter_var($correo, FILTER_VALIDATE_EMAIL) && !empty($correo)){
        $errores[] = "Correo no tiene formato válido.";
    }

    if($password !== $confirmar && !empty($password)){
        $errores[] = "Las contraseñas no coinciden.";
    }

    if(!validarPassword($password) && !empty($password)){
        $errores[] = "La contraseña debe tener mínimo 8 caracteres, incluir al menos 1 mayúscula, 1 minúscula, 1 número y 1 símbolo permitido: %, +, -, &.";
    }

    // Verificar usuario y correo únicos
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ? OR usuario = ?");
    $stmt->execute([$correo, $usuario]);
    if($stmt->rowCount() > 0){
        $errores[] = "El correo o el usuario ya está registrado.";
    }

    // Captcha
    if($captcha != $_SESSION['captcha_text']){
        $errores[] = "Captcha incorrecto.";
    }

    // Insertar usuario si no hay errores
    if(empty($errores)){
        $passHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (cedula, nombre, usuario, correo, password) VALUES (?, ?, ?, ?, ?)");
        if($stmt->execute([$cedula, $nombre, $usuario, $correo, $passHash])){
            // Redirigir a la página de confirmación
            header("Location: usuario_creado.php");
            exit();
        } else {
            $errores[] = "Error al crear usuario. Intente nuevamente.";
        }
    }
}

// Generar captcha
$captcha_text = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
$_SESSION['captcha_text'] = $captcha_text;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="bg-login">
<div class="form-container">
    <h2>Crear Usuario</h2>

    <?php
    if(!empty($errores)){
        foreach($errores as $e){
            echo "<div class='error'>$e</div>";
        }
    }
    ?>

    <form method="POST" action="">
        <input type="text" name="cedula" placeholder="Ingresar Cédula (10 dígitos)" required maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        <input type="text" name="nombre" placeholder="Ingresar Nombres completos" required>
        <input type="text" name="usuario" placeholder="Usuario (solo letras minúsculas)" required>
        <input type="email" name="correo" placeholder="Correo electrónico" required>
        
        <input type="password" name="password" placeholder="Contraseña" required>
        <div style="font-size:12px; color:#444; margin-bottom:12px; text-align:left; font-weight: 500; line-height: 1.4;">
            Requisito: Mínimo 8 caracteres, 1 mayúscula, 1 minúscula, 1 número y 1 símbolo registrado (%, +, -, &).
        </div>

        <input type="password" name="confirmar" placeholder="Confirmar contraseña" required>

        <div class="captcha"><?php echo $captcha_text; ?></div>
        <input type="text" name="captcha" placeholder="Ingresa el captcha" required>

        <button type="submit">Registrarse</button>
    </form>
    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
</div>
</body>
</html>