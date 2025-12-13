<?php 
require_once '../../config/database.php'; 

header('Content-Type: application/json'); 

try {
  // Validar ID
  if (empty($_POST['id'])) {
      throw new Exception("ID de cita no proporcionado");
  }

  $id = $_POST['id'];
  $idUser = $_POST['idUser'];
  $idDoctor = $_POST['idDoctor'];
  $idSpecialty = $_POST['idSpecialty'];
  $dateAppointment = $_POST['dateAppointment'];
  $idStatus = $_POST['idStatus'];

  $sql = "UPDATE appointment SET 
            idUser = :user, 
            idDoctor = :doctor, 
            idSpecialty = :specialty, 
            dateAppointment = :date,
            idStatus = :status
          WHERE id = :id";

  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':user'      => $idUser,
    ':doctor'    => $idDoctor,
    ':specialty' => $idSpecialty, 
    ':date'      => $dateAppointment,
    ':status'    => $idStatus,
    ':id'        => $id
  ]);

  echo json_encode(['success'=> true, 'message'=>'Cita actualizada correctamente']);

} catch (Exception $e){
  echo json_encode(['success'=> false, 'message' => 'Error: '.$e->getMessage()]);
}
?>