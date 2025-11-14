<?php 
/**
 * Elimina una especialidad de la base de datos
 * @param PDO $db Conecion a la base de datos
 * @param int $id ID dela especialidad
 * @return bool
 */

function eliminarEspecialidad ($db , $id):bool {
 
  if (empty($id)){
    return false;
  }
  
  try {
    $stmt = $db->prepare("DELETE FROM specialty WHERE id = ?");
    $stmt -> execute([$id]);
    return $stmt->rowCount()> 0 ;
  } catch (PDOException $e) {
    return false;
  }
}


?>