<?php 
require_once '../../config/database.php';

header('Content-Type: application/json'); 


try {
  $idUser = $_POST['idUser'];
  $idDoctor = $_POST['idDoctor'];
  $idSpecialty = $_POST['idSpecialty'];
  $dateAppointment = $_POST['dateAppointment'];
  $idStatus = 3;

  $sql = "INSERT INTO appointment (idUser , idDoctor, idSpecialty, dateAppointment, idStatus)
  VALUES (:user, :doctor, :specialty, :date, :status)";

  $stmt = $conn -> prepare($sql);
  $stmt->execute([
    ':user'=>$idUser,
    ':doctor'=>$idDoctor,
    ':specialty'=>$idSpecialty,
    ':date'=>$dateAppointment,
    ':status'=>$idStatus
  ]);

  echo json_encode(['success'=> true, 'message'=>'Cita agendada correctamente']);
} catch (Exception $e){
  echo json_encode(['success'=> false, 'message' => 'Error'.$e->getMessage()]);
}
?>