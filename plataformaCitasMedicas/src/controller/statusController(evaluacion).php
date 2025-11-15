<?php 
/**
 * @param PDO
 * @param string $status_name
 * @return string
 */



//CRUD CREADO PARA LA EVALUACION
 function crearEstatus($db, $status_name): string{
    if(empty($status_name)){
        return "Error: Los campos son obligatorios";
    }

    try {
        $stmt = $db -> prepare("INSERT INTO status(name) VALUES(?)");
        $success = $stmt->execute([$status_name]);

        if ($success){
            return "Estatus de la cita creado correctamente";
        } else {
            return "Error al crear el estatus de la cita";
        }
    } catch (PDOException $e) {
        return $e->getMessage();
    }
 }


 function eliminarEstatus ($db , $id):bool {
 
  if (empty($id)){
    return false;
  }
  
  try {
    $stmt = $db->prepare("DELETE FROM status WHERE id = ?");
    $stmt -> execute([$id]);
    return $stmt->rowCount()> 0 ;
  } catch (PDOException $e) {
    return false;
  }
}

 function actualizarEstatus($db, $id, $status_name): string {
    if(empty($id)&&($status_name)){
        return "Error: Los campos son obligatorios";
    }

    try {
        $sql = "UPDATE status SET
        name = ?
        WHERE id =?";

        $params = [$status_name, $id];

        $stmt = $db ->prepare($sql);
        $stmt->execute($params);

        if($stmt->rowCount()>0){
            return "Estatus actualizado exitosamente";
        } else{
            return "No se realizaron cambios (o el estatus no fue encontrado)";
        };
    } catch (PDOException $e) {
        return $e->getMessage();
    }
 }


?>

