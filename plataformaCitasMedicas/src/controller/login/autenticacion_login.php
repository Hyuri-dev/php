<?php
session_start();

if ($_SERVER['REQUEST_METHOD']=="POST") {
  require_once "../../../config/database.php";

  try {
      $user_form = $_POST["usuario"];
      $password_form = $_POST["contraseña"];

      $sql = "SELECT id, username, password FROM users WHERE username = :usuario LIMIT 1";

      $stmt = $conn->prepare($sql);

      $stmt->execute([':usuario' => $user_form]);

      $user_db = $stmt->fetch(PDO::FETCH_ASSOC);

      if($user_db) {

        if($password_form === $user_db["password"]){
          $_SESSION['user_id'] = $user_db['id'];
          $_SESSION['username'] = $user_db['username'];
          $_SESSION['logueado'] = true;

          header('location: ../../../public/index.php');
          exit;

        } else {
          $_SESSION['error_login'] = "Contraseña incorrecta, intentelo de nuevo";
          header("Location: ../../public/login.php");
          exit;
      }

      } else{
      $_SESSION["error_login"] = "Usuario no encontrado";
      header("Location: ../../public/login.php");
      exit;
      }
    //code...
  } catch (PDOException $e) {
    $_SESSION['error_login'] = "Error en la base de datos: " . $e->getMessage();
    header("Location: ../../public/login.php");
    exit;
  } finally {
    $conn = null;
  }


} else {
  header("Location: ../../../public/login.php");
  exit;
 
}
?>