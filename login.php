<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - BAJ WAM</title>
</head>
<body>
    <h2>BAJ WAM - Control de Acceso</h2>
    
    <form action="procesar_login.php" method="POST">
        <div>
            <label>Nombre de Usuario:</label>
            <input type="text" name="usuario" required>
        </div>
        <br>
        <div>
            <label>Contraseña:</label>
            <input type="password" name="password" required>
        </div>
        <br>
        <button type="submit">Ingresar al Sistema</button>
    </form>
</body>
</html>
