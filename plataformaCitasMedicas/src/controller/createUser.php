<?php
// Este archivo solo contendrá funciones
/**
 * Crea un nuevo usuario en la base de datos.
 *
 * @param PDO $db La conexión a la base de datos (viene de database.php)
 * @param string $username El nombre de usuario a registrar
 * @param string $password La contraseña en texto plano
 * @return string Un mensaje de éxito o error
 */
function crearUsuario($db, $username, $password): string {


  if (empty($username) || empty($password)) {
    return "Error: El usuario y la contraseña son obligatorios";
  }


  // 3. Intentar insertar en la base de datos
  try {
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $success = $stmt->execute([$username, $password]);

    if ($success) {
      return "Usuario creado exitosamente";
    } else {
      return "Error al crear el usuario";
    }

  } catch (PDOException $e) {
    return $e->getMessage();
  }
}

?>