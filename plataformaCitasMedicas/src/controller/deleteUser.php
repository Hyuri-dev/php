<?php
/**
 * Elimina un usuario de la base de datos.
 *
 * @param PDO $db La conexión a la base de datos
 * @param int $id El ID del usuario a eliminar
 * @return bool 
 */
function eliminarUsuario($db, $id): bool {
    
    if (empty($id)) {
        return false;
    }

    try {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        // Retorna true si se eliminó al menos una fila
        return $stmt->rowCount() > 0;

    } catch (PDOException $e) {
        return false;
    }
}
?>