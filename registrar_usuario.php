<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Validar que los campos obligatorios del servidor no estén vacíos
    if (empty($_POST['id_rol']) || empty($_POST['nombres']) || empty($_POST['apellidos']) || empty($_POST['usuario']) || empty($_POST['password'])) {
        die("Error: Todos los campos obligatorios deben ser llenados.");
    }

    $id_rol = $_POST['id_rol'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $usuario = $_POST['usuario'];
    $password_plana = $_POST['password'];

    // 2. Encriptación segura (Norma RNF-05)
    $password_encriptada = password_hash($password_plana, PASSWORD_BCRYPT);

    $sql = "INSERT INTO usuarios (id_rol, nombres, apellidos, correo, telefono, usuario, password) 
            VALUES ('$id_rol', '$nombres', '$apellidos', '$correo', '$telefono', '$usuario', '$password_encriptada')";

    if (mysqli_query($conexion, $sql)) {
        echo "Usuario registrado exitosamente en el sistema.";
    } else {
        echo "Error al registrar: " . mysqli_error($conexion);
    }
}
?>
