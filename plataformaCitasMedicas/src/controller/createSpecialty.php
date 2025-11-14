<?php 
/**
 * @param PDO
 * @param string $specialty
 * @return string
 */

 function crearEspecialidad($db, $name): string{
    if(empty($name)){
        return "Error: Los campos son obligatorios";
    }

    try {
        $stmt = $db -> prepare("INSERT INTO specialty(nombre) VALUES(?)");
        $success = $stmt->execute([$name]);

        if ($success){
            return "Especialidad creada correctamente";
        } else {
            return "Error al crear la especialidad";
        }
    } catch (PDOException $e) {
        return $e->getMessage();
    }
 }
?>