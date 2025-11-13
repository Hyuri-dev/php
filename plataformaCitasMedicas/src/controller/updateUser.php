<?php
/**
 * Actualiza un usuario existente en la base de datos.
 *
 * @param PDO $db  conexión a la base de datos
 * @param int $id El ID del usuario a actualizar
 * @param string $username
 * @param string $password La contraseña 
 * @param string $name
 * @param string $lastname
 * @param string $birthday
 * @param string $city
 * @param string $typeUser
 * * @return string 
 */
function actualizarUsuario($db, $id, $username, $password, $name, $lastname, $birthday, $city, $typeUser): string {

  if (empty($id) || empty($username) || empty($name) || empty($lastname) || empty($birthday) || empty($city) || empty($typeUser)) {
    return "Error: Todos los campos (excepto la contraseña) son obligatorios";
  }

  try {
    
    // Lógica para la contraseña:
    if (!empty($password)) {
        

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE users SET 
                  username = ?, 
                  password = ?, 
                  name = ?, 
                  lastname = ?, 
                  birthdate = ?, 
                  idCity = ?, 
                  idTypeUser = ? 
                WHERE id = ?";
        
        $params = [$username, $hashed_password, $name, $lastname, $birthday, $city, $typeUser, $id];

    } else {

        $sql = "UPDATE users SET 
                  username = ?, 
                  name = ?, 
                  lastname = ?, 
                  birthdate = ?, 
                  idCity = ?, 
                  idTypeUser = ? 
                WHERE id = ?";
        
        $params = [$username, $name, $lastname, $birthday, $city, $typeUser, $id];
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
      return "Usuario actualizado exitosamente";
    } else {
      return "No se realizaron cambios (o el usuario no fue encontrado)";
    }

  } catch (PDOException $e) {
    return $e->getMessage();
  }
}
?>