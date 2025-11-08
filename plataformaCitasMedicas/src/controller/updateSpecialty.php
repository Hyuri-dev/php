<?php 

/**
 * @param PDO
 * @param int
 * @param string
 * @return string
 */

 function actualizarEspecialidad($db, $id, $name): string {
    if(empty($id)||($name)){
        return "Error: Los campos son obligatorios";
    }

    try {
        $sql = "UPDATE specialty SET
        name = ?
        WHERE id =?";

        $params = [$name];

        $stmt = $db ->prepare($sql);
        $stmt->execute($params);

        if($stmt->rowCount()>0){
            return "Especialidad actualizado exitosamente";
        } else{
            return "No se realizaron cambios (o la especialidad no fue encontrada)";
        };
    } catch (PDOException $e) {
        return $e->getMessage();
    }
 }

?>