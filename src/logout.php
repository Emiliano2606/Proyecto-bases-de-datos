<?php
session_start();
session_unset(); // Borra las variables de sesión
session_destroy(); // Destruye la sesión
header("Location: ../es/index.html"); // Te manda de regreso al login (ajusta la ruta)
exit();
?>