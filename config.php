<?php
session_start();

// Configuración de conexión
$host = "localhost";
$db   = "proyectoutpl";  // Base de datos
$user = "root";          // Usuario 
$pass = "";              // Contraseña 
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo "Error al conectar con la base de datos: " . $e->getMessage();
    exit();
}

// Función verificar sesión activa
function verificarSesion() {
    if(!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit();
    }
}
?>