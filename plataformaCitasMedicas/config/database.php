<?php
try {

  // Cambiar la contraseña a vacio si se tiene el usuario del mariadb o mysql con contraseña
  $conn = new PDO('mysql:host=localhost;dbname=appointment_platform;charset=utf8', 'root', '');
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Error conexión: " . $e->getMessage());
}																
?>