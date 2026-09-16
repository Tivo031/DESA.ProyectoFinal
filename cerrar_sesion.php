<?php
// Iniciar la sesión existente
session_start();

// Borrar todas las variables de la sesión
$_SESSION = array();

// Destruir la sesión por completo en el servidor
session_destroy();

// Redirigir al usuario automáticamente de vuelta al formulario de Login
header("location: login.php");
exit;
?>
