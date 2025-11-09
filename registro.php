<?php

$servidor = "localhost";
$usuario_db = "root";
$password_db = "";
$nombre_db = "veterinaria";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $conn = new mysqli($servidor, $usuario_db, $password_db, $nombre_db);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $password_formulario = $_POST['password'];

    // hashear la contraseña
    $hash_para_guardar = password_hash($password_formulario, PASSWORD_DEFAULT);

    // prepara la consulta
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, password_hash) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $apellido, $email, $hash_para_guardar);

    if ($stmt->execute()) {
        
        header("Location: index.html?registro=exitoso");
        exit;

    } else {

        if ($conn->errno == 1062) { // email duplicado
            header("Location: registro.html?error=email_duplicado");
            exit;
        } else {

            header("Location: registro.html?error=desconocido");
            exit;
        }
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: registro.html");
    exit;
}
?>