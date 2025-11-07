<?php
require_once '../config/database.php';
require_once '../src/controller/createUser.php';


$message = '';


if (isset($_POST['save'])) {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 4. ¡LLAMAR A LA FUNCIÓN!
    // Le pasamos la conexión ($conn) y los datos del formulario
    $message = crearUsuario($conn, $username, $password);

    // Opcional: Limpiar el POST si fue exitoso
    if ($message === "Usuario creado exitosamente") {
        $_POST = [];
    }
}



?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de usuarios</title>
</head>


<body>
  <div class="container">
    <h2>Gestión de Usuarios</h2>
    <?php  if ($message) ?>   <!-- Aqui va el mensaje de estado  -->

    <form method= "POST" id="formUsers" action="">
      <input type="text" name="username" id="username" placeholder="Usuario" required> <br><br>
      <input type="password" name="password" id="password" placeholder="Contraseña" required>
      <br><br>
      <button type="submit" name="save">Guardar</button>
      <button type="button">Limpiar</button>


  </form>


  </div>

</body>
</html>