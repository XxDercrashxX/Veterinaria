<?php
// Base de Datos Config 
$servidor = "localhost";
$usuario_db = "root";
$password_db = "";
$nombre_db = "mi_proyecto_fintech";

// 1. Comprobar que los datos llegan por POST 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Conectar a la BD 
    $conn = new mysqli($servidor, $usuario_db, $password_db, $nombre_db);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // 3. Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $password_formulario = $_POST['password'];

    // 4. Hashear la contraseña
    $hash_para_guardar = password_hash($password_formulario, PASSWORD_DEFAULT);

    // 5. Preparar consulta
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, password_hash) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $apellido, $email, $hash_para_guardar);

    // 6. Ejecutar y verificar
    if ($stmt->execute()) {
        
        // REGISTRO CORRECTO
        // Redirigir al login (index.html) para que ahora inicie sesión
        header("Location: index.html?registro=exitoso");
        exit;

    } else {
        // Error
        if ($conn->errno == 1062) { // 1062 = Error de email duplicado
            header("Location: registro.html?error=email_duplicado");
            exit;
        } else {
            // Otro error
            header("Location: registro.html?error=desconocido");
            exit;
        }
    }

    $stmt->close();
    $conn->close();

} else {
    // Si intentan entrar directo al .php (CAMBIADO - debe ir a registro.html)
    header("Location: registro.html");
    exit;
}
?>