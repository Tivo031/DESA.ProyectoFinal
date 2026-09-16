<form action="registrar_usuario.php" method="POST">
    <label>Nombres:</label>
    <input type="text" name="nombres" required>

    <label>Apellidos:</label>
    <input type="text" name="apellidos" required>

    <label>Correo Electrónico:</label>
    <input type="email" name="correo" required>

    <label>Teléfono:</label>
    <input type="text" name="telefono">

    <label>Nombre de Usuario:</label>
    <input type="text" name="usuario" required>

    <label>Contraseña:</label>
    <input type="password" name="password" required>

    <label>Rol del Usuario:</label>
    <select name="id_rol" required>
        <option value="1">Administrador</option>
        <option value="2">Recepcionista</option>
        <option value="3">Especialista</option>
        <option value="4">Encargado de Inventario</option>
    </select>

    <button type="submit">Registrar Usuario</button>
</form>
