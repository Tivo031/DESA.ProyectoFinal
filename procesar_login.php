<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['usuario']) || empty($_POST['password'])) {
        die("Error: Todos los campos son obligatorios.");
    }

    $usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $password_plana = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
    $resultado = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($resultado) == 1) {
        $usuario_datos = mysqli_fetch_assoc($resultado);

        if (password_verify($password_plana, $usuario_datos['password'])) {
            $_SESSION['id_usuario'] = $usuario_datos['id_usuario'];
            $_SESSION['usuario'] = $usuario_datos['usuario'];
            $_SESSION['id_rol'] = $usuario_datos['id_rol'];
            $_SESSION['nombres'] = $usuario_datos['nombres'];

            echo "<h2>¡Inicio de sesión correcto! Bienvenido/a, " . $_SESSION['nombres'] . ".</h2>";
            echo "<p>Has ingresado con éxito al sistema de la clínica.</p>";
        } else {
            echo "<h2>Error: Contraseña incorrecta.</h2>";
        }
    } else {
        echo "<h2>Error: El nombre de usuario no existe.</h2>";
    }
}
?>
