<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dpi = $_POST['dpi'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $sexo = $_POST['sexo'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $id_usuario = 2; // ID de usuario por defecto para el registro

    $sql = "INSERT INTO pacientes (id_usuario_registro, dpi, nombres, apellidos, fecha_nacimiento, sexo, telefono, correo) 
            VALUES ('$id_usuario', '$dpi', '$nombres', '$apellidos', '$fecha_nacimiento', '$sexo', '$telefono', '$correo')";

    if (mysqli_query($conexion, $sql)) {
        header("Location: pacientes.php");
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Paciente</title>
</head>
<body>
    <h2>Nuevo Paciente</h2>
    <form method="POST">
        <label>DPI:</label><br><input type="text" name="dpi" required><br>
        <label>Nombres:</label><br><input type="text" name="nombres" required><br>
        <label>Apellidos:</label><br><input type="text" name="apellidos" required><br>
        <label>Fecha Nacimiento:</label><br><input type="date" name="fecha_nacimiento" required><br>
        <label>Sexo:</label><br>
        <select name="sexo" required>
            <option value="MASCULINO">MASCULINO</option>
            <option value="FEMENINO">FEMENINO</option>
        </select><br>
        <label>Teléfono:</label><br><input type="text" name="telefono"><br>
        <label>Correo:</label><br><input type="email" name="correo"><br><br>
        <button type="submit">Guardar Paciente</button>
    </form>
</body>
</html>