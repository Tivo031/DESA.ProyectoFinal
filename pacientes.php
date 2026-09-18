<?php
include("conexion.php");

// Eliminar paciente si se envía el parametro borrar
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    mysqli_query($conexion, "DELETE FROM pacientes WHERE id_paciente = $id");
    header("Location: pacientes.php");
}

$resultado = mysqli_query($conexion, "SELECT * FROM pacientes");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Pacientes</title>
</head>
<body>
    <h2>Pacientes Registrados</h2>
    <a href="registrar_paciente.php"> + Registrar Nuevo Paciente</a><br><br>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>DPI</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Fecha Nac.</th>
            <th>Sexo</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Acciones</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($resultado)) { ?>
        <tr>
            <td><?php echo $row['id_paciente']; ?></td>
            <td><?php echo $row['dpi']; ?></td>
            <td><?php echo $row['nombres']; ?></td>
            <td><?php echo $row['apellidos']; ?></td>
            <td><?php echo $row['fecha_nacimiento']; ?></td>
            <td><?php echo $row['sexo']; ?></td>
            <td><?php echo $row['telefono']; ?></td>
            <td><?php echo $row['correo']; ?></td>
            <td>
                <a href="pacientes.php?eliminar=<?php echo $row['id_paciente']; ?>" onclick="return confirm('¿Desea eliminar este paciente?')">Eliminar</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>