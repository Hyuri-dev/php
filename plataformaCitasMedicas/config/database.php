<?php
try {
  $conn = new PDO('mysql:host=localhost;dbname=hospital_db;charset=utf8', 'root', '');
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Error conexión: " . $e->getMessage());
}																
?>