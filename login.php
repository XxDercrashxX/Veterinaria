<?php
session_start();

$servidor = "localhost";
$usuario_db = "root";
$password_db = "";
$nombre_db = "veterinaria"; //  CAMBIA ESTO


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //conectar
    $conn = new mysqli($servidor, $usuario_db, $password_db, $nombre_db);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // los datos
    $email = $_POST['email'];
    $password_formulario = $_POST['password'];

    // eso es de seguridad
    $stmt = $conn->prepare("SELECT id, email, password_hash FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // verifica el usuario
    if ($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();

        // la contraseña
        if (password_verify($password_formulario, $usuario['password_hash'])) {
            //se guarda
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_email'] = $usuario['email'];

            // y manda al inicio
            header("Location: inicio.php");
            exit;

        } else {
            // Contraseña incorrecta
            header("Location: index.html?error=1"); // Error 1 = pass incorrecta
            exit;
        }
    } else {
        // Usuario no existe
        header("Location: index.html?error=2"); // Error 2 = usuario no existe
        exit;
    }

    $stmt->close();
    $conn->close();

} else {
    // Si intentan entrar directo al .php
    header("Location: index.html");
    exit;
}
?>